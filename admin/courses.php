<?php
include("../include/config.php");
include("header.php");

session_start();
if ($_SESSION['role'] !== 'admin') {
    header('Location:../index.php');
    exit;
}
// Upload Material
if (isset($_POST['submit'])) {
    $course_id = $_POST['course_id'];
    $file = $_FILES['file'];

    $file_name = $_FILES['file']['name'];
    $file_tmp_name = $_FILES['file']['tmp_name'];
    $file_size = $_FILES['file']['size'];
    $file_error = $_FILES['file']['error'];

    $file_ext = explode('.', $file_name);
    $file_ext = strtolower(end($file_ext));

    $allowed_ext = array('pdf', 'ppt', 'docx', 'txt');

    if (in_array($file_ext, $allowed_ext)) {
        if ($file_error === 0) {
            $file_new_name = uniqid('', true) . '.' . $file_ext;
            $file_destination = '../uploads/' . $file_new_name;

            if (move_uploaded_file($file_tmp_name, $file_destination)) {
                $sql = "INSERT INTO materials (course_id, file_name, file_path) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iss", $course_id, $file_new_name, $file_destination);
                $stmt->execute();

                echo "File uploaded successfully!";
            } else {
                echo "Error uploading file!";
            }
        } else {
            echo "Error uploading file!";
        }
    } else {
        echo "Invalid file type!";
    }
}

// Delete Material
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $sql = "SELECT file_path FROM materials WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $file_path = $row['file_path'];

        if (unlink($file_path)) {
            $sql = "DELETE FROM materials WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();

            echo "File deleted successfully!";
        } else {
            echo "Error deleting file!";
        }
    } else {
        echo "File not found!";
    }
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
    <link rel="stylesheet" href="style.css">
    <script src="script.js"></script>
    <title>Document</title>
    
    <div class="foreground">
    <h1>Manage Courses</h1>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>

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
    <h2>Add Material</h2>
    <form action="courses.php" method="POST" enctype="multipart/form-data">
    <label for="course_id">Course ID:</label>
    <select name="course_id" required>
           <option value="" disabled selected>Select Course</option>
           <?php while ($course = $courses->fetch_assoc()) { ?>
               <option value="<?= $course['id']; ?>"><?= $course['name']; ?></option>
           <?php } ?>
       </select>

    <label for="file">File:</label>
    <input type="file" name="file" required>

    <button type="submit" name="submit">Upload</button>
</form>
    </div>
</head>

</html>