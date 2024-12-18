<?php
if (!isset($_GET['score'])) {
    header("Location: student_portal.php");
    exit;
}

$score = $_GET['score'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Completed</title>
</head>
<body>
    <h2>Exam Completed!</h2>
    <p>Your score: <?= $score; ?>%</p>
    <a href="student.php">Go Back</a>
</body>
</html>
