<?php

session_start();

include("config/db.php");


if(isset($_POST['login'])){

    $email = $_POST['email'];

    $password = $_POST['password'];


    $user = $conn->query(

    "SELECT * FROM users

    WHERE email='$email'

    AND password='$password'"

    );


    if($user->num_rows > 0){

        $data = $user->fetch_assoc();

        $_SESSION['user_id'] = $data['id'];

        header("Location: index.php");

        exit();

    }else{

        echo "<script>

        alert('Invalid Email or Password');

        </script>";
    }
}

?>

<!DOCTYPE html>

<html>

<head>

    <title>Login</title>

    <link rel="stylesheet" href="style.css?v=2">

</head>

<body>

<div class="container">

    <div style="width:400px; margin:auto; background:white; padding:30px; border-radius:14px; margin-top:60px;">

        <h2 style="text-align:center;">

            User Login

        </h2>

        <form method="POST">

            <input
            type="email"
            name="email"
            placeholder="Enter Email"
            required
            style="width:100%; padding:12px; margin-top:15px;">

            <input
            type="password"
            name="password"
            placeholder="Enter Password"
            required
            style="width:100%; padding:12px; margin-top:15px;">

            <button
            type="submit"
            name="login"
            style="width:100%; padding:12px; margin-top:20px; background:#2563eb; color:white; border:none; border-radius:8px;">

                Login

            </button>

        </form>

        <p style="text-align:center; margin-top:15px;">

            Don't have an account?

            <a href="register.php">

                Register

            </a>

        </p>

    </div>

</div>

</body>

</html>