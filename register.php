<?php

include("config/db.php");


if(isset($_POST['register'])){

    $name = $_POST['name'];

    $email = $_POST['email'];

    $password = $_POST['password'];


    // CHECK EMAIL

    $check = $conn->query(

    "SELECT * FROM users WHERE email='$email'"

    );


    if($check->num_rows > 0){

        echo "<script>

        alert('Email already exists');

        </script>";

    }else{


        // INSERT USER

        $conn->query(

        "INSERT INTO users(

        name,

        email,

        password

        )

        VALUES(

        '$name',

        '$email',

        '$password'

        )"

        );


        echo "<script>

        alert('Registration Successful');

        window.location='login.php';

        </script>";
    }
}

?>

<!DOCTYPE html>

<html>

<head>

    <title>Register</title>

    <link rel="stylesheet" href="style.css?v=2">

</head>

<body>

<div class="container">

    <div style="width:400px; margin:auto; background:white; padding:30px; border-radius:14px; margin-top:60px;">

        <h2 style="text-align:center;">

            User Registration

        </h2>

        <form method="POST">

            <input
            type="text"
            name="name"
            placeholder="Enter Name"
            required
            style="width:100%; padding:12px; margin-top:15px;">

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
            name="register"
            style="width:100%; padding:12px; margin-top:20px; background:#2563eb; color:white; border:none; border-radius:8px;">

                Register

            </button>

        </form>

        <p style="text-align:center; margin-top:15px;">

            Already have an account?

            <a href="login.php">

                Login

            </a>

        </p>

    </div>

</div>

</body>

</html>