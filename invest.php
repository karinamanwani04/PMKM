<?php

session_start();

include("config/db.php");


// LOGIN CHECK

if(!isset($_SESSION['user_id'])){

    header("Location: login.php");

    exit();
}


// GET USER ID

$user_id = $_SESSION['user_id'];


// GET FORM DATA

$company_id = $_POST['company_id'];

$points = $_POST['points'];


// GET USER DATA

$user = $conn->query(

"SELECT * FROM users WHERE id=$user_id"

)->fetch_assoc();


// CHECK USER POINTS

if($points > $user['points']){

    die("Not enough points available");
}


// GET COMPANY DATA

$company = $conn->query(

"SELECT * FROM companies WHERE id=$company_id"

)->fetch_assoc();


// RANDOM STOCK PRICE

$symbol = $company['stock_symbol'];

$apiKey = "";


$url = "https://www.alphavantage.co/query?function=GLOBAL_QUOTE&symbol=$symbol&apikey=$apiKey";


$response = file_get_contents($url);

$data = json_decode($response,true);


$stock_price = $data['Global Quote']['05. price'] ?? 0;

// CALCULATE QUANTITY

$quantity = $points / $stock_price;


// SAVE INVESTMENT

$conn->query(

"INSERT INTO investments(

user_id,

company_id,

company_name,

invested_points,

stock_price,

quantity

)

VALUES(

'$user_id',

'$company_id',

'".$company['name']."',

'$points',

'$stock_price',

'$quantity'

)"

);


// UPDATE USER POINTS

$newPoints = $user['points'] - $points;

$conn->query(

"UPDATE users

SET points='$newPoints'

WHERE id=$user_id"

);


// SUCCESS REDIRECT

header("Location: dashboard.php");

?>