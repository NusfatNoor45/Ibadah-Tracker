<?php
session_start();
require_once('DBconnect.php');

if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $aid = 4;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $udate = mysqli_real_escape_string($conn, $_POST['udate']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $nog = !empty($_POST['good_deed']) ? (int)$_POST['good_deed'] : 0;

        $q = "SELECT record_id FROM daily_record WHERE user_id='$uid' AND `date`='$udate' LIMIT 1";
        $res = mysqli_query($conn, $q);

        if ($res && mysqli_num_rows($res) > 0) {
            $record_id = mysqli_fetch_assoc($res)['record_id'];
        } else {
            mysqli_query($conn, "INSERT INTO daily_record (user_id, `date`) VALUES('$uid','$udate')");
            $record_id = mysqli_insert_id($conn);
        }

        mysqli_query($conn, "INSERT INTO other_deed (activity_id, user_id, udate, description, good_deed) 
                             VALUES ('$aid', '$uid', '$udate', '$description', '$nog')");

        $points = $nog * 4;
        $value = $nog;

        $qcheck = "SELECT log_id FROM activity_log WHERE record_id='$record_id' AND activity_id='$aid' LIMIT 1";
        $rcheck = mysqli_query($conn, $qcheck);

        if ($rcheck && mysqli_num_rows($rcheck) > 0) {
            $log_id = mysqli_fetch_assoc($rcheck)['log_id'];
            mysqli_query($conn, "UPDATE activity_log SET `value`='$value', points_earned='$points' WHERE log_id='$log_id'");
        } else {
            mysqli_query($conn, "INSERT INTO activity_log (record_id, activity_id, `value`, points_earned) 
                                 VALUES ('$record_id', '$aid', '$value', '$points')");
        }
		header("Location: dawah.php");
exit;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>GOOD DEEDS</title>
<style>
body { margin:0; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg,#f8cdda,#1d2b64); color:#fff; }
.navbar { background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); padding:15px 30px; display:flex; justify-content:space-between; align-items:center;}
.navbar h1 { font-size:28px; color:#fff; }
.btn { padding:10px 20px; border-radius:25px; border:none; cursor:pointer; font-weight:bold; transition:0.3s; color:#fff; text-decoration:none;}
.btn-back { background:#1fd1f9; } .btn-back:hover { background:#ff4b6e; }
.container { padding:20px; text-align:center; }
table { width:100%; max-width:500px; margin:auto; border-collapse:collapse; color:#fff;}
table, th, td { border:1px solid #fff; padding:10px; }
th { background:rgba(255,255,255,0.2); }
input[type="date"], input[type="text"] { padding:8px; width:90%; border-radius:5px; border:none; }
input[type="submit"] { padding:10px 25px; border-radius:25px; border:none; cursor:pointer; background:#1fd1f9; color:#fff; font-weight:bold; transition:0.3s;}
input[type="submit"]:hover { background:#0abde3; }
input.text-input {
    padding:8px;
    width:90%;
    border-radius:5px;
    border:none;
    font-size:16px; /* same as Duration */
    box-sizing:border-box;
}
</style>
</head>
<body>

<div class="navbar">
<h1>Good Deeds</h1>
<a href="fasting.php" class="btn btn-back">Back</a>
</div>

<div class="container">
<form action="other_deed.php" method="post">
<table>
<!--<caption>Good Deeds</caption>-->
<tr>
<td colspan="2">Date: <input type="date" name="udate" required value="<?php echo htmlspecialchars($selected_date); ?>"></td>
</tr>
<tr>
<th>Number of Good Deeds</th>
<th>Description</th>
</tr>
<tr>
<!--<td></td>-->
<td><input type="text" name="good_deed" required value="">
<!--  <input type="number" name="number_of_pages" min="0" max="3" 
         value="<//?php echo htmlspecialchars($number_of_pages); ?>" 
         placeholder="1-3" required class="text-input">-->
  <br><small style="color:#ffd700;">Maximum 3 deeds per day</small>
</td>
<td><input type="text" name="description" value=""></td>
</tr>
</table>
<br>
<!--<input type="submit" value="Add to Database">-->
<!--<p><a href="dawah.php" class="btn btn-back">Submit</a></p>-->
<p><button type="submit" class="btn btn-back">Submit</button></p>


</form>
</div>

</body>
</html>