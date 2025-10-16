<?php 
session_start();
require_once('DBconnect.php');


if(!isset($_SESSION['user_id'])){
    header("location: signin.php");
    exit;
}

$current_user_id = $_SESSION['user_id'];

$current_date = isset($_POST['date']) ? $_POST['date'] : date('Y-m-d');

$message = "";
$impvg = "";
$quotes_html = "";

$sql_record = "SELECT record_id 
               FROM daily_record 
               WHERE user_id = '$current_user_id' AND date = '$current_date'";

$result_record = mysqli_query($conn, $sql_record);
if (mysqli_num_rows($result_record) > 0) {
    $row = mysqli_fetch_assoc($result_record);
    $record_id = $row['record_id'];

	$sql_compare = "
		SELECT a.activity_id, a.name, a.max_point, 
			   COALESCE(SUM(al.points_earned),0) AS earned
		FROM activity a
		LEFT JOIN activity_log al ON a.activity_id = al.activity_id AND al.record_id = '$record_id'
		GROUP BY a.activity_id, a.name, a.max_point
	";
	$res_compare = mysqli_query($conn, $sql_compare);

	$lowest_percentage = 101;
	$weak_activities = [];

	while ($row = mysqli_fetch_assoc($res_compare)) {
		$earned = (int)$row['earned'];
		$max_point = (int)$row['max_point'];
		$percentage = $max_point > 0 ? ($earned / $max_point) * 100 : 0;

		if ($percentage < $lowest_percentage) {
			$lowest_percentage = $percentage;
			$weak_activities = [$row['name']];
		} elseif ($percentage == $lowest_percentage) {
			$weak_activities[] = $row['name'];
		}
	}

	$guidance_text = implode(", ", $weak_activities);
if (!empty($weak_activities)) {
    $activity_name = $weak_activities[0]; 
    $sql_act = "SELECT activity_id FROM activity WHERE name='".mysqli_real_escape_string($conn, $activity_name)."'";
    $res_act = mysqli_query($conn, $sql_act);
    if ($row_act = mysqli_fetch_assoc($res_act)) {
        $activity_id = $row_act['activity_id'];

        $q_sql = "SELECT text, source FROM quote WHERE activity_id_for_quote='$activity_id' LIMIT 1";
        $q_res = mysqli_query($conn, $q_sql);
        if (mysqli_num_rows($q_res) > 0) {
            $quotes_html .= "<h3>Motivational Quote</h3>";
            while($q = mysqli_fetch_assoc($q_res)) {
                $quotes_html .= "<blockquote>".$q['text']."</blockquote>";
                $quotes_html .= "<i>- ".$q['source']."</i><br><br>";
            }
        }
    }
}

	$update_q = "
		UPDATE daily_record 
		SET improvement_guidance = '".mysqli_real_escape_string($conn, $guidance_text)."'
		WHERE record_id = '$record_id'
	";
	mysqli_query($conn, $update_q);

    $sql_count = "SELECT COUNT(DISTINCT activity_id) as activity_done
                  FROM activity_log
                  WHERE record_id = '$record_id'";
    $result_count = mysqli_query($conn, $sql_count);
    $row_count = mysqli_fetch_assoc($result_count);
    $activity_done = $row_count['activity_done'];

    $sql_total = "SELECT COUNT(*) as total_activities FROM activity";
    $result_total = mysqli_query($conn, $sql_total);
    $row_total = mysqli_fetch_assoc($result_total);
    $total_activities = $row_total['total_activities'];

    if ($activity_done == $total_activities) {
        $sql_points = "SELECT SUM(points_earned) as total_points
                       FROM activity_log
                       WHERE record_id = '$record_id'";
        $result_points = mysqli_query($conn, $sql_points);
        $row_points = mysqli_fetch_assoc($result_points);
        $total_points = $row_points['total_points'] ?? 0;

        // Update daily_record table with total_points
        $update_sql = "UPDATE daily_record 
                       SET total_points = '$total_points' 
                       WHERE record_id = '$record_id'";
        mysqli_query($conn, $update_sql);

        $message = "✅ All activities submitted. Total points for today is <strong>$total_points</strong>";
		// Show improvement guidance after total points
		if (!empty($guidance_text)) {
			$impvg = "<strong>Improvement Guidance:</strong> <span style='color:#ff0000;'>$guidance_text</span>";
		}

    } else {
        //$message = "⚠️ Please submit all activities to see total points of the day.";
		
    $missed_activities = [];
    $sql_missed = "
        SELECT a.name
        FROM activity a
        LEFT JOIN activity_log al 
        ON a.activity_id = al.activity_id AND al.record_id = '$record_id'
        WHERE al.points_earned IS NULL OR al.points_earned = 0
    ";
    $res_missed = mysqli_query($conn, $sql_missed);
    while ($row = mysqli_fetch_assoc($res_missed)) {
        $missed_activities[] = $row['name'];
    }

    // Prepare message
    if (!empty($missed_activities)) {
        $message = "⚠️ Missed or zero-point activities: <strong>".implode(", ", $missed_activities)."</strong>";
    } else {
        $message = "⚠️ Some activities are incomplete.";
    }

    if (!empty($guidance_text)) {
        $impvg = "<strong>Improvement Guidance:</strong> <span style='color:#6a5acd;'>$guidance_text</span>";
    }


    }
	
    $q_sql = "SELECT text, source FROM quote WHERE record_id = '$record_id'"; //shows quote
    $q_res = mysqli_query($conn, $q_sql);
    if (mysqli_num_rows($q_res) > 0) {
        $quotes_html .= "<h3>Motivational Quote(s):</h3>";
        while($q = mysqli_fetch_assoc($q_res)) {
            $quotes_html .= "<blockquote>".$q['text']."</blockquote>";
            $quotes_html .= "<i>- ".$q['source']."</i><br><br>";
        }
    }
	
} else {
    $message = "No record found for this date.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daily Record</title>
<style>
body {
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #f8cdda, #1d2b64);
    color: #fff;
}
.navbar {
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    padding: 15px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #fff;
}
.navbar h1 { font-size: 28px; color: #fff; }
.btn { padding: 10px 20px; border-radius: 25px; border: none; cursor: pointer; font-weight: bold; transition:0.3s; color:#fff; text-decoration:none;}
.btn-back { background: #ff758c; } .btn-back:hover { background:#ff4b6e; }
.btn-signout { background: #1fd1f9; } .btn-signout:hover { background:#0abde3; }
.container { padding: 20px; text-align:center; }
form input, form button { padding: 8px; margin:5px 0; border-radius:5px; border:none; }
form input[type="date"] { width:200px; }
blockquote { font-style: italic; color:#f0e68c; }
</style>
</head>
<body>

<div class="navbar">
<h1>Daily Record</h1>
<div>
<a href="activity.php" class="btn btn-back">Activities</a>
<a href="signout.php" class="btn btn-signout">Sign Out</a>
</div>
</div>

<div class="container">
<form method="POST">
<label for="date">Select Date:</label>
<input type="date" name="date" required value="<?php echo $current_date; ?>">
<br>
<button type="submit">View</button>
</form>

<p><?php echo $message; ?></p>
<p><?php echo $impvg; ?></p>
<div><?php echo $quotes_html; ?></div>
</div>
<p style="text-align:center;"><a href="index.php" class="btn btn-back mt-3">Home</a></p>

</body>
</html>
