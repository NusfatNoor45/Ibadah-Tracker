
<?php
session_start();
require_once('DBconnect.php');

if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $aid = 2; 

    $selected_date = $_POST['udate'] ?? date('Y-m-d');

    $q = "SELECT record_id FROM daily_record WHERE user_id='$uid' AND `date`='$selected_date' LIMIT 1";
    $res = mysqli_query($conn, $q);
    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        $record_id = $row['record_id'];
    } else {
        $ins = "INSERT INTO daily_record (user_id, `date`) VALUES ('$uid', '$selected_date')";
        mysqli_query($conn, $ins);
        $record_id = mysqli_insert_id($conn);
    }

    $sql_fetch = "SELECT * FROM quran_recite WHERE user_id='$uid' AND activity_id='$aid' AND udate='$selected_date' LIMIT 1";
    $result_fetch = mysqli_query($conn, $sql_fetch);

    if (mysqli_num_rows($result_fetch) > 0) {
        $existing = mysqli_fetch_assoc($result_fetch);
        $number_of_pages = $existing['number_of_pages'];
        $duration = $existing['duration'];
    } else {
        $number_of_pages = '';
        $duration = '';
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $number_of_pages = mysqli_real_escape_string($conn, $_POST['number_of_pages']);
        $duration = mysqli_real_escape_string($conn, $_POST['duration']);

        $total_done = $number_of_pages; 
        $points = $total_done * 5;

        if (mysqli_num_rows($result_fetch) > 0) {
            $upd = "UPDATE quran_recite 
                    SET number_of_pages='$number_of_pages', duration='$duration'
                    WHERE user_id='$uid' AND activity_id='$aid' AND udate='$selected_date'";
            mysqli_query($conn, $upd);

           
            $qcheck = "SELECT log_id FROM activity_log WHERE record_id='$record_id' AND activity_id='$aid' LIMIT 1";
            $rcheck = mysqli_query($conn, $qcheck);
            if ($rcheck && mysqli_num_rows($rcheck) > 0) {
                $rowc = mysqli_fetch_assoc($rcheck);
                $log_id = $rowc['log_id'];
                $upd_log = "UPDATE activity_log SET `value`='$total_done', points_earned='$points' WHERE log_id='$log_id'";
                mysqli_query($conn, $upd_log);
            }
        } else {
            $ins = "INSERT INTO quran_recite (activity_id, user_id, udate, number_of_pages, duration)
                    VALUES ('$aid','$uid','$selected_date','$number_of_pages','$duration')";
            mysqli_query($conn, $ins);

            $ins_log = "INSERT INTO activity_log (record_id, activity_id, `value`, points_earned)
                        VALUES ('$record_id','$aid','$total_done','$points')";
            mysqli_query($conn, $ins_log);
        }
		header("Location: fasting.php");
exit;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>QURAN</title>
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
input[type="text"], input[type="date"] { padding:8px; width:90%; border-radius:5px; border:none; }
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
<h1>Quran Recitation</h1>
<a href="salah.php" class="btn btn-back">Back</a>
</div>

<div class="container">
<form method="post" action="quran.php">
<table>
<!--<caption>Quran Recitation</caption>-->
<tr>
<td colspan="2">Date: <input type="date" name="udate" required value="<?php echo htmlspecialchars($selected_date); ?>"></td>
</tr>
<tr>
<th>Number of Pages</th>
<th>Duration</th>
</tr>
<tr>
<!--<td></td>-->
<td><input type="text" name="number_of_pages" required value="<?php echo htmlspecialchars($number_of_pages); ?>">
  <!--<input type="number" name="number_of_pages" min="0" max="5" 
         value="<//?php echo htmlspecialchars($number_of_pages); ?>" 
         placeholder="1-5 pages" required class="text-input">-->
  <br><small style="color:#ffd700;">Maximum 5 pages per day</small>
</td>
<td><input type="text" name="duration" value="<?php echo htmlspecialchars($duration); ?>"></td>
</tr>
</table>
<br>
<!--<input type="submit" value="Add to Database">-->
<p><button type="submit" class="btn btn-back">Submit</button></p>

</form>
</div>

</body>
</html>

</html>
