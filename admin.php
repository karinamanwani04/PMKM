<?php

session_start();

include("config/db.php");


// LOGIN CHECK

if(!isset($_SESSION['user_id'])){

    header("Location: dashboard.php");

    exit();
}


// CURRENT USER

$user_id = $_SESSION['user_id'];

$user = $conn->query(

"SELECT * FROM users WHERE id=$user_id"

)->fetch_assoc();


// ADMIN CHECK

if($user['role'] != 'admin'){

    die("Access Denied");
}


// ALL USERS

$users = $conn->query(

"SELECT * FROM users ORDER BY id DESC"

);


// ALL INVESTMENTS

$investments = $conn->query(

"SELECT * FROM investments ORDER BY id DESC"

);

?>

<!DOCTYPE html>

<html>

<head>

    <title>Admin Panel</title>

    <link rel="stylesheet" href="style.css?v=2">

</head>

<body style="background:#f3f4f6; font-family:Arial;">

<div style="background:linear-gradient(to right,#4338ca,#6366f1); padding:20px; color:white; font-size:28px; font-weight:bold;">

    Admin Panel

</div>

<div style="padding:30px;">

    <!-- USERS SECTION -->

    <div style="background:white; padding:25px; border-radius:16px; margin-bottom:30px;">

        <h2 style="margin-bottom:20px;">

            All Users

        </h2>

        <table
        border="1"
        cellpadding="12"
        width="100%"
        style="border-collapse:collapse; text-align:center;">

            <tr style="background:#e5e7eb;">

                <th>ID</th>

                <th>Name</th>

                <th>Email</th>

                <th>Points</th>

                <th>Role</th>

                <th>Created At</th>

            </tr>

            <?php

            while($userData = $users->fetch_assoc()){

            ?>

            <tr>

                <td>

                    <?php echo $userData['id']; ?>

                </td>

                <td>

                    <?php echo $userData['name']; ?>

                </td>

                <td>

                    <?php echo $userData['email']; ?>

                </td>

                <td>

                    <?php echo $userData['points']; ?>

                </td>

                <td>

                    <?php echo $userData['role']; ?>

                </td>

                <td>

                    <?php echo $userData['created_at']; ?>

                </td>

            </tr>

            <?php } ?>

        </table>

    </div>


    <!-- INVESTMENTS SECTION -->

    <div style="background:white; padding:25px; border-radius:16px;">

        <h2 style="margin-bottom:20px;">

            All Investments

        </h2>

        <table
        border="1"
        cellpadding="12"
        width="100%"
        style="border-collapse:collapse; text-align:center;">

            <tr style="background:#e5e7eb;">

                <th>ID</th>

                <th>User ID</th>

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

                    <?php echo $row['id']; ?>

                </td>

                <td>

                    <?php echo $row['user_id']; ?>

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