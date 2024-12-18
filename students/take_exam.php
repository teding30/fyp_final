<?php
session_start();
include("../include/config.php");
include("../include/header.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: index.php");
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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $score = 0;
    $total_questions = $questions->num_rows;

    // Calculate the score based on submitted answers
    foreach ($_POST['answers'] as $question_id => $student_answer) {
        $correct_option = $conn->query("SELECT correct_option FROM questions WHERE id = $question_id")->fetch_assoc()['correct_option'];
        if ($student_answer === $correct_option) {
            $score++;
        }
    }

    // Convert score to percentage
    $final_score = ($score / $total_questions) * 100;

    // Insert the score into the grades table
    $conn->query("INSERT INTO grades (student_id, exam_id, score) VALUES ($user_id, $exam_id, $final_score)");

    // Redirect to the success page
    header("Location: exam_success.php?score=$final_score");
    exit;
}

// If time runs out or student doesn't submit, score defaults to 0
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['timeout'])) {
    $conn->query("INSERT INTO grades (student_id, exam_id, score) VALUES ($user_id, $exam_id, 0)");
    header("Location: exam_success.php?score=0");
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
    <style>
        .timer { font-size: 20px; color: red; }
        form { margin-top: 20px; }
        .question { margin-bottom: 20px; }
    </style>
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

    <form method="POST" id="examForm">
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


