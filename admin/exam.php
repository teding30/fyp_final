<?php
include("../include/config.php");

session_start();
if ($_SESSION['role'] !== 'admin') {
    header('Location:../index.php');
    exit;
}

// Handle Create Exam
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_exam'])) {
    $exam_name = $_POST['exam_name'];
    $course_id = $_POST['course_id'];
    $exam_date = $_POST['exam_date'];
    $exam_time = $_POST['exam_time'];
    $duration = $_POST['duration'];
    $stmt = $conn->prepare("INSERT INTO exams (name, course_id, date, time, duration) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sissi", $exam_name, $course_id, $exam_date, $exam_time, $duration);
    $stmt->execute() ? $success = "Exam created successfully!" : $error = "Failed to create exam.";
}

// Fetch existing data
$departments = $conn->query("SELECT * FROM departments");
$courses = $conn->query("SELECT * FROM courses");
$users = $conn->query("SELECT * FROM users");
// Fetch all exams and their associated course names
$exams = $conn->query(
    "SELECT exams.id, exams.name AS exam_name, courses.name AS course_name, exams.date, exams.time, exams.duration 
     FROM exams 
     JOIN courses ON exams.course_id = courses.id"
);

// Handle delete action
if (isset($_GET['delete_exam'])) {
    $exam_id = $_GET['delete_exam'];

    // Delete the exam from the database
    $conn->query("DELETE FROM exams WHERE id = $exam_id");

    // Redirect to refresh the page
    header("Location: exam.php");
    exit;
}
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
                <a href="admin.php" >Home</a>
                <a href="exam.php" >Exams</a>
                <a href="courses.php" >Courses</a>
                <a href="grade.php" >Grades</a>
                <a href="users.php" >Users</a>
                <a href="#" onclick="confirmLogout()">Logout</a>
                </div>

    </div>


    <div class="foreground">
    <h1>Manage Exam</h1>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>

   
<h2>Create Exam</h2>
   
   <form method="POST">
       <input type="text" name="exam_name" placeholder="Exam Name" required>
       <select name="course_id" required>
           <option value="" disabled selected>Select Course</option>
           <?php while ($course = $courses->fetch_assoc()) { ?>
               <option value="<?= $course['id']; ?>"><?= $course['name']; ?></option>
           <?php } ?>
       </select>
       <input type="date" name="exam_date" required><br>
       <input type="time" name="exam_time" required>
       <input type="number" name="duration" placeholder="Duration (minutes)" required>
       <button type="submit" name="create_exam">Create Exam</button>
   </form>


<h2>All Exams</h2>
            <table>
                <thead>
                    <tr>
                        <th>Exam ID</th>
                        <th>Exam Name</th>
                        <th>Course Name</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Duration (minutes)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($exams->num_rows > 0) {
                        while ($exam = $exams->fetch_assoc()) {
                    ?>
                        <tr>
                            <td><?= $exam['id']; ?></td>
                            <td><?= htmlspecialchars($exam['exam_name']); ?></td>
                            <td><?= htmlspecialchars($exam['course_name']); ?></td>
                            <td><?= $exam['date']; ?></td>
                            <td><?= $exam['time']; ?></td>
                            <td><?= $exam['duration']; ?></td>
                            <td>
                                <a href="exam.php?delete_exam=<?= $exam['id']; ?>" onclick="return confirm('Are you sure you want to delete this exam?');">Delete</a>
                            </td>
                        </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='7'>No exams found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
    </div>

</body>
</html>
