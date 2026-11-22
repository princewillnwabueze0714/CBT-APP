<?php
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = trim($_POST['student_id'] ?? '');

    if (!empty($student_id)) {
        $stmt = $conn->prepare("SELECT s.*, p.program_name FROM students s JOIN programs p ON s.program_key = p.program_key WHERE s.student_id = ?");
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['student'] = $result->fetch_assoc();
            header("Location: portal.php");
            exit;
        } else {
            $error = "Invalid Student ID! Please verify and try again.";
        }
    } else {
        $error = "Please enter your Student ID.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Jerro Computer School Limited - CBT Portal</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #0c1821; color: #fff; margin: 0; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .card { background: #1b2a38; padding: 35px; border-radius: 12px; width: 100%; max-width: 420px; box-shadow: 0 8px 24px rgba(0,0,0,0.5); text-align: center; }
        .logo { font-size: 22px; font-weight: bold; color: #38bdf8; margin-bottom: 20px; text-transform: uppercase; letter-spacing: 1px; }
        input[type="text"] { width: 100%; padding: 12px; margin: 15px 0; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: #fff; box-sizing: border-box; }
        .btn-submit { width: 100%; padding: 12px; background: #0284c7; border: none; color: white; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 16px; }
        .btn-submit:hover { background: #0369a1; }
        .admin-link { display: inline-block; margin-top: 25px; padding: 10px 20px; background: #334155; color: #cbd5e1; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: 600; border: 1px solid #475569; }
        .admin-link:hover { background: #475569; color: #fff; }
        .error { color: #f87171; margin-bottom: 15px; font-size: 14px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">Jerro Computer School Limited</div>
        <h3>Student CBT Login</h3>
        <?php if($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="text" name="student_id" placeholder="Enter Student ID (e.g., 2026085825)" required>
            <button type="submit" class="btn-submit">Start Assessment</button>
        </form>

        <hr style="border: 0; border-top: 1px solid #334155; margin-top: 30px;">
        <a href="admin_login.php" class="admin-link">🔑 Admin Portal</a>
    </div>
</body>
</html>