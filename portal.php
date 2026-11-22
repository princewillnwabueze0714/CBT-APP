<?php
require_once 'config.php';

if (!isset($_SESSION['student'])) {
    header("Location: index.php");
    exit;
}

$student = $_SESSION['student'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Jerro Computer School</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8fafc; color: #334155; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        h2 { color: #1e293b; }
        .welcome { background: #e0f2fe; padding: 15px; border-left: 4px solid #0284c7; margin-bottom: 20px; border-radius: 4px; }
        .actions { display: flex; gap: 20px; margin-top: 20px; }
        .btn { flex: 1; padding: 20px; text-align: center; background: #2563eb; color: white; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 18px; }
        .btn-test { background: #0d9488; }
        .btn:hover { opacity: 0.9; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Jerro Computer School Limited</h2>
        <div class="welcome">
            <h3>Welcome, <?php echo htmlspecialchars($student['full_name']); ?>!</h3>
            <p><strong>ID:</strong> <?php echo htmlspecialchars($student['student_id']); ?></p>
            <p><strong>Program Enrolled:</strong> <?php echo htmlspecialchars($student['program_name']); ?></p>
        </div>

        <h4>Select Assessment Type to Begin:</h4>
        <div class="actions">
            <a href="exam.php?type=test" class="btn btn-test">Take Practice Test</a>
            <a href="exam.php?type=exam" class="btn">Take Semester Exam</a>
        </div>
        <br><br>
        <a href="logout.php">Logout</a>
    </div>
</body>
</html>