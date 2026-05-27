<?php

session_start();

include("config/db.php");


// LOGIN CHECK

if(!isset($_SESSION['user_id'])){

    header("Location: login.php");
}


$user_id = $_SESSION['user_id'];


// USER DATA

$user = $conn->query(

"SELECT * FROM users WHERE id=$user_id"

)->fetch_assoc();


// INVESTMENTS WITH STOCK SYMBOL

$investments = $conn->query(

"SELECT investments.*,

companies.stock_symbol

FROM investments

JOIN companies

ON investments.company_id = companies.id

WHERE user_id=$user_id

ORDER BY investments.id DESC"

);

?>

<!DOCTYPE html>

<html>

<head>

    <title>User Dashboard</title>

    <link rel="stylesheet" href="style.css?v=2">

</head>

<body style="background:#f3f4f6; font-family:Arial;">

<div style="background:linear-gradient(to right,#4338ca,#6366f1); padding:20px; color:white; font-size:28px; font-weight:bold;">

    User Dashboard

</div>

<div style="padding:30px;">

    <!-- USER CARD -->

    <div style="background:white; padding:30px; border-radius:16px; margin-bottom:30px;">

        <h2>

            Welcome,
            <?php echo $user['name']; ?>

        </h2>
        <?php

if($user['role'] == 'admin'){

?>

<a href="admin.php">

    <button style="
    padding:12px 20px;
    background:#4338ca;
    color:white;
    border:none;
    border-radius:10px;
    margin-top:15px;
    cursor:pointer;">

        Admin Dashboard

    </button>

</a>

<?php } ?>

        <h3>

            Remaining Points:

            <span style="color:#2563eb;">

                <?php echo $user['points']; ?>

            </span>

        </h3>

    </div>


    <!-- INVESTMENT TABLE -->

    <div style="background:white; padding:30px; border-radius:16px;">

        <h2 style="margin-bottom:20px;">

    Your Investments

</h2>

<table
border="1"
cellpadding="14"
width="100%"
style="border-collapse:collapse; text-align:center;">

    <tr style="background:#e5e7eb;">

        <th>ID</th>

        <th>Company</th>

        <th>Invested Points</th>

        <th>Buy Price</th>

        <th>Current Price</th>

        <th>Quantity</th>

        <th>Profit / Loss</th>

        <th>Date</th>

        <th>Action</th>

    </tr>

    <?php

    while($row = $investments->fetch_assoc()){


        // STOCK SYMBOL

        $symbol = $row['stock_symbol'];


        // API KEY

        $apiKey = "YOUR_API_KEY";


        // API URL

        $url = "https://www.alphavantage.co/query?function=GLOBAL_QUOTE&symbol=$symbol&apikey=$apiKey";


        // FETCH DATA

        $response = @file_get_contents($url);

        $data = json_decode($response,true);


        // CURRENT PRICE

        if(isset($data['Global Quote']['05. price'])){

            $current_price = $data['Global Quote']['05. price'];

        }else{

            $current_price = $row['stock_price'];
        }


        // CALCULATIONS

        $current_value = $current_price * $row['quantity'];

        $buy_value = $row['stock_price'] * $row['quantity'];

        $profit_loss = $current_value - $buy_value;

    ?>

    <tr>

        <td>

            <?php echo $row['id']; ?>

        </td>

        <td>

            <?php echo $row['company_name']; ?>

        </td>

        <td>

            ₹<?php echo $row['invested_points']; ?>

        </td>

        <td>

            ₹<?php echo round($row['stock_price'],2); ?>

        </td>

        <td>

            ₹<?php echo round($current_price,2); ?>

        </td>

        <td>

            <?php echo round($row['quantity'],2); ?>

        </td>

        <td>

            <?php

            if($profit_loss > 0){

                echo "<span style='color:green; font-weight:bold;'>

                +₹".round($profit_loss,2)."

                </span>";

            }elseif($profit_loss < 0){

                echo "<span style='color:red; font-weight:bold;'>

                ₹".round($profit_loss,2)."

                </span>";

            }else{

                echo "<span style='color:#2563eb; font-weight:bold;'>

                ₹0

                </span>";
            }

            ?>

        </td>

        <td>

            <?php echo $row['investment_date']; ?>

        </td>

        <td>

            <form action="sell.php" method="POST">

                <input
                type="hidden"
                name="investment_id"
                value="<?php echo $row['id']; ?>">

                <button type="submit">

                    Sell

                </button>

            </form>

        </td>

    </tr>

    <?php } ?>

</table>

            <?php

            while($row = $investments->fetch_assoc()){


                // API SYMBOL

                $symbol = $row['stock_symbol'];


                // API KEY

                $apiKey = "";


                // API URL

                $url = "https://www.alphavantage.co/query?function=GLOBAL_QUOTE&symbol=$symbol&apikey=$apiKey";


                // API RESPONSE

                $response = file_get_contents($url);

                $data = json_decode($response,true);


                // CURRENT PRICE

               $symbol = $row['stock_symbol'];

$apiKey = "";

$url = "https://www.alphavantage.co/query?function=GLOBAL_QUOTE&symbol=$symbol&apikey=$apiKey";

$response = @file_get_contents($url);

$data = json_decode($response, true);


if(isset($data['Global Quote']['05. price'])){

    $current_price = $data['Global Quote']['05. price'];

}else{

    $current_price = $row['stock_price'];
}


                // CALCULATIONS

                $current_value = $current_price * $row['quantity'];

                $buy_value = $row['stock_price'] * $row['quantity'];

$profit_loss = $current_value - $buy_value;
            ?>

            <tr>

                <td>

                    <?php echo $row['id']; ?>

                </td>

                <td>

                    <?php echo $row['company_name']; ?>

                </td>

                <td>

                    ₹<?php echo $row['invested_points']; ?>

                </td>

                <td>

                    ₹<?php echo round($row['stock_price'],2); ?>

                </td>

                <td>

                    ₹<?php echo round($current_price,2); ?>

                </td>

                <td>

                    <?php echo round($row['quantity'],2); ?>

                </td>

                <td>

                    <?php

                    if($profit_loss >= 0){

                        echo "<span style='color:green; font-weight:bold;'>

                        +₹".round($profit_loss,2)."

                        </span>";

                    }else{

                        echo "<span style='color:red; font-weight:bold;'>

                        ₹".round($profit_loss,2)."

                        </span>";
                    }

                    ?>

                </td>

                <td>

                    <?php echo $row['investment_date']; ?>

                </td>

            </tr>

            <?php } ?>

        </table>

    </div>

</div>

</body>

</html>