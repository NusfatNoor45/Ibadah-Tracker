<?php
session_start();
require_once('DBconnect.php');
require 'vendor/autoload.php';

use Dompdf\Dompdf;

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
$activity_progress = [];

while ($row = mysqli_fetch_assoc($res_compare)) {
    $earned = (int)$row['earned'];
    $max_point = (int)$row['max_point'];
    $percentage = $max_point > 0 ? ($earned / ($max_point * $days_count)) * 100 : 0;

    $activity_progress[] = [
        'name' => $row['name'],
        'earned' => $earned,
        'max' => $max_point * $days_count,
        'percentage' => $percentage
    ];

    if ($earned == 0) $missed_activities[] = $row['name'];

    if ($percentage < $lowest_percentage) {
        $lowest_percentage = $percentage;
        $worst_activity = $row['name'];
    }
}

$imp_guide = $worst_activity;
$missed_summary = implode(", ", $missed_activities);
$motivational_msg = "Keep pushing forward! Focus on improving weak areas and completing missed activities.";

$quotes_html = "";
if (!empty($imp_guide)) {
    $sql_act = "SELECT activity_id FROM activity WHERE name='".mysqli_real_escape_string($conn, $imp_guide)."'";
    $res_act = mysqli_query($conn, $sql_act);
    if ($row_act = mysqli_fetch_assoc($res_act)) {
        $activity_id = $row_act['activity_id'];
        $q_sql = "SELECT text, source FROM quote WHERE activity_id_for_quote='$activity_id' LIMIT 1";
        $q_res = mysqli_query($conn, $q_sql);
        if (mysqli_num_rows($q_res) > 0) {
            $quotes_html .= "<h2 style='text-align:center;color:#2E86C1;'>A Motivational Quote For You</h2>";
            while($q = mysqli_fetch_assoc($q_res)) {
                $quotes_html .= "<blockquote style='font-size:16px;font-style:italic;text-align:center;color:#555;margin:10px 0;'>".$q['text']."</blockquote>";
                $quotes_html .= "<p style='text-align:center;font-weight:bold;color:#555;'>- ".$q['source']."</p><br>";
            }
        }
    }
}

// --- HTML for PDF ---
$html = '
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Monthly Report</title>
<style>
body {
    font-family: Arial, sans-serif;
    padding: 30px;
    color: #333;
}
h1 { text-align:center; color:#1B4F72; }
h2 { margin-top:30px; }
.report-box {
    background:#f9f9f9;
    padding:20px;
    border-radius:15px;
    margin-bottom:20px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
}
p { font-size:16px; margin:8px 0; }
.progress-container {
    background: #ddd;
    border-radius: 25px;
    margin: 5px 0 15px 0;
}
.progress-bar {
    height: 20px;
    border-radius: 25px;
    text-align: right;
    padding-right: 5px;
    color: #fff;
    font-size: 12px;
    line-height: 20px;
}
.low { background: #E74C3C; }
.medium { background: #F1C40F; }
.high { background: #2ECC71; }
blockquote { font-style:italic; color:#2C3E50; margin:10px 0; }
</style>
</head>
<body>
    <h1>Monthly Report</h1>
    <div class="report-box">
        <p><strong>Month:</strong> '.$month.'</p>
        <p><strong>Total Points:</strong> '.$total_points.'</p>
        <p><strong>Worst Activity:</strong> '.$imp_guide.'</p>
        <p><strong>Missed Activities:</strong> '.$missed_summary.'</p>
        <p>'.$motivational_msg.'</p>
    </div>
    <!-- <h2>Activity Performance</h2> -->
';

/*
foreach ($activity_progress as $act) {
    $color_class = $act['percentage'] < 40 ? 'low' : ($act['percentage'] < 70 ? 'medium' : 'high');
    $html .= '
    <p>'.$act['name'].' ('.$act['earned'].'/'.$act['max'].' points)</p>
    <div class="progress-container">
        <div class="progress-bar '.$color_class.'" style="width:'.$act['percentage'].'%;">'.round($act['percentage']).'%</div>
    </div>
    ';
}*/

$html .= $quotes_html;
$html .= '</body></html>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$filename = "Monthly_Report_".$month.".pdf";
$dompdf->stream($filename, ["Attachment" => true]);
exit;
