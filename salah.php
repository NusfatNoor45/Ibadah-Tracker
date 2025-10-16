<?php
session_start();
require_once('DBconnect.php');
if (isset($_SESSION['user_id'])) {
	$uid = $_SESSION['user_id'];
	$aid = 1;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	//$udate = $_POST['udate'];
	$udate = mysqli_real_escape_string($conn, $_POST['udate']);
    $fajr = isset($_POST['fajr']) ? 1 : 0;
    $dhuhr = isset($_POST['dhuhr']) ? 1 : 0;
    $asr = isset($_POST['asr']) ? 1 : 0;
    $maghrib = isset($_POST['maghrib']) ? 1 : 0;
    $isha = isset($_POST['isha']) ? 1 : 0;
	$q = "SELECT record_id FROM daily_record WHERE user_id='$uid' AND `date` = '$udate' LIMIT 1";
    $res = mysqli_query($conn, $q);
    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        $record_id = $row['record_id'];
    } else {
        $ins = "INSERT INTO daily_record (user_id, `date`) VALUES('$uid', '$udate')";
        mysqli_query($conn, $ins);
        $record_id = mysqli_insert_id($conn);
    }
	$sql = "INSERT INTO salah (activity_id, user_id, udate, fajr, dhuhr, asr, maghrib, isha) VALUES ('$aid', '$uid', '$udate', '$fajr', '$dhuhr', '$asr', '$maghrib', '$isha')";
    mysqli_query($conn, $sql);
	$total_done = $fajr + $dhuhr + $asr + $maghrib + $isha;
    $points = $total_done * 10; 
    $value = $total_done;
    $status_text = "Salah: $total_done done"; 
	$qcheck = "SELECT log_id FROM activity_log WHERE record_id='$record_id' AND activity_id='$aid' LIMIT 1";
	$rcheck = mysqli_query($conn, $qcheck);
	if ($rcheck && mysqli_num_rows($rcheck) > 0) {
		$rowc = mysqli_fetch_assoc($rcheck);
		$log_id = $rowc['log_id'];
		$upd = "UPDATE activity_log SET `value`='$value', points_earned='$points' WHERE log_id='$log_id'";
		mysqli_query($conn, $upd);
	} else {
		$ins = "INSERT INTO activity_log (record_id, activity_id, `value`, points_earned)
				VALUES ('$record_id', '$aid', '$value', '$points')";
		mysqli_query($conn, $ins);
	}
	header("Location: quran.php");
exit;
}
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Salah</title>
<style>
body { margin:0; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg,#f8cdda,#1d2b64); color:#fff; }
.navbar { background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); padding:15px 30px; display:flex; justify-content:space-between; align-items:center;}
.navbar h1 { font-size:28px; color:#fff; }
.btn { padding:10px 20px; border-radius:25px; border:none; cursor:pointer; font-weight:bold; transition:0.3s; color:#fff; text-decoration:none;}
.btn-back { background:#1fd1f9; } .btn-back:hover { background:#ff4b6e; }
.container { padding:20px; text-align:center; }
table { width:100%; max-width:600px; margin:auto; border-collapse:collapse; color:#fff;}
table, th, td { border:1px solid #fff; padding:10px; }
th { background:rgba(255,255,255,0.2); }
input[type="checkbox"] { transform:scale(1.5); margin:auto; display:block;}
input[type="submit"] { padding:10px 25px; border-radius:25px; border:none; cursor:pointer; background:#1fd1f9; color:#fff; font-weight:bold; transition:0.3s;}
input[type="submit"]:hover { background:#0abde3; }
</style>
</head>
<body>

<div class="navbar">
<h1>Salah</h1>
<a href="activity.php" class="btn btn-back">Back</a>

</div>

<div class="container">
<form action="salah.php" method="post">
<table>
<!--<caption>SALAH</caption>-->
<tr><td colspan="5">Date: <input type="date" name="udate" required></td></tr>
<tr>
<th>Fajr</th><th>Dhuhr</th><th>Asr</th><th>Maghrib</th><th>Isha</th>
</tr>
<tr>
<td><input type="checkbox" name="fajr" value="1"></td>
<td><input type="checkbox" name="dhuhr" value="1"></td>
<td><input type="checkbox" name="asr" value="1"></td>
<td><input type="checkbox" name="maghrib" value="1"></td>
<td><input type="checkbox" name="isha" value="1"></td>
</tr>
</table>
<br>
<!--<input type="submit" value="Save">-->
<p><button type="submit" class="btn btn-back">Submit</button></p>
</form>
</div>
</body>
</html>
