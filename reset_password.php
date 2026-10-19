<?php
require_once 'db.php';

$error = '';
$success = '';
$token = $_GET['token'] ?? '';

if (empty($token)) {
    $error = "No reset token provided. Please request a new password reset link.";
} else {
    $stmt = $pdo->prepare("SELECT patient_id FROM patients WHERE reset_token = ? AND reset_expires > NOW()");
    $stmt->execute([$token]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$patient) {
        $error = "This password reset link is invalid or has expired (links are valid for 1 hour).";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $patient) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match. Please try again.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $update = $pdo->prepare("UPDATE patients SET password = ?, reset_token = NULL, reset_expires = NULL WHERE patient_id = ?");
        $update->execute([$hashed_password, $patient['patient_id']]);

        $success = "Your password has been successfully reset!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password - Hospital Portal</title>
    <style>
        :root {
            --primary: #10b981;
            --primary-hover: #059669;
            --bg-gradient: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            --card-bg: #ffffff;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --error-bg: #fee2e2;
            --error-text: #991b1b;
            --success-bg: #f0fdf4;
            --success-text: #166534;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        
        body {
            background: var(--bg-gradient);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: var(--text-main);
        }

        .card {
            background: var(--card-bg);
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 400px;
            transform: translateY(20px);
            opacity: 0;
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes slideUp {
            to { transform: translateY(0); opacity: 1; }
        }

        h2 { margin-bottom: 8px; font-size: 24px; font-weight: 700; color: #111827; }
        .subtitle { font-size: 14px; color: var(--text-muted); margin-bottom: 24px; line-height: 1.4; }

        .input-group { position: relative; margin-bottom: 20px; }
        
        input {
            width: 100%;
            padding: 14px 16px;
            border: 1.5px solid #e5e7eb;
            border-radius: 8px;
            font-size: 15px;
            outline: none;
            transition: all 0.3s ease;
            background: #f9fafb;
        }

        input:focus {
            border-color: var(--primary);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
        }

        button {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        button:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
        }

        .alert {
            padding: 14px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 20px;
            line-height: 1.5;
        }
        .error { background: var(--error-bg); color: var(--error-text); }
        .success { background: var(--success-bg); color: var(--success-text); border: 1px solid #bbf7d0; text-align: center; }

        .btn-action {
            display: inline-block;
            margin-top: 15px;
            padding: 12px 20px;
            background: var(--primary);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: background 0.2s;
        }
        .btn-action:hover { background: var(--primary-hover); }

        .back-link { text-align: center; margin-top: 24px; font-size: 14px; }
        .back-link a { color: var(--text-muted); text-decoration: none; font-weight: 600; }
        .back-link a:hover { color: var(--text-main); }
    </style>
</head>
<body>
    <div class="card">
        <h2>Set New Password</h2>

        <?php if (!empty($success)): ?>
            <div class="alert success">
                <p style="font-size: 16px; font-weight: bold; margin-bottom: 6px;">🎉 Success!</p>
                <p><?= $success ?></p>
                <a href="login.php" class="btn-action">Proceed to Login</a>
            </div>
        <?php elseif (!empty($error) && empty($patient)): ?>
            <div class="alert error">
                <p style="font-weight: bold; margin-bottom: 4px;">Link Error</p>
                <p><?= $error ?></p>
            </div>
            <div class="back-link">
                <a href="forgot_password.php">← Request a new reset link</a>
            </div>
        <?php else: ?>
        
            <p class="subtitle">Please enter a secure new password for your account.</p>

            <?php if (!empty($error)): ?>
                <div class="alert error"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="input-group">
                    <input type="password" name="password" placeholder="New Password (min. 6 chars)" required>
                </div>
                <div class="input-group">
                    <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
                </div>
                <button type="submit">Update Password</button>
            </form>

            <div class="back-link">
                <a href="login.php">Remember your password? Log in</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>