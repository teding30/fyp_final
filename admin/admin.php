<?php
include("../include/config.php");
include("header.php");

session_start();
if ($_SESSION['role'] !== 'admin') {
    header('Location:../index.php');
    exit;
}

// Fetch all exams
$exams = $conn->query("SELECT * FROM exams");

// Statistics logic
$stats = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['view_stats'])) {
    $exam_id = $_POST['exam_id'];

    // Get the number of students who took the selected exam and calculate the average score
    $exam_stats_query = $conn->query(
        "SELECT COUNT(DISTINCT grades.student_id) AS student_count, AVG(grades.score) AS average_score 
         FROM grades 
         WHERE grades.exam_id = $exam_id"
    );

    $stats = $exam_stats_query->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <script src="script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Statistics Dashboard</title>
</head>
<body>
    

    <div class="foreground">
        <h1>Statistics Dashboard</h1>
        <section>
            <h2>Exam Statistics</h2>
            <form method="POST">
                <select name="exam_id" required>
                    <option value="" disabled selected>Select Exam</option>
                    <?php while ($exam = $exams->fetch_assoc()) { ?>
                        <option value="<?= $exam['id']; ?>"><?= htmlspecialchars($exam['name']); ?></option>
                    <?php } ?>
                </select>
                <button type="submit" name="view_stats">View Statistics</button>
            </form>

            <?php if (!empty($stats)) { ?>
                <h3>Statistics for Selected Exam</h3>
                <p><strong>Number of Students Took Exam:</strong> <?= $stats['student_count'] ?: 0; ?></p>
                <p><strong>Average Score:</strong> <?= $stats['average_score'] ? round($stats['average_score'], 2) : 0; ?>%</p>

                <canvas id="successChart" width="450" height="140"></canvas>
                <script>
                   const ctx = document.getElementById('successChart').getContext('2d');
                const averageScore = <?php echo isset($stats['average_score']) ? round($stats['average_score'], 2) : 0; ?>;

                    new Chart(ctx, {
                    type: 'bar',
                    data: {
                    labels: ['Average Success Rate'],
                    datasets: [{
                        label: 'Success Rate (%)',
                        data: [averageScore],
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                    },
                    options: {
                    scales: {
                        y: {
                        beginAtZero: true,
                        max: 100
                        }
                    }
                    }
                    });
                </script>
            <?php } ?>
        </section>
    </div>
</body>
</html>
