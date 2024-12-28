<?php
session_start();
include("../include/config.php");
include("../include/header.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit;
}

if (!isset($_GET['exam_id'])) {
    header("Location: student.php");
    exit;
}

$exam_id = $_GET['exam_id'];
$user_id = $_SESSION['user_id'];

// Fetch exam duration
$exam = $conn->query("SELECT duration FROM exams WHERE id = $exam_id")->fetch_assoc();
if (!$exam) {
    header("Location: student_portal.php");
    exit;
}
$exam_duration = $exam['duration'];

// Fetch questions for the exam
$questions = $conn->query("SELECT id, question_text, option_a, option_b, option_c, option_d FROM questions WHERE exam_id = $exam_id");
if ($questions->num_rows === 0) {
    header("Location: take_exam.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Take Exam</title>
    
    <script>
        let timeRemaining = <?= $exam_duration * 60; ?>; // Duration in seconds

        function startTimer() {
            const timerDisplay = document.getElementById('timer');
            const timerInterval = setInterval(() => {
                const minutes = Math.floor(timeRemaining / 60);
                const seconds = timeRemaining % 60;
                timerDisplay.textContent = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;

                if (timeRemaining <= 0) {
                    clearInterval(timerInterval);
                    alert("Time is up! Submitting the exam.");
                    document.getElementById('examForm').submit();
                }

                timeRemaining--;
            }, 1000);
        }

        window.onload = startTimer;
    </script>
</head>
<body>
    <h2>Take Exam</h2>
    <div class="timer">Time Remaining: <span id="timer"></span></div>

    <form method="POST" action="exam_success.php" id="examForm">
        <input type="hidden" name="exam_id" value="<?= $exam_id; ?>">
        <?php while ($question = $questions->fetch_assoc()) { ?>
            <div class="question">
                <p><strong><?= $question['question_text']; ?></strong></p>
                <label><input type="radio" name="answers[<?= $question['id']; ?>]" value="A" required> <?= $question['option_a']; ?></label><br>
                <label><input type="radio" name="answers[<?= $question['id']; ?>]" value="B" required> <?= $question['option_b']; ?></label><br>
                <label><input type="radio" name="answers[<?= $question['id']; ?>]" value="C" required> <?= $question['option_c']; ?></label><br>
                <label><input type="radio" name="answers[<?= $question['id']; ?>]" value="D" required> <?= $question['option_d']; ?></label>
            </div>
        <?php } ?>
        <button type="submit">Submit Exam</button>
    </form>
</body>
</html>
