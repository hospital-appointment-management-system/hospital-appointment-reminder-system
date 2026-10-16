CREATE TABLE patients (
  patient_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100), 
  email VARCHAR(100) UNIQUE, 
  password VARCHAR(255)
);

CREATE TABLE doctors (
  doctor_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100), 
  specialization VARCHAR(100)
);

CREATE TABLE time_slots (
  slot_id INT AUTO_INCREMENT PRIMARY KEY,
  doctor_id INT, 
  slot_date DATE, 
  start_time TIME, 
  end_time TIME,
  is_available BOOLEAN DEFAULT TRUE,
  FOREIGN KEY (doctor_id) REFERENCES doctors(doctor_id)
);

CREATE TABLE appointments (
  appointment_id INT AUTO_INCREMENT PRIMARY KEY,
  patient_id INT, 
  slot_id INT, status VARCHAR(20) DEFAULT 'Pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (patient_id) REFERENCES patients(patient_id),
  FOREIGN KEY (slot_id) REFERENCES time_slots(slot_id)
);