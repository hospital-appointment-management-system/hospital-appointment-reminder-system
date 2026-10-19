<?php
session_start();
require 'db.php';                

if (!isset($_SESSION['patient_id'])) {
    echo "Please log in first.";
    exit;
}

$patientId = $_SESSION['patient_id'];
$slotId = $_POST['slot_id'];

$pdo->beginTransaction();
try {
    // lock the slot row so no one else can book it at the same moment
    $check = $pdo->prepare("SELECT is_available FROM time_slots WHERE slot_id = ? FOR UPDATE");
    $check->execute([$slotId]);
    $available = $check->fetchColumn();

    if ($available) {
        $pdo->prepare("INSERT INTO appointments (patient_id, slot_id) VALUES (?, ?)")
            ->execute([$patientId, $slotId]);
        $pdo->prepare("UPDATE time_slots SET is_available = 0 WHERE slot_id = ?")
            ->execute([$slotId]);
        $pdo->commit();
        echo "Appointment booked successfully!";
    } else {
        $pdo->rollBack();
        echo "Sorry, this slot was just booked by someone else. Please choose another.";
    }
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Something went wrong. Please try again.";
}
?>