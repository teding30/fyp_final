<?php
include("../include/config.php");

session_start();

if ($_SESSION['role'] !== 'teacher') {
    header('Location:../ index.php');
    exit;
}


// Handle delete action
if (isset($_GET['delete_grade'])) {
    $grade_id = $_GET['delete_grade'];
    $conn->query("DELETE FROM grades WHERE id = $grade_id");
    header("Location: admin_grades.php");
    exit;
}

// Handle search
$search_student_id = isset($_GET['search']) ? $_GET['search'] : '';

// Fetch grades based on search or fetch all
$sql = "SELECT g.id, g.student_id, g.exam_id, g.score, u.username AS student_name, e.name AS exam_name
        FROM grades g
        JOIN users u ON g.student_id = u.id
        JOIN exams e ON g.exam_id = e.id";

if ($search_student_id) {
    $sql .= " WHERE g.student_id = $search_student_id";
}

$grades = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <script src="script.js"></script>
    <title>Document</title>
    
    <div class="sidebar">
    <img class="logo" src="../img/logo.png" alt="">
    
                <div class="links">
                <a href="teacher.php" >Home</a>
                <a href="exam.php" >Exams</a>
                <a href="courses.php" >Courses</a>
                <a href="grade.php" >Grades</a>
                <a href="users.php" >Users</a>
                <a href="#" onclick="confirmLogout()">Logout</a>
                </div>

    </div>


    <div class="foreground">
    <h1>Manage Grades</h1>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>

    

<form method="GET" class="search-bar">
    <input type="text" name="search" placeholder="Enter Student ID" value="<?= htmlspecialchars($search_student_id); ?>">
    <button type="submit">Search</button>
</form>

<table class="tables">
    <thead>
        <tr>
            <th>Grade ID</th>
            <th>Student ID</th>
            <th>Student Name</th>
            <th>Exam Name</th>
            <th>Score</th>
            <
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


  
    </div>
</head>
<body>
    
</body>
</html>