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
        color: #333;
    }
    .navbar {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        padding: 15px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: #fff;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .navbar h1 {
        font-size: 30px;
        font-weight: bold;
        color: #fff;
        letter-spacing: 1px;
    }
    .btn {
        padding: 10px 20px;
        border-radius: 25px;
        border: none;
        cursor: pointer;
        font-weight: bold;
        transition: 0.3s;
        color: #fff;
        text-decoration: none;
    }
    .btn-login { background: #ff758c; }
    .btn-login:hover { background: #ff4b6e; }

    .btn-signup { background: #1fd1f9; }
    .btn-signup:hover { background: #0abde3; }

    .hero {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        height: 77vh;
        text-align: center;
        color: #fff;
    }

    .hero h2 {
        font-size: 48px;
        margin-bottom: 20px;
    }

    .hero p {
        font-size: 22px;
        margin-bottom: 30px;
    }

    .app-name {
        font-size: 70px;
        font-weight: bold;
        background: linear-gradient(90deg, #ff9a9e, #fad0c4, #fbc2eb);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        text-shadow: 5px 5px 6px rgba(0,0,0,0.3);
        letter-spacing: 2px;
    }

    .verse {
        font-size: 13px;
        font-style: italic;
        color: #E9E4D4;
        margin-top: 20px;
        max-width: 700px;
    }
</style>
</head>
<body>

<div class="navbar">
    <h1>Ibadah Tracker</h1>
    <div>
        <a href="signin.php" class="btn btn-login">Log In</a>
        <a href="signup.php" class="btn btn-signup">Sign Up</a>
    </div>
</div>

<div class="hero">
    <h2>Welcome to</h2>
    <p class="app-name">Ibadah Tracker</p>
    <p>Keep track of your daily worship, stay motivated, and grow spiritually every day.</p>
    <p class="verse">"Indeed, those who have believed and done righteous deeds – 
    they will have the Gardens of Paradise as a lodging." (Qur'an 18:107)</p>
</div>

</body>
</html>
