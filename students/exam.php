<?php
session_start();
include("../include/config.php");
include("../include/header.php");

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: index.php");
    exit;
}

// Fetch student department
$user_id = $_SESSION['user_id'];
$student = $conn->query("SELECT department FROM users WHERE id = $user_id")->fetch_assoc();
$student_department = $student['department'];

// Fetch  exams for the student's department
$exams = $conn->query("
    SELECT e.id AS exam_id, e.name AS exam_name, c.name AS course_name, e.date, e.time, e.duration 
    FROM exams e 
    JOIN courses c ON e.course_id = c.id 
    WHERE c.department = '$student_department' AND e.date >= CURDATE()
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal</title>
    <link rel="stylesheet" href="styles.css">
    
</head>
<body>
    
<div class="foreground">
    <h2>Welcome, Students!</h2>

  
    <h3>Available Exams</h3>
    <table>
        <thead>
            <tr>
                <th>Course</th>
                <th>Exam</th>
                <th>Date</th>
                <th>Time</th>
                <th>Duration</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($exam = $exams->fetch_assoc()) { ?>
                <tr>
                    <td><?= $exam['course_name']; ?></td>
                    <td><?= $exam['exam_name']; ?></td>
                    <td><?= $exam['date']; ?></td>
                    <td><?= $exam['time']; ?></td>
                    <td><?= $exam['duration']; ?> minutes</td>
                    <td>
                        <a href="take_exam.php?exam_id=<?= $exam['exam_id']; ?>" class="btn">Take Exam</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    </div>
</body>
</html>
