<?php
session_start();
include("../include/config.php");
include("../include/header.php");

if (!isset($_POST['exam_id']) || !isset($_POST['answers'])) {
    header("Location: student.php");
    exit;
}

$exam_id = $_POST['exam_id'];
$user_id = $_SESSION['user_id'];
$student_answers = $_POST['answers'];

// Fetch all questions for the exam
$questions = $conn->query("
    SELECT 
        id AS question_id, 
        question_text, 
        option_a, 
        option_b, 
        option_c, 
        option_d, 
        correct_option
    FROM questions
    WHERE exam_id = $exam_id
");

// Calculate score
$score = 0;
$total_questions = $questions->num_rows;

while ($question = $questions->fetch_assoc()) {
    $question_id = $question['question_id'];
    $correct_answer = $question['correct_option'];
    $student_answer = isset($student_answers[$question_id]) ? $student_answers[$question_id] : null;

    if ($student_answer === $correct_answer) {
        $score++;
    }
}

$final_score = ($score / $total_questions) * 100;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Exam Results</title>
    <style>
        .correct { color: green; font-weight: bold; }
        .wrong { color: red; font-weight: bold; }
    </style>
</head>
<body>
   
    <div class="container">
    <h2>Exam Completed!</h2>
    <p class="score">Your Score: <?= $final_score; ?>%</p>
    <h3>Review Questions</h3>

    <?php 
    $questions->data_seek(0);
    while ($question = $questions->fetch_assoc()) { 
        $question_id = $question['question_id'];
        $correct_answer = $question['correct_option'];
        $student_answer = isset($student_answers[$question_id]) ? $student_answers[$question_id] : null;
    ?>
        <div class="question-box">
            <p class="question-text"><?= $question['question_text']; ?></p>
            <ul>
                <li class="<?= $correct_answer === 'A' ? 'correct' : ($student_answer === 'A' ? 'wrong' : 'default'); ?>">A: <?= $question['option_a']; ?></li>
                <li class="<?= $correct_answer === 'B' ? 'correct' : ($student_answer === 'B' ? 'wrong' : 'default'); ?>">B: <?= $question['option_b']; ?></li>
                <li class="<?= $correct_answer === 'C' ? 'correct' : ($student_answer === 'C' ? 'wrong' : 'default'); ?>">C: <?= $question['option_c']; ?></li>
                <li class="<?= $correct_answer === 'D' ? 'correct' : ($student_answer === 'D' ? 'wrong' : 'default'); ?>">D: <?= $question['option_d']; ?></li>
            </ul>
            <div class="answers">
                <p>Your Answer: <span><?= $student_answer ?: 'No Answer'; ?></span></p>
                <p>Correct Answer: <span class="correct-answer"><?= $correct_answer; ?></span></p>
            </div>
        </div>
        
    <?php } ?>
    <a href="student.php">Go Back</a>
</div>

</body>
</html>
