<?php
session_start();
require_once('DBconnect.php');

if (!isset($_SESSION['user_id'])) {
    header("location: signin.php");
    exit;
}

$uid = $_SESSION['user_id'];

$month = isset($_POST['month']) ? $_POST['month'] : date('Y-m');

$sql_daily = "
    SELECT dr.record_id, dr.date
    FROM daily_record dr
    WHERE dr.user_id='$uid' AND DATE_FORMAT(dr.date,'%Y-%m')='$month'
";
$res_daily = mysqli_query($conn, $sql_daily);

$daily_records = [];
while ($row = mysqli_fetch_assoc($res_daily)) {
    $daily_records[] = $row;
}

$total_points = 0;
$days_count = count($daily_records) ?: 1; 
foreach ($daily_records as $dr) {
    $sql_points = "SELECT SUM(points_earned) AS sum_points FROM activity_log WHERE record_id='".$dr['record_id']."'";
    $r = mysqli_query($conn, $sql_points);
    $sum = mysqli_fetch_assoc($r)['sum_points'] ?? 0;
    $total_points += (int)$sum;
}

$sql_compare = "
    SELECT a.activity_id, a.name, a.max_point, 
           COALESCE(SUM(al.points_earned),0) AS earned
    FROM activity a
    LEFT JOIN activity_log al 
           ON a.activity_id = al.activity_id 
           AND al.record_id IN (SELECT record_id FROM daily_record WHERE user_id='$uid' AND DATE_FORMAT(date,'%Y-%m')='$month')
    GROUP BY a.activity_id, a.name, a.max_point
";
$res_compare = mysqli_query($conn, $sql_compare);

$lowest_percentage = 101;
$worst_activity = "";
$missed_activities = [];

while ($row = mysqli_fetch_assoc($res_compare)) {
    $earned = (int)$row['earned'];
    $max_point = (int)$row['max_point'];
    $percentage = $max_point > 0 ? ($earned / ($max_point * $days_count)) * 100 : 0; 

    if ($earned == 0) {
        $missed_activities[] = $row['name'];
    }

    if ($percentage < $lowest_percentage) {
        $lowest_percentage = $percentage;
        $worst_activity = $row['name'];

    }
}

$imp_guide = $worst_activity;
$missed_summary = implode(", ", $missed_activities);
$quotes_html = "";
if (!empty($imp_guide)) {
    $sql_act = "SELECT activity_id FROM activity WHERE name='".mysqli_real_escape_string($conn, $imp_guide)."'";
    $res_act = mysqli_query($conn, $sql_act);
    if ($row_act = mysqli_fetch_assoc($res_act)) {
        $activity_id = $row_act['activity_id'];

        $q_sql = "SELECT text, source 
                  FROM quote 
                  WHERE activity_id_for_quote='$activity_id' 
                  LIMIT 1";
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




$motivational_msg = "Focus on improving your weak activities and complete missed ones!";

$sql_check = "SELECT report_id FROM monthly_report WHERE user_id='$uid' AND month='$month' LIMIT 1";
$res_check = mysqli_query($conn, $sql_check);

if(mysqli_num_rows($res_check) > 0){
    $row = mysqli_fetch_assoc($res_check);
    $report_id = $row['report_id'];
    $sql_upd = "
        UPDATE monthly_report 
        SET total_points='$total_points',
            motivational_msg='".mysqli_real_escape_string($conn, $motivational_msg)."',
            improvement_status='".mysqli_real_escape_string($conn, $imp_guide)."',
            missed_activity_summary='".mysqli_real_escape_string($conn, $missed_summary)."'
        WHERE report_id='$report_id'
    ";
    mysqli_query($conn, $sql_upd);
} else {
    $sql_ins = "
        INSERT INTO monthly_report (user_id, month, total_points, motivational_msg, improvement_status, missed_activity_summary)
        VALUES ('$uid', '$month', '$total_points', '".mysqli_real_escape_string($conn, $motivational_msg)."', '".mysqli_real_escape_string($conn, $imp_guide)."', '".mysqli_real_escape_string($conn, $missed_summary)."')
    ";
    mysqli_query($conn, $sql_ins);
	$report_id = mysqli_insert_id($conn);
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Monthly Report</title>
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
form input[type="month"] { width:200px; }
blockquote { font-style: italic; color:#f0e68c; }
</style>
</head>
<body>

<div class="navbar">
<h1>Monthly Report</h1>
<div>
<a href="activity.php" class="btn btn-back">Activities</a>
<a href="signout.php" class="btn btn-signout">Sign Out</a>
</div>
</div>

<div class="container">
<form method="POST">
    <label for="month">Select Month:</label>
    <input type="month" name="month" value="<?php echo htmlspecialchars($month); ?>" required>
    <br>
    <button type="submit" class="btn btn-back">View</button>
</form>

<p><strong>Report for:</strong> <?php echo htmlspecialchars($month); ?></p>

<p><strong>Total Points:</strong> <?php echo $total_points; ?></p>

<?php if(!empty($imp_guide)): ?>
<p><strong>Activity Performed Worst:</strong> <?php echo htmlspecialchars($imp_guide); ?></p>
<?php endif; ?>

<?php if(!empty($missed_summary)): ?>
<p><strong>Missed Activities:</strong> <?php echo htmlspecialchars($missed_summary); ?></p>
<?php endif; ?>

<p><?php echo htmlspecialchars($motivational_msg); ?></p>

<div><?php echo $quotes_html; ?></div>

<p><a href="index.php" class="btn btn-back mt-3">Home</a></p>
</div>

<form method="POST" action="monthly_pdf.php" target="_blank" style="text-align:center; margin:20px 0;">
    <input type="hidden" name="month" value="<?php echo htmlspecialchars($month); ?>">
    <button type="submit" class="btn btn-signout">📄 Download PDF</button>
</form>


</body>
</html>

