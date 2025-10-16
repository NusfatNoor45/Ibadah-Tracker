<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("location: signin.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Activities</title>
<style>
body {
    margin:0; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg,#f8cdda,#1d2b64); color:#fff;
}
.navbar {
    background: rgba(255,255,255,0.2); backdrop-filter: blur(10px);
    padding:15px 30px; display:flex; justify-content:space-between; align-items:center;
}
.navbar h1 { font-size:28px; color:#fff; }
.btn { padding:10px 20px; border-radius:25px; border:none; cursor:pointer; font-weight:bold; transition:0.3s; color:#fff; text-decoration:none;}
.btn-signout { background:#1fd1f9; } .btn-signout:hover { background:#0abde3; }
.container { text-align:center; padding:20px; }
ul { list-style:none; padding:0; display:flex; justify-content:center; gap:15px; flex-wrap:wrap; }
ul li a { background:#ff758c; padding:10px 20px; border-radius:25px; text-decoration:none; color:#fff; transition:0.3s;}
ul li a:hover { background:#ff4b6e; }
</style>
</head>
<body>

<div class="navbar">
<h1>Activities</h1>
<a href="index.php" class="btn btn-signout">Home</a>
</div>

<div class="container">
<ul>
<li><a href="salah.php">Salah</a></li>
<li><a href="quran.php">Quran</a></li>
<li><a href="fasting.php">Fasting</a></li>
<li><a href="other_deed.php">Other Deeds</a></li>
<li><a href="dawah.php">Dawah</a></li>
</ul>
<br>
<a href="daily_record.php" class="btn btn-signout">Daily Record</a>
</div>

</body>
</html>

