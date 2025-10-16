<?php
session_start();
require_once('DBconnect.php');

if (isset($_POST['record_id']) && isset($_POST['report_id']) && isset($_POST['text']) && isset($_POST['source'])) {
    $record_id = $_POST['record_id'];
    $report_id = $_POST['report_id'];
    $text = mysqli_real_escape_string($conn, $_POST['text']);
    $source = mysqli_real_escape_string($conn, $_POST['source']);

    $sql = "INSERT INTO quotes (record_id, report_id, text, source) 
            VALUES ('$record_id', '$report_id', '$text', '$source')";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        header("Location: quote.php?msg=added");
        exit;
    } else {
        header("Location: quote.php?error=failed");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Quotes</title>
</head>
<body>
  <h1>Add Quote</h1>
  <form method="POST" action="quote.php">
    <label>Record ID:</label>
    <input type="number" name="record_id" required><br><br>

    <label>Report ID (Monthly):</label>
    <input type="number" name="report_id" required><br><br>

    <label>Quote Text:</label>
    <textarea name="text" required></textarea><br><br>

    <label>Source:</label>
    <input type="text" name="source"><br><br>

    <button type="submit">Add Quote</button>
  </form>
</body>
</html>
