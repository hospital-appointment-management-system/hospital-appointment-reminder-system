<?php
$pdo = new PDO("mysql:host=localhost;dbname=hospital_appointment_db", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>