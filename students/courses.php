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

// Get the student's department (replace with actual authentication)
$student_department = $student_department; 

// Fetch materials for the student's department
$sql = "SELECT file_name, file_path FROM materials
        INNER JOIN courses ON materials.course_id = courses.id
        WHERE courses.department = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_department);
$stmt->execute();
$result = $stmt->get_result();
// Fetch courses  for the student's department
$courses = $conn->query("SELECT * FROM courses WHERE department = '$student_department'");


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

<h3>Your Courses</h3>
<ul>
    <?php while ($course = $courses->fetch_assoc()) { ?>
        <li><?= $course['name']; ?></li>
    <?php } ?>
</ul>
<h2>Materials for Your Department</h2>
    <table>
        <thead>
            <tr>
                <th>File Name</th>
                <th>Download</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . $row['file_name'] . '</td>';
                echo '<td><a href="' . htmlspecialchars($row['file_path']) . '">Download</a></td>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
    </div>
</body>
</html>
