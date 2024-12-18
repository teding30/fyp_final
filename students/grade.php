<?php
session_start();
include("../include/config.php");
include("../include/header.php");

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: index.php");
    exit;
}

// Fetch grades based on search or fetch all
$sql = "SELECT g.id, g.student_id, g.exam_id, g.score, u.username AS student_name, e.name AS exam_name
        FROM grades g
        JOIN users u ON g.student_id = u.id
        JOIN exams e ON g.exam_id = e.id";



$grades = $conn->query($sql);

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
    

<table class="tables">
    <thead>
        <tr>
            <th>Grade ID</th>
            <th>Student ID</th>
            <th>Student Name</th>
            <th>Exam Name</th>
            <th>Score</th>
            
        </tr>
    </thead>
    <tbody>
        <?php if ($grades->num_rows > 0) { ?>
            <?php while ($grade = $grades->fetch_assoc()) { ?>
                <tr>
                    <td><?= $grade['id']; ?></td>
                    <td><?= $grade['student_id']; ?></td>
                    <td><?= htmlspecialchars($grade['student_name']); ?></td>
                    <td><?= htmlspecialchars($grade['exam_name']); ?></td>
                    <td><?= $grade['score']; ?></td>
                    
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="6">No grades found.</td>
            </tr>
        <?php } ?>
    </tbody>
</table>
    
</body>
</html>
