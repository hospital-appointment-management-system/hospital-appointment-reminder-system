<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar">
  <div class="navbar-brand">
    <a href="dashboard.php">🏥 Hospital Portal</a>
  </div>
  <ul class="navbar-links">
    <li><a href="dashboard.php" class="<?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">Home</a></li>
    <li><a href="availability.php" class="<?= $currentPage === 'availability.php' ? 'active' : '' ?>">Doctor Availability</a></li>
    <li><a href="profile.php" class="<?= $currentPage === 'profile.php' ? 'active' : '' ?>">My Profile</a></li>
  </ul>
  <div class="navbar-user">
    <span>Hi, <?= htmlspecialchars($_SESSION['patient_name'] ?? '') ?></span>
    <a href="logout.php" class="btn-logout">Logout</a>
  </div>
</nav>