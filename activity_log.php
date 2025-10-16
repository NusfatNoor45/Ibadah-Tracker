<?php
session_start();
require_once('DBconnect.php');

if (!isset($_SESSION['user_id'])) {
    die("Please login first.");
}

$uid = $_SESSION['user_id'];
$selected_date = $_GET['date'] ?? date('Y-m-d');

$q = "SELECT record_id FROM daily_record WHERE user_id = '$uid' AND `date` = '$selected_date' LIMIT 1";
$res = mysqli_query($conn, $q);
$record_id = null;
if ($res && mysqli_num_rows($res) > 0) {
    $row = mysqli_fetch_assoc($res);
    $record_id = $row['record_id'];
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Activity Log</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
  <h1>Activity Log</h1>

  <form method="get" class="mb-3">
    <label>Select date: <input type="date" name="date" value="<?php echo htmlspecialchars($selected_date); ?>"></label>
    <button class="btn btn-sm btn-primary" type="submit">Show</button>
  </form>

<?php
if (!$record_id) {
    echo "<div class='alert alert-warning'>No daily record found for $selected_date. Please create it in Daily Record page first.</div>";
} else {

    $sql = "
      SELECT al.log_id, al.activity_id, COALESCE(a.name,'Activity') AS activity_name,
             al.`value`, al.points_earned
      FROM activity_log al
      LEFT JOIN activity a ON al.activity_id = a.activity_id
      WHERE al.record_id = '$record_id'
      ORDER BY al.log_id DESC
    ";
    $r = mysqli_query($conn, $sql);

    echo "<table class='table table-striped'><thead><tr>
            <th>Activity</th><th>Value</th><th>Points</th>
          </tr></thead><tbody>";
    $sum = 0;
    while ($row = mysqli_fetch_assoc($r)) {
        $sum += (int)$row['points_earned'];
        echo "<tr>";
        echo "<td>".htmlspecialchars($row['activity_name'])."</td>";
        echo "<td>".htmlspecialchars($row['value'])."</td>";
        echo "<td>".htmlspecialchars($row['points_earned'])."</td>";
        
        echo "</tr>";
    }
    echo "</tbody></table>";

    echo "<div class='alert alert-info'>Total points for <strong>$selected_date</strong>: <strong>$sum</strong></div>";

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

    $update_q = "
        UPDATE daily_record 
        SET improvement_guidance = '".mysqli_real_escape_string($conn, $guidance_text)."'
        WHERE record_id = '$record_id'
    ";
    mysqli_query($conn, $update_q);

    echo "<div class='alert alert-warning'>
            Improvement Guidance: <strong>$guidance_text</strong>
          </div>";

    $qtot = "
      SELECT SUM(al.points_earned) AS total_all
      FROM activity_log al
      JOIN daily_record dr ON al.record_id = dr.record_id
      WHERE dr.user_id = '$uid'
    ";
    $rtot = mysqli_query($conn, $qtot);
    $total_all = mysqli_fetch_assoc($rtot)['total_all'] ?? 0;
    $total_all = (int)$total_all;
    echo "<div class='alert alert-success'>All-time total points: <strong>$total_all</strong></div>";
}
?>

  <p><a href="activity.php" class="btn btn-secondary">Back to Activities</a></p>
</body>
</html>
