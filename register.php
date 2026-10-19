<?php
require_once 'db.php';
session_start();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($name) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        $stmt = $pdo->prepare("SELECT patient_id FROM patients WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            $error = "Email is already registered.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert = $pdo->prepare("INSERT INTO patients (name, email, password) VALUES (?, ?, ?)");
            
            if ($insert->execute([$name, $email, $hashed_password])) {
                $success = "Registration successful! You can now <a href='login.php'>log in</a>.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Registration - Hospital Portal</title>
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --bg-gradient: linear-gradient(135deg, #f0fdf4 0%, #eff6ff 100%);
            --card-bg: #ffffff;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --error-bg: #fee2e2;
            --error-text: #991b1b;
            --success-bg: #d1fae5;
            --success-text: #065f46;
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

        h2 { margin-bottom: 8px; font-size: 26px; font-weight: 700; color: #111827; }
        .subtitle { font-size: 14px; color: var(--text-muted); margin-bottom: 24px; }

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
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
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
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        button:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.4);
        }

        button:active { transform: translateY(0); }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 20px;
            animation: fadeIn 0.4s ease;
        }
        .error { background: var(--error-bg); color: var(--error-text); }
        .success { background: var(--success-bg); color: var(--success-text); }
        .success a { color: var(--success-text); font-weight: bold; }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        .footer-text { text-align: center; margin-top: 24px; font-size: 14px; color: var(--text-muted); }
        .footer-text a { color: var(--primary); text-decoration: none; font-weight: 600; }
        .footer-text a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Create Account</h2>
        <p class="subtitle">Join our healthcare network today</p>

        <?php if(!empty($error)): ?><div class="alert error"><?= $error ?></div><?php endif; ?>
        <?php if(!empty($success)): ?><div class="alert success"><?= $success ?></div><?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <input type="text" name="name" placeholder="Full Name" required>
            </div>
            <div class="input-group">
                <input type="email" name="email" placeholder="Email Address" required>
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="Create Password" required>
            </div>
            <button type="submit">Sign Up</button>
        </form>
        <p class="footer-text">Already have an account? <a href="login.php">Log in</a></p>
    </div>
</body>
</html>