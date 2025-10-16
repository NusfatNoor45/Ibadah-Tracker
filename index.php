<?php
session_start();
$username = isset($_SESSION['username']) ? $_SESSION['username'] : "User";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tracker Home</title>
<style>
    body {
        margin: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #f8cdda, #1d2b64);
        color: #fff;
    }

    .navbar {
        background: rgba(0, 0, 0, 0.2);
        backdrop-filter: blur(10px);
        padding: 15px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .navbar h1 {
        font-size: 28px;
        font-weight: bold;
    }

    .hero {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        height: 80vh;
        text-align: center;
    }

    .hero h2 {
        font-size: 48px;
        margin-bottom: 30px;
    }

    .hero h3 {
        font-size: 22px;
        font-weight: 400;
        margin-bottom: 30px;
    }

    .hero .btn-container {
        display: flex;
        gap: 20px;
        justify-content: center;
    }

    .btn {
        background: #ff758c;
        color: #fff;
        padding: 10px 20px;
        border-radius: 25px;
        border: none;
        cursor: pointer;
        text-decoration: none;
        transition: 0.3s;
        font-weight: bold;
    }

    .btn:hover { 
        background: #ff4b6e; 
    }
</style>
</head>
<body>

<div class="navbar">
    <h1>Tracker</h1>
    <a href="signout.php" class="btn">Sign Out</a>
</div>

<div class="hero">
    <h2>Assalamu Alaikum, <?php echo htmlspecialchars($username); ?>!</h2>
    <h3>Alhamdulillah, you’re signed in 🌸</h3>
    <p>May Allah accept your efforts and bless your journey of self-improvement.</p>
    <div class="btn-container">
        <a href="daily_record.php" class="btn">Daily Record</a>
        <a href="monthly.php" class="btn">Monthly Report</a>
    </div>
</div>

</div>

</body>
</html>
