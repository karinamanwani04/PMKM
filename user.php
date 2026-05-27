<?php

session_start();

include("config/db.php");

if(!isset($_SESSION['user_id'])){

    header("Location: login.php");
}

$user_id = $_SESSION['user_id'];


// USER DATA

$user = $conn->query(

"SELECT * FROM users WHERE id=$user_id"

)->fetch_assoc();


// USER INVESTMENTS

$investments = $conn->query(

"SELECT * FROM investments

WHERE user_id=$user_id

ORDER BY id DESC"

);

?>

<!DOCTYPE html>

<html>

<head>

    <title>User Dashboard</title>

    <link rel="stylesheet" href="style.css?v=2">

</head>

<body>

<div class="header">

    Welcome,
    <?php echo $user['name']; ?>

</div>

<div class="container">

    <!-- USER INFO -->

    <div style="background:white; padding:20px; border-radius:14px; margin-bottom:20px;">

        <h2>Your Points</h2>

        <h1 style="color:#2563eb;">

            <?php echo $user['points']; ?>

        </h1>

    </div>

    <!-- INVESTMENTS -->

    <div style="background:white; padding:20px; border-radius:14px;">

        <h2>Your Investments</h2>

        <table
        border="1"
        cellpadding="12"
        width="100%"
        style="border-collapse:collapse; text-align:center;">

            <tr>

                <th>Company</th>

                <th>Invested Points</th>

                <th>Stock Price</th>

                <th>Quantity</th>

                <th>Date</th>

            </tr>

            <?php

            while($row = $investments->fetch_assoc()){

            ?>

            <tr>

                <td>

                    <?php echo $row['company_name']; ?>

                </td>

                <td>

                    <?php echo $row['invested_points']; ?>

                </td>

                <td>

                    ₹<?php echo $row['stock_price']; ?>

                </td>

                <td>

                    <?php echo round($row['quantity'],2); ?>

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