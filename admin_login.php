<?php
require_once 'config.php';

$error = '';

// Process Admin Login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = trim($_POST['password'] ?? '');

    // Check against official admin password
    if ($password === '120109') {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin.php");
        exit;
    } else {
        $error = "Incorrect Admin Password. Access Denied!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Jerro Computer School Limited</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: #0f172a; 
            color: #fff; 
            margin: 0; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            min-height: 100vh; 
        }
        .card { 
            background: #1e293b; 
            padding: 35px; 
            border-radius: 12px; 
            width: 100%; 
            max-width: 380px; 
            box-shadow: 0 8px 24px rgba(0,0,0,0.5); 
            text-align: center; 
            border: 1px solid #334155; 
        }
        .logo { 
            font-size: 20px; 
            font-weight: bold; 
            color: #38bdf8; 
            margin-bottom: 10px; 
            text-transform: uppercase; 
            letter-spacing: 1px; 
        }
        .subtitle {
            color: #94a3b8;
            font-size: 14px;
            margin-bottom: 25px;
        }
        input[type="password"] { 
            width: 100%; 
            padding: 12px; 
            margin: 15px 0 20px 0; 
            border-radius: 6px; 
            border: 1px solid #475569; 
            background: #0f172a; 
            color: #fff; 
            box-sizing: border-box; 
            font-size: 16px;
        }
        input[type="password"]:focus {
            outline: none;
            border-color: #38bdf8;
        }
        .btn-login { 
            width: 100%; 
            padding: 12px; 
            background: #0284c7; 
            border: none; 
            color: white; 
            border-radius: 6px; 
            font-weight: bold; 
            cursor: pointer; 
            font-size: 16px; 
            transition: background 0.2s;
        }
        .btn-login:hover { 
            background: #0369a1; 
        }
        .error { 
            background: #450a0a;
            color: #fca5a5; 
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #991b1b;
            margin-bottom: 20px; 
            font-size: 14px; 
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #94a3b8;
            text-decoration: none;
            font-size: 14px;
        }
        .back-link:hover {
            color: #38bdf8;
        }
    </style>
</head>
<body>

    <div class="card">
        <div class="logo">Jerro Computer School</div>
        <div class="subtitle">Administrator Access Portal</div>

        <?php if (!empty($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="password" style="display: block; text-align: left; font-size: 14px; color: #cbd5e1;">Enter Admin Password:</label>
            <input type="password" id="password" name="password" placeholder="••••••••" required autofocus>
            <button type="submit" class="btn-login">Login to Dashboard</button>
        </form>

        <a href="index.php" class="back-link">← Return to Student Portal</a>
    </div>

</body>
</html>
