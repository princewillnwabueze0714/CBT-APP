<?php
require_once 'config.php';

if (!isset($_SESSION['student']) || !isset($_SESSION['last_submission_id'])) {
    header("Location: portal.php");
    exit;
}

$sub_id = $_SESSION['last_submission_id'];
$stmt = $conn->prepare("SELECT * FROM submissions WHERE id = ?");
$stmt->bind_param("i", $sub_id);
$stmt->execute();
$sub = $stmt->get_result()->fetch_assoc();

$student = $_SESSION['student'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cumulative Results - Jerro Computer School</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f1f5f9; color: #1e293b; margin: 0; padding: 40px; display: flex; justify-content: center; }
        .card { background: white; width: 100%; max-width: 600px; padding: 30px; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.1); text-align: center; }
        .score-circle { font-size: 42px; font-weight: bold; color: #2563eb; margin: 20px 0; }
        .status { font-weight: bold; text-transform: uppercase; padding: 6px 16px; border-radius: 20px; display: inline-block; }
        .pass { background: #dcfce7; color: #15803d; }
        .fail { background: #fee2e2; color: #b91c1c; }
        .btn { display: inline-block; margin-top: 20px; padding: 12px 24px; background: #1e293b; color: white; text-decoration: none; border-radius: 6px; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Jerro Computer School Limited</h2>
        <p>Official Assessment Result Statement</p>
        <hr>
        <h3>Candidate: <?php echo htmlspecialchars($student['full_name']); ?></h3>
        <p>Student ID: <?php echo htmlspecialchars($student['student_id']); ?></p>

        <div class="score-circle">
            <?php echo $sub['total_score']; ?> / <?php echo $sub['max_score']; ?>
            <div style="font-size: 18px; color: #64748b;"><?php echo number_format($sub['percentage'], 1); ?>% Cumulative</div>
        </div>

        <div>
            <?php if($sub['percentage'] >= 50): ?>
                <span class="status pass">Passed Assessment</span>
            <?php else: ?>
                <span class="status fail">Needs Improvement</span>
            <?php endif; ?>
        </div>

        <br>
        <a href="portal.php" class="btn">Return to Dashboard</a>
    </div>
</body>
</html>