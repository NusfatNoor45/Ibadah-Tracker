<?php

require_once('DBconnect.php');


if(isset($_POST['fname']) && isset($_POST['pass'])){

	$u = $_POST['fname'];

	$p = $_POST['pass'];

	$sql = "SELECT * FROM users WHERE username = '$u' AND password = '$p'";

	$result = mysqli_query($conn, $sql);


	if(mysqli_num_rows($result) !=0 ){

	   session_start();
	   $row = mysqli_fetch_assoc($result); 
	   $_SESSION['user_id']=$row['user_id'];
	   $_SESSION['username']=$row['username'];
	   header("location: index.php");
	}
	else{
	   header("location: signin.php?error=invalid_credentials"); 
	   
	}
	
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign In</title>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(to right, #a1c4fd, #c2e9fb);
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }

    .card {
        background: #fff;
        padding: 40px;
        border-radius: 25px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        width: 350px;
        text-align: center;
    }

    .card h2 {
        color: #1fd1f9;
        margin-bottom: 25px;
    }

    input[type="text"], input[type="password"] {
        width: 100%;
        padding: 12px 15px;
        margin: 8px 0;
        border-radius: 15px;
        border: 1px solid #ddd;
        outline: none;
        transition: 0.3s;
    }

    input[type="text"]:focus, input[type="password"]:focus {
        border-color: #ff758c;
    }

    button {
        width: 100%;
        padding: 12px;
        border-radius: 25px;
        border: none;
        background: #1fd1f9;
        color: white;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
    }

    button:hover { background: #0abde3; }

    .alert {
        background-color: #ffe0e0;
        color: #d63384;
        padding: 10px;
        border-radius: 10px;
        margin-top: 15px;
    }
</style>
</head>
<body>

<div class="card">
    <h2>Sign In</h2>
    <form action="signin.php" method="post">
        <input type="text" name="fname" placeholder="Username" required>
        <input type="password" name="pass" placeholder="Password" required>
        <button type="submit">Sign In</button>
    </form>
    <?php
        if(isset($_GET['error']) && $_GET['error'] == 'invalid_credentials'){
            echo '<div class="alert">Invalid credentials!</div>';
        }
    ?>
</div>

</body>
</html>
