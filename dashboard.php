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
    <title>Home - Hospital Portal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="page-container">
        <div class="welcome-banner">
            <h1>Welcome back, <?= htmlspecialchars($_SESSION['patient_name']) ?>!</h1>
            <p>Here's what you can do today.</p>
        </div>

        <div class="action-grid">
            <a href="availability.php" class="action-card">
                <div class="action-icon">📅</div>
                <h3>View Availability &amp; Book</h3>
                <p>See open doctor time slots and book an appointment.</p>
            </a>

            <a href="profile.php" class="action-card">
                <div class="action-icon">👤</div>
                <h3>My Profile</h3>
                <p>View your personal details on file.</p>
            </a>
        </div>
    </div>
</body>
</html>