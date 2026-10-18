<?php
require 'db.php';

$stmt = $pdo->prepare("
    SELECT ts.slot_id, d.name AS doctor_name, d.specialization, ts.slot_date, ts.start_time, ts.end_time
    FROM time_slots ts
    JOIN doctors d ON ts.doctor_id = d.doctor_id
    WHERE ts.is_available = 1
    ORDER BY ts.slot_date, ts.start_time
");
$stmt->execute();
$slots = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Doctor Availability</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5" style="max-width: 500px;">
    <h2 class="mb-4">Available Appointment Slots</h2>

    <?php if (count($slots) === 0): ?>
      <div class="alert alert-info">No available slots at the moment. Please check back later.</div>
    <?php else: ?>
      <form method="POST" action="book_appointment.php" class="card p-4 shadow-sm">
        <div class="mb-3">
          <label class="form-label">Choose a Doctor &amp; Time Slot</label>
          <select name="slot_id" class="form-select" required>
            <?php foreach ($slots as $slot): ?>
              <option value="<?= $slot['slot_id'] ?>">
                Dr. <?= htmlspecialchars($slot['doctor_name']) ?> (<?= htmlspecialchars($slot['specialization']) ?>)
                — <?= $slot['slot_date'] ?>, <?= $slot['start_time'] ?> to <?= $slot['end_time'] ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <button type="submit" class="btn btn-primary">Book This Appointment</button>
      </form>
    <?php endif; ?>
  </div>
</body>
</html>
