<?php
session_start();
if (!isset($_SESSION['patient_id'])) {
    header("Location: login.php");
    exit;
}

require 'db.php';

$stmt = $pdo->prepare("SELECT name, email FROM patients WHERE patient_id = ?");
$stmt->execute([$_SESSION['patient_id']]);
$patient = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile - Hospital Portal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="page-container" style="max-width: 560px;">
        <h1>My Profile</h1>
        <p style="color: var(--text-muted); margin-bottom: 24px;">Your personal details on file.</p>

        <div class="card">
            <div class="profile-field">
                <span class="label">Full Name</span>
                <span class="value"><?= htmlspecialchars($patient['name']) ?></span>
            </div>
            <div class="profile-field">
                <span class="label">Email</span>
                <span class="value"><?= htmlspecialchars($patient['email']) ?></span>
            </div>
        </div>
    </div>
</body>
</html>