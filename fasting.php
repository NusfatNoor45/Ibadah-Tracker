<?php
session_start();
require_once('DBconnect.php');

if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $aid = 3; // Fasting activity ID

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $udate = mysqli_real_escape_string($conn, $_POST['udate']);
        $fasted = isset($_POST['fasted']) ? 1 : 0;

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

        $sql = "INSERT INTO fasting (activity_id, user_id, udate, fasted)
                VALUES ('$aid', '$uid', '$udate', '$fasted')";
        mysqli_query($conn, $sql);

    
        $points = $fasted * 5; 
        $value = $fasted;

        $qcheck = "SELECT log_id FROM activity_log WHERE record_id='$record_id' AND activity_id='$aid' LIMIT 1";
        $rcheck = mysqli_query($conn, $qcheck);

        if ($rcheck && mysqli_num_rows($rcheck) > 0) {
            $rowc = mysqli_fetch_assoc($rcheck);
            $log_id = $rowc['log_id'];
            $upd = "UPDATE activity_log 
                    SET `value`='$value', points_earned='$points' WHERE log_id='$log_id'";
            mysqli_query($conn, $upd);
        } else {
            $ins = "INSERT INTO activity_log (record_id, activity_id, `value`, points_earned)
                    VALUES ('$record_id', '$aid', '$value', '$points')";
            mysqli_query($conn, $ins);
        }
		header("Location: other_deed.php");
exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FASTING</title>
<style>
body { margin:0; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg,#f8cdda,#1d2b64); color:#fff; }
.navbar { background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); padding:15px 30px; display:flex; justify-content:space-between; align-items:center;}
.navbar h1 { font-size:28px; color:#fff; }
.btn { padding:10px 20px; border-radius:25px; border:none; cursor:pointer; font-weight:bold; transition:0.3s; color:#fff; text-decoration:none;}
.btn-back { background:#1fd1f9; } .btn-back:hover { background:#ff4b6e; }
.container { padding:20px; text-align:center; }
table { width:100%; max-width:400px; margin:auto; border-collapse:collapse; color:#fff;}
table, th, td { border:1px solid #fff; padding:10px; }
th { background:rgba(255,255,255,0.2); }
input[type="date"] { padding:8px; width:90%; border-radius:5px; border:none; }
input[type="submit"] { padding:10px 25px; border-radius:25px; border:none; cursor:pointer; background:#1fd1f9; color:#fff; font-weight:bold; transition:0.3s;}
input[type="submit"]:hover { background:#0abde3; }
input[type="checkbox"] { transform:scale(1.5); margin:auto; display:block;}
</style>
</head>
<body>

<div class="navbar">
<h1>Fasting</h1>
<a href="quran.php" class="btn btn-back">Back</a>
</div>

<div class="container">
<form action="fasting.php" method="post">
<table>
<!--<caption>Fasting</caption>-->
<tr>
<td colspan="2">Date: <input type="date" name="udate" required value="<?php echo htmlspecialchars($selected_date); ?>"></td>
</tr>
<tr>
<th>Fasted Today</th>
</tr>
<tr>
<td><input type="checkbox" name="fasted" value="1"></td>
</tr>
</table>
<br>
<!--<input type="submit" value="Add to Database">-->
<p><button type="submit" class="btn btn-back">Submit</button></p>

</form>
</div>

</body>
</html>
