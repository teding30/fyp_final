<?php
include("../include/config.php");

session_start();

// Check if the user is an admin
if ($_SESSION['role'] !== 'teacher') {
    header('Location:../index.php');
    exit;
}



// Handle User Management (CRUD)


// Delete a user
if (isset($_GET['delete_user'])) {
    $user_id = $_GET['delete_user'];
    $conn->query("DELETE FROM users WHERE id = $user_id") ? $success = "User deleted successfully!" : $error = "Failed to delete user.";
}

// Fetch existing data
$departments = $conn->query("SELECT * FROM departments ");
$courses = $conn->query("SELECT * FROM courses");
$users = $conn->query("SELECT * FROM users");
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
    <h1>Manage Users</h1>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>

  

    <!-- Manage Users -->
    <section>
        
       

        <!-- Existing Users -->
        <table >
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Department</th>
                    
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $users->fetch_assoc()) { ?>
                    <tr>
                        <td><?= $user['username']; ?></td>
                        <td><?= $user['role']; ?></td>
                        <td><?= $user['department']; ?></td>
                       
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </section>
    </div>
</head>
<body>
    
</body>
</html>
