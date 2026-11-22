<?php
require_once 'config.php';

// Verify Admin Session
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

$submission_id = isset($_GET['sub_id']) ? intval($_GET['sub_id']) : 0;

// Fetch Submission Details & Student Info
$stmt = $conn->prepare("
    SELECT s.*, st.full_name, st.program_key, p.program_name 
    FROM submissions s 
    JOIN students st ON s.student_id = st.student_id 
    JOIN programs p ON st.program_key = p.program_key 
    WHERE s.id = ?
");
$stmt->bind_param("i", $submission_id);
$stmt->execute();
$submission = $stmt->get_result()->fetch_assoc();

if (!$submission) {
    die("Submission not found.");
}

// Fetch Detailed Question Analysis
$q_stmt = $conn->prepare("
    SELECT sa.*, q.question_text, q.option_a, q.option_b, q.option_c, q.option_d, q.correct_option, sub.subject_name 
    FROM submission_answers sa 
    JOIN questions q ON sa.question_id = q.id 
    JOIN subjects sub ON q.subject_id = sub.id 
    WHERE sa.submission_id = ?
    ORDER BY sub.subject_name ASC
");
$q_stmt->bind_param("i", $submission_id);
$q_stmt->execute();
$details = $q_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Diagnostic View - Jerro Computer School Limited</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8fafc; color: #1e293b; margin: 0; padding: 30px; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .header-info { background: #e0f2fe; border-left: 5px solid #0284c7; padding: 15px; border-radius: 4px; margin-bottom: 25px; }
        .card { border: 1px solid #cbd5e1; border-radius: 6px; padding: 15px; margin-bottom: 15px; }
        .card.failed { border-left: 5px solid #ef4444; background: #fef2f2; }
        .card.passed { border-left: 5px solid #22c55e; background: #f0fdf4; }
        .badge { padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 12px; display: inline-block; }
        .badge-fail { background: #fee2e2; color: #991b1b; }
        .badge-pass { background: #dcfce7; color: #166534; }
        .btn-back { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #1e293b; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Admin Diagnostic Analysis</h2>
        <div class="header-info">
            <h3 style="margin-top:0;"><?php echo htmlspecialchars($submission['full_name']); ?> (ID: <?php echo htmlspecialchars($submission['student_id']); ?>)</h3>
            <p><strong>Program:</strong> <?php echo htmlspecialchars($submission['program_name']); ?> | <strong>Assessment:</strong> <?php echo strtoupper($submission['type']); ?></p>
            <p><strong>Cumulative Score:</strong> <?php echo $submission['total_score']; ?> / <?php echo $submission['max_score']; ?> (<?php echo number_format($submission['percentage'], 1); ?>%)</p>
        </div>

        <h3>Question Breakdown</h3>

        <?php foreach ($details as $index => $item): ?>
            <div class="card <?php echo $item['is_correct'] ? 'passed' : 'failed'; ?>">
                <small style="color: #64748b; font-weight: bold;"><?php echo htmlspecialchars($item['subject_name']); ?> - Question <?php echo ($index + 1); ?></small>
                <p style="font-size: 16px; font-weight: 600; margin: 8px 0;"><?php echo htmlspecialchars($item['question_text']); ?></p>
                
                <p style="margin: 4px 0;">A) <?php echo htmlspecialchars($item['option_a']); ?></p>
                <p style="margin: 4px 0;">B) <?php echo htmlspecialchars($item['option_b']); ?></p>
                <p style="margin: 4px 0;">C) <?php echo htmlspecialchars($item['option_c']); ?></p>
                <p style="margin: 4px 0;">D) <?php echo htmlspecialchars($item['option_d']); ?></p>

                <hr style="border:0; border-top: 1px solid #e2e8f0; margin: 12px 0;">

                <p style="margin: 0;">
                    <strong>Student Response:</strong> 
                    <?php if ($item['user_answer'] === '-'): ?>
                        <span class="badge badge-fail">Unanswered / Skipped</span>
                    <?php else: ?>
                        <code><?php echo $item['user_answer']; ?></code>
                    <?php endif; ?>
                    
                    | <strong>Correct Answer:</strong> <code><?php echo $item['correct_option']; ?></code>
                    
                    <span style="float: right;">
                        <?php if ($item['is_correct']): ?>
                            <span class="badge badge-pass">PASSED</span>
                        <?php else: ?>
                            <span class="badge badge-fail">FAILED</span>
                        <?php endif; ?>
                    </span>
                </p>
            </div>
        <?php endforeach; ?>

        <a href="admin.php" class="btn-back">← Back to Admin Leaderboard</a>
        <a href="logout.php" class="btn-back">Logout</a>
    </div>
</body>
</html>
