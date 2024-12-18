<?php
include("../include/config.php");

session_start();
if ($_SESSION['role'] !== 'teacher') {
    header('Location:../index.php');
    exit;
}

// Fetch all exams for the dropdown menu
$exams = $conn->query("SELECT id, name FROM exams");

// Handle question upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_question'])) {
    $exam_id = $_POST['exam_id'];
    $question_text = $_POST['question_text'];
    $option_a = $_POST['option_a'];
    $option_b = $_POST['option_b'];
    $option_c = $_POST['option_c'];
    $option_d = $_POST['option_d'];
    $correct_option = $_POST['correct_option'];

    $stmt = $conn->prepare("INSERT INTO questions (exam_id, question_text, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $exam_id, $question_text, $option_a, $option_b, $option_c, $option_d, $correct_option);

    if ($stmt->execute()) {
        $success = "Question added successfully!";
    } else {
        $error = "Failed to add question.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <script src="script.js"></script>
    <title>Manage Exams</title>
</head>
<body>
    <div class="sidebar">
        <img class="logo" src="../img/logo.png" alt="">
        <div class="links">
            <a href="teacher.php">Home</a>
            <a href="exam.php">Exams</a>
            <a href="courses.php">Courses</a>
            <a href="grade.php">Grades</a>
            <a href="users.php">Users</a>
            <a href="#" onclick="confirmLogout()">Logout</a>
        </div>
    </div>

    <div class="foreground">
        <h1>Manage Questions</h1>

        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>

        <section>
            <h2>Add Question</h2>
            <form method="POST">
                <label for="exam_id">Select Exam:</label>
                <select name="exam_id" id="exam_id" required>
                    <option value="" disabled selected>Select Exam</option>
                    <?php while ($exam = $exams->fetch_assoc()) { ?>
                        <option value="<?= $exam['id']; ?>"><?= htmlspecialchars($exam['name']); ?></option>
                    <?php } ?>
                </select>

                <label for="question_text">Question Text:</label>
                <textarea name="question_text" id="question_text" required></textarea><br>

                <label for="option_a">Option A:</label>
                <input type="text" name="option_a" id="option_a" required>

                <label for="option_b">Option B:</label>
                <input type="text" name="option_b" id="option_b" required><br>

                <label for="option_c">Option C:</label>
                <input type="text" name="option_c" id="option_c" required>

                <label for="option_d">Option D:</label>
                <input type="text" name="option_d" id="option_d" required><br>

                <label for="correct_option">Correct Option:</label>
                <select name="correct_option" id="correct_option" required>
                    <option value="" disabled selected>Select Correct Option</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>

                <button type="submit" name="add_question">Add Question</button>
            </form>
        </section>
    </div>
</body>
</html>
