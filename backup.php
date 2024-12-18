<?php
include("../include/config.php");
include("header.php");
session_start();

// Check if the user is an admin
if ($_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Handle Create Department
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_department'])) {
    $department_name = $_POST['department_name'];
    $check_dept = $conn->query("SELECT * FROM departments WHERE name='$department_name'");
    if ($check_dept->num_rows > 0) {
        $error = "Department already exists.";
    } else {
        $stmt = $conn->prepare("INSERT INTO departments (name) VALUES (?)");
        $stmt->bind_param("s", $department_name);
        $stmt->execute() ? $success = "Department created successfully!" : $error = "Failed to create department.";
    }
}

// Handle Create Course
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_course'])) {
    $course_name = $_POST['course_name'];
    $department_id = $_POST['department_id']; // Get the department ID from the dropdown menu

    // Validate department exists
    $dept_check = $conn->query("SELECT * FROM departments WHERE id = $department_id");
    if ($dept_check->num_rows === 0) {
        $error = "Selected department does not exist.";
    } else {
        // Insert the course with the selected department
        $stmt = $conn->prepare("INSERT INTO courses (name, department) VALUES (?, ?)");
        $stmt->bind_param("si", $course_name, $department_id);

        if ($stmt->execute()) {
            $success = "Course created successfully!";
        } else {
            $error = "Failed to create course.";
        }
    }
}

// Fetch all departments for the dropdown
$departments = $conn->query("SELECT * FROM departments");

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

// Handle User Management (CRUD)
// Add a new user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_user'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role'];
    $department = $_POST['department'];
    $stmt = $conn->prepare("INSERT INTO users (username, password, role, department) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $password, $role, $department);
    $stmt->execute() ? $success = "User created successfully!" : $error = "Failed to create user.";
}

// Delete a user
if (isset($_GET['delete_user'])) {
    $user_id = $_GET['delete_user'];
    $conn->query("DELETE FROM users WHERE id = $user_id") ? $success = "User deleted successfully!" : $error = "Failed to delete user.";
}

// Fetch existing data
$departments = $conn->query("SELECT * FROM departments");
$courses = $conn->query("SELECT * FROM courses");
$users = $conn->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Admin Dashboard</h1>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>

    <!-- Create Department -->
    <section>
        <h2>Create Department</h2>
        <form method="POST">
            <input type="text" name="department_name" placeholder="Department Name" required>
            <button type="submit" name="create_department">Create Department</button>
        </form>
        <section>
    <h2>Create Course</h2>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
    <form method="POST">
        <input type="text" name="course_name" placeholder="Course Name" required>
        <select name="department_id" required>
            <option value="" disabled selected>Select Department</option>
            <?php while ($department = $departments->fetch_assoc()) { ?>
                <option value="<?= $department['name']; ?>"><?= $department['name']; ?></option>
            <?php } ?>
        </select>
        <button type="submit" name="create_course">Create Course</button>
    </form>
    <section>
  
       <section>
        <h2>Create Exam</h2>
        <form method="POST">
            <input type="text" name="exam_name" placeholder="Exam Name" required>
            <select name="course_id" required>
                <option value="" disabled selected>Select Course</option>
                <?php while ($course = $courses->fetch_assoc()) { ?>
                    <option value="<?= $course['id']; ?>"><?= $course['name']; ?></option>
                <?php } ?>
            </select>
            <input type="date" name="exam_date" required>
            <input type="time" name="exam_time" required>
            <input type="number" name="duration" placeholder="Duration (minutes)" required>
            <button type="submit" name="create_exam">Create Exam</button>
        </form>
    </section>

    <!-- Manage Users -->
    <section>
        <h2>Manage Users</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="role" required>
                <option value="" disabled selected>Select Role</option>
                <option value="admin">Admin</option>
                <option value="teacher">Teacher</option>
                <option value="student">Student</option>
            </select>
            <select name="department" required>
                <option value="" disabled selected>Select Department</option>
                <?php while ($department = $departments->fetch_assoc()) { ?>
                    <option value="<?= $department['name']; ?>"><?= $department['name']; ?></option>
                <?php } ?>
            </select>
            <button type="submit" name="create_user">Create User</button>
        </form>

        <!-- Existing Users -->
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Department</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $users->fetch_assoc()) { ?>
                    <tr>
                        <td><?= $user['username']; ?></td>
                        <td><?= $user['role']; ?></td>
                        <td><?= $user['department']; ?></td>
                        <td>
                            <a href="admin.php?delete_user=<?= $user['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </section>
</body>
</html>
