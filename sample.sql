CREATE TABLE `patients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `date_of_birth` date NOT NULL,
  `gender` varchar(50) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  PRIMARY KEY (`id`)
);


CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
);


CREATE TABLE `doctors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `specialization` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
);



CREATE TABLE `appointments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `reason` text NOT NULL,
  `status` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`),
  KEY `doctor_id` (`doctor_id`),
  CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE
);



INSERT INTO `doctors` (`name`, `specialization`, `phone`, `email`) VALUES
('Dr. Rahman Chowdhury', 'Cardiology', '+8801711234567', 'rahman.chowdhury@bdmed.com'),
('Dr. Fatema Begum', 'Pediatrics', '+8801722987654', 'fatema.begum@bdmed.com'),
('Dr. Shamsul Islam', 'Dermatology', '+8801911555666', 'shamsul.islam@bdmed.com'),
('Dr. Rokeya Ahmed', 'Orthopedics', '+8801812333444', 'rokeya.ahmed@bdmed.com'),
('Dr. Abul Kalam Azad', 'General Medicine', '+8801977123123', 'abul.kalam.azad@bdmed.com');



INSERT INTO `patients` (`name`, `date_of_birth`, `gender`, `phone`, `address`) VALUES
('Md. Karim Hassan', '1988-05-12', 'Male', '01715123456', 'House # 15, Road # 5, Sector 10, Uttara, Dhaka 1230'),
('Fatima Akter', '1995-11-30', 'Female', '01987654321', 'Apartment 3B, Jasmine Tower, Mirpur DOHS, Dhaka 1216'),
('Rashedul Islam', '2005-01-20', 'Male', '01818999888', 'Village: Shantinagar, Post Office: Kalia, Upazila: Sreepur, District: Gazipur 1740'),
('Sultana Parvin', '1978-07-25', 'Female', '01777888777', 'House # 100, Road # 20, Gulshan 1, Dhaka 1212'),
('Md. Jamilur Rahman', '1990-03-15', 'Male', '01912345678', '25/A, K.B. Road, Khulna Sadar, Khulna 9000');



INSERT INTO `appointments` (`patient_id`, `doctor_id`, `appointment_date`, `appointment_time`, `reason`, `status`) VALUES
(1, 1, '2025-09-20', '10:00:00', 'Follow-up for hypertension', 'Scheduled'),
(2, 2, '2025-09-20', '10:30:00', 'Child\'s fever and cough', 'Scheduled'),
(3, 3, '2025-09-21', '11:00:00', 'Skin rash consultation', 'Completed'),
(4, 4, '2025-09-21', '09:30:00', 'Knee pain assessment', 'Scheduled'),
(5, 5, '2025-09-22', '14:00:00', 'General check-up', 'Scheduled'),
(1, 3, '2025-09-22', '11:30:00', 'Mole check', 'Scheduled');



INSERT INTO `users` (`id`, `username`, `password`) VALUES
(1, 'nazmul', '$2y$10$Rzr3uVW3Z6VeKVWa36mKyOShmDg4nSLvdvg44Urh6DLoKcao7DcCy');
