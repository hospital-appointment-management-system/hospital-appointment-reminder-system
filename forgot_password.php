<?php
require_once 'db.php';

$message = '';
$dev_link = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    $stmt = $pdo->prepare("SELECT patient_id FROM patients WHERE email = ?");
    $stmt->execute([$email]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($patient) {
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

        $update = $pdo->prepare("UPDATE patients SET reset_token = ?, reset_expires = ? WHERE email = ?");
        $update->execute([$token, $expires, $email]);

        $reset_link = "http://localhost/hospital/reset_password.php?token=" . $token;
        $message = "Password reset instructions generated successfully.";
        $dev_link = $reset_link;
    } else {
        $message = "If that email exists, reset instructions have been sent.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password - Hospital Portal</title>
    <style>
        :root {
            --primary: #f59e0b;
            --primary-hover: #d97706;
            --bg-gradient: linear-gradient(135deg, #fef3c7 0%, #fffbeb 100%);
            --card-bg: #ffffff;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --success-bg: #ecfdf5;
            --success-text: #047857;
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
            box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1);
        }

        button {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }

        button:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(245, 158, 11, 0.4);
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 20px;
            background: var(--success-bg);
            color: var(--success-text);
        }

        .dev-box {
            background: #f3f4f6;
            padding: 12px;
            border-radius: 6px;
            font-size: 12px;
            margin-bottom: 20px;
            word-break: break-all;
            color: #4b5563;
            border-left: 3px solid var(--primary);
        }
        .dev-box a { color: #2563eb; }

        .back-link { text-align: center; margin-top: 24px; font-size: 14px; }
        .back-link a { color: var(--text-muted); text-decoration: none; font-weight: 600; }
        .back-link a:hover { color: var(--text-main); }
    </style>
</head>
<body>
    <div class="card">
        <h2>Reset Password</h2>
        <p class="subtitle">Enter your registered email address and we'll send you a link to reset your password.</p>

        <?php if(!empty($message)): ?><div class="alert"><?= $message ?></div><?php endif; ?>
        <?php if(!empty($dev_link)): ?>
            <div class="dev-box">
                <strong>[Local Dev Link]:</strong> <a href="<?= $dev_link ?>"><?= $dev_link ?></a>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <input type="email" name="email" placeholder="Registered Email Address" required>
            </div>
            <button type="submit">Send Reset Link</button>
        </form>

        <div class="back-link">
            <a href="login.php">← Back to Login</a>
        </div>
    </div>
</body>
</html>