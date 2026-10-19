<?php
session_start();
if (!isset($_SESSION['patient_id'])) {
    header("Location: login.php");
    exit;
}

require 'db.php';

$stmt = $pdo->prepare("
    SELECT ts.slot_id, d.doctor_id, d.name AS doctor_name, d.specialization,
           ts.slot_date, ts.start_time, ts.end_time
    FROM time_slots ts
    JOIN doctors d ON ts.doctor_id = d.doctor_id
    WHERE ts.is_available = 1
    ORDER BY ts.slot_date, ts.start_time
");
$stmt->execute();
$slots = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group: specialization -> doctor_id -> { name, slots[] }
$grouped = [];
foreach ($slots as $slot) {
    $spec = $slot['specialization'];
    $docId = $slot['doctor_id'];

    if (!isset($grouped[$spec])) {
        $grouped[$spec] = [];
    }
    if (!isset($grouped[$spec][$docId])) {
        $grouped[$spec][$docId] = [
            'name' => $slot['doctor_name'],
            'slots' => [],
        ];
    }
    $grouped[$spec][$docId]['slots'][] = $slot;
}
ksort($grouped);

// Flash message set by book_appointment.php after a booking attempt.
$status = $_GET['status'] ?? null;
$msg = $_GET['msg'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Doctor Availability - Hospital Portal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="page-container">
        <h1>Available Appointment Slots</h1>
        <p style="color: var(--text-muted); margin-bottom: 24px;">Pick a specialization, doctor, and time slot below.</p>

        <?php if ($msg): ?>
            <div class="alert <?= $status === 'success' ? 'alert-success' : 'alert-error' ?>">
                <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>

        <?php if (count($slots) === 0): ?>
            <div class="alert alert-info">No available slots at the moment. Please check back later.</div>
        <?php else: ?>
            <form method="POST" action="book_appointment.php" id="book-form">

                <?php foreach ($grouped as $specialization => $doctors): ?>
                    <div class="specialization-group">
                        <span class="specialization-heading"><?= htmlspecialchars($specialization) ?></span>

                        <?php foreach ($doctors as $doctorData): ?>
                            <div class="doctor-group">
                                <h3>Dr. <?= htmlspecialchars($doctorData['name']) ?></h3>
                                <div class="slot-grid">
                                    <?php foreach ($doctorData['slots'] as $slot): ?>
                                        <?php
                                            $niceDate = date('D, M j', strtotime($slot['slot_date']));
                                            $niceStart = date('g:i A', strtotime($slot['start_time']));
                                            $niceEnd = date('g:i A', strtotime($slot['end_time']));
                                            $label = "Dr. {$doctorData['name']} — {$niceDate}, {$niceStart} to {$niceEnd}";
                                        ?>
                                        <label class="slot-pill">
                                            <input
                                                type="radio"
                                                name="slot_id"
                                                value="<?= $slot['slot_id'] ?>"
                                                data-label="<?= htmlspecialchars($label) ?>"
                                                required
                                            >
                                            <span><?= $niceDate ?>, <?= $niceStart ?>–<?= $niceEnd ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>

                <div class="book-bar">
                    <span class="selected-slot-text" id="selected-slot-text">No time slot selected yet.</span>
                    <button type="submit" class="btn-primary" id="book-submit" disabled>Book This Appointment</button>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <script>
        const form = document.getElementById('book-form');
        const selectedText = document.getElementById('selected-slot-text');
        const submitBtn = document.getElementById('book-submit');

        if (form) {
            form.addEventListener('change', function (e) {
                if (e.target.name === 'slot_id') {
                    selectedText.textContent = 'Selected: ' + e.target.dataset.label;
                    submitBtn.disabled = false;
                }
            });
        }
    </script>
</body>
</html>