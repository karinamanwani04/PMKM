<?php

session_start();

include("config/db.php");


if(!isset($_SESSION['user_id'])){

    header("Location: login.php");

    exit();
}


$user_id = $_SESSION['user_id'];

$investment_id = $_POST['investment_id'];


/* GET INVESTMENT */

$investment = $conn->query(

"SELECT * FROM investments

WHERE id='$investment_id'

AND user_id='$user_id'"

)->fetch_assoc();


if(!$investment){

    die("Investment not found");
}


/* COMPANY */

$company_id = $investment['company_id'];


/* GET COMPANY */

$company = $conn->query(

"SELECT * FROM companies

WHERE id='$company_id'"

)->fetch_assoc();


/* STOCK SYMBOL */

$symbol = $company['stock_symbol'];


/* API */

$apiKey = "YOUR_API_KEY";


$url =
"https://www.alphavantage.co/query?function=GLOBAL_QUOTE&symbol=$symbol&apikey=$apiKey";


$response = @file_get_contents($url);

$data = json_decode($response,true);


/* LIVE PRICE */

if(isset($data['Global Quote']['05. price'])){

    $live_price =
    $data['Global Quote']['05. price'];

}else{

    $live_price =
    $investment['stock_price'];
}


/* CURRENT VALUE */

$current_value =
$investment['quantity']
*
$live_price;


/* GET USER */

$user = $conn->query(

"SELECT * FROM users

WHERE id='$user_id'"

)->fetch_assoc();


/* NEW USER POINTS */

$new_points =
$user['points']
+
$current_value;


/* UPDATE USER */

$conn->query(

"UPDATE users

SET points='$new_points'

WHERE id='$user_id'"

);


/* DELETE INVESTMENT */

$conn->query(

"DELETE FROM investments

WHERE id='$investment_id'"

);


/* REDIRECT */

header("Location: dashboard.php");

exit();

?>