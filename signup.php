<?php 

require_once('DBconnect.php');


if(isset($_POST['fname']) && isset($_POST['pass'])){

	$u = $_POST['fname'];

	$p = $_POST['pass']; 

	$sql = "SELECT * FROM users WHERE username='$u'";

	$result = mysqli_query($conn, $sql);


	if(mysqli_num_rows($result) > 0){
	   header("location: signup.php?error=user_already_exist"); 
	}
	else{
	
	   $sql = "INSERT INTO users (username, password) VALUES('$u', '$p')";
	   mysqli_query($conn, $sql);
	   session_start();
       $_SESSION['user_id']= mysqli_insert_id($conn);
	   $_SESSION['username']=$u;
	   header("location: index.php");
	}
}
?>

<!-----------------------------------------------------------HTML------------------------------------------------------>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign Up</title>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(to right, #fbc2eb, #a6c1ee);
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
        color: #ff758c;
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
        border-color: #1fd1f9;
    }

    button {
        width: 100%;
        padding: 12px;
        border-radius: 25px;
        border: none;
        background: #ff758c;
        color: white;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
    }

    button:hover { background: #ff4b6e; }

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
    <h2>Sign Up</h2>
    <form action="signup.php" method="post">
        <input type="text" name="fname" placeholder="Username" required>
        <input type="password" name="pass" placeholder="Password" required>
        <button type="submit">Sign Up</button>
    </form>
    <?php
        if(isset($_GET['error']) && $_GET['error'] == 'user_already_exist'){
            echo '<div class="alert">Username already exists!</div>';
        }
    ?>
</div>

</body>
</html>
