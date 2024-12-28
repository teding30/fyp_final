<?php
include("../include/config.php");
include("header.php");

session_start();

// Check if the user is an admin
if ($_SESSION['role'] !== 'admin') {
    header('Location:../index.php');
    exit;
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
// approve a user 
if (isset($_GET['approve'])) {
    $user_id = $_GET['approve'];
    $conn->query("UPDATE users SET status = 'approved' WHERE id = $user_id;") ? $success = "User approved successfully!" : $error = "Failed to approve user.";
}
// revoke a user 
if (isset($_GET['revoke'])) {
    $user_id = $_GET['revoke'];
    $conn->query("UPDATE users SET status = 'revoke' WHERE id = $user_id;") ? $success = "User revoked successfully!" : $error = "Failed to revoked user.";
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
    <title>users</title>
    


    <div class="foreground">
    <h1>Manage Users</h1>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>

  

    <!-- Manage Users -->
    <section>
        
        <form method="POST">
            <div class="">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="role" required>
                <option value="" disabled selected>Select Role</option>
                <option value="admin">Admin</option>
                <option value="teacher">Teacher</option>
                <option value="student">Student</option>
            </select>
            <select name="department" class="dropdown" required>
                <option value="" disabled selected>Select Department</option>
                <?php while ($department = $departments->fetch_assoc()) { ?>
                    <option value="<?= $department['name']; ?>"><?= $department['name']; ?></option>
                <?php } ?>
            </select>
            <button type="submit" name="create_user">Create User</button>
            </div>
        </form>

        <!-- Existing Users -->
        <table >
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
                            <a href="users.php?delete_user=<?= $user['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                            <a href="users.php?approve=<?= $user['id']; ?>" onclick="return confirm('Are you sure?')">Approve</a>
                            <a href="users.php?revoke=<?= $user['id']; ?>" onclick="return confirm('Are you sure?')">Revoke</a>
                        </td>
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
