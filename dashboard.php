<?php
session_start();
if (!isset($_SESSION['patient_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f9; padding: 40px; }
        .header { display: flex; justify-content: space-between; align-items: center; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .logout-btn { background: #dc3545; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; }
        .logout-btn:hover { background: #c82333; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Welcome, <?= htmlspecialchars($_SESSION['patient_name']) ?>!</h2>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
    <p>This is your patient dashboard where you can book and view your hospital appointments.</p>
</body>
</html>