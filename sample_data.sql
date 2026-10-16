INSERT INTO doctors (name, specialization) VALUES
('Dr. Amina Yusuf', 'General Practice'),
('Dr. Brian Otieno', 'Dentistry'),
('Dr. Grace Wambui', 'Pediatrics');

INSERT INTO time_slots (doctor_id, slot_date, start_time, end_time, is_available) VALUES
(1, '2026-10-20', '09:00', '09:30', 1),
(1, '2026-10-20', '09:30', '10:00', 1),
(1, '2026-10-20', '10:00', '10:30', 0),   -- already booked, for testing the "unavailable" case
(2, '2026-10-21', '14:00', '14:30', 1),
(2, '2026-10-21', '14:30', '15:00', 1),
(3, '2026-10-22', '11:00', '11:30', 1),
(3, '2026-10-22', '11:30', '12:00', 1);