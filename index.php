<?php
require 'include/config.php';
session_start();

$error = $success = "";


// Handle Signup
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = "student";
    $department = $_POST['department'];

    // Check if the username already exists
    $check_user = $conn->query("SELECT * FROM users WHERE username='$username'");
    if ($check_user->num_rows > 0) {
        $error = "Username already exists. Please choose a different one.";
    } else {
        // If the username is unique, insert the new user into the database
        $stmt = $conn->prepare("INSERT INTO users (username, password, role, department) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $password, $role, $department);

        if ($stmt->execute()) {
            $success = "Signup successful! Please log in.";
        } else {
            $error = "Signup failed. Please try again.";
        }
    }
}

// Fetch all departments from the database
$departments = $conn->query("SELECT DISTINCT name FROM departments");


// Handle Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE username='$username'");
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

   
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['department'] = $user['department'];
            $_SESSION['status']=$user['status'];

            if (empty($user['status']) || $user['status'] === 'revoked') {
                header('Location: deactivated.php');
                
            }
            else{
            // Redirect based on role
            if ($user['role'] === 'admin') {
                header('Location: admin/admin.php');
            } elseif ($user['role'] === 'teacher') {
                header('Location: teacher/teacher.php');
            } else {
                header('Location: students/student.php');
            }}
            exit;
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User not found.";
    }
}

// Fetch Departments for Signup
$departments = $conn->query("SELECT * FROM departments");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register & Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container" id="signup" style="display:none;">
    <h2>Signup</h2>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <label for="username">username</label>
        <input type="password" name="password" placeholder="Password" required>
        <label for="password">password</label>
        
        <select name="department" required class="">
            <option value="" disabled selected>Select Department</option>
            <?php while ($department = $departments->fetch_assoc()) { ?>
                <option value="<?= $department['name']; ?>"><?= $department['name']; ?></option>
            <?php } ?>
        </select>
        <button type="submit" name="signup">Signup</button>
    </form>
        <p>Already Have Account ?</p>
        <button id="signInButton">Sign In</button>
      </div>
   

    <div class="container" id="signIn">
            <h2>Login</h2>
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            <form method="POST">
                <label for="username">username</label>
                <input type="text" name="username" placeholder="Username" required>
                <label for="password">password</label>
                <input type="password" name="password" placeholder="Password" required>
                
                <button type="submit" name="login">Login</button> 
                
        </form>
        
        <p>Don't have account yet?</p>
        <button id="signUpButton">Sign Up</button>
        
      </div>

      


      <script src="script.js"></script>
</body>

</html>
