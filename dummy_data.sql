-- ============================================================
-- Blood Bank Management System — Presentation Demo Data
-- Run AFTER the main schema (blood_bank_management_system.sql)
-- All portal passwords below are: demo123
-- ============================================================
USE `blood_bank_management_system`;

-- STAFF (a few more, on top of the 2 already seeded)
INSERT IGNORE INTO `staff` (`Name`,`Phone`,`Role`,`Salary`) VALUES
('Tanvir Ahmed Rafi',   '01700000012','Lab Technician',  30000.00),
('Farzana Yasmin',      '01700000013','Nurse',           28000.00),
('Shakil Hossain',      '01700000014','Receptionist',    22000.00),
('Mahmuda Akter Nishi', '01700000015','Blood Bank Officer',38000.00);

-- HOSPITALS (a few more)
INSERT IGNORE INTO `hospital` (`Name`,`Contact`,`Street`,`City`,`State`,`PIN_Code`) VALUES
('Mount Adora Hospital',        '01700000003','Bagbari',            'Sylhet','Sylhet','3100'),
('North East Medical College',  '01700000004','Kumargaon',          'Sylhet','Sylhet','3114'),
('Sylhet MAG Osmani Medical',   '01700000005','Medical College Road','Sylhet','Sylhet','3100');

-- CONTRACT (matching the 3 new hospitals: IDs 3,4,5)
INSERT IGNORE INTO `contract` (`Hospital_ID`,`Start_Date`,`End_Date`) VALUES
(3,'2026-02-01','2027-01-31'),
(4,'2026-01-15','2026-12-15'),
(5,'2026-04-01','2027-03-31');

-- DONORS (spread across all 8 blood groups)
INSERT IGNORE INTO `donor` (`Name`,`Age`,`Phone`,`Blood_Group`,`Street`,`City`,`State`,`PIN_Code`,`Date_Joined`) VALUES
('Rafiul Islam Shanto',   27,'01711000001','O+', 'Zindabazar',        'Sylhet','Sylhet','3100','2026-01-05'),
('Nusrat Jahan Priya',    24,'01711000002','A+', 'Amberkhana',        'Sylhet','Sylhet','3100','2026-01-10'),
('Tamim Iqbal Rafi',      31,'01711000003','B+', 'Shahjalal Upashahar','Sylhet','Sylhet','3114','2026-01-18'),
('Sadia Afrin Mim',       22,'01711000004','AB+','Subid Bazar',       'Sylhet','Sylhet','3100','2026-02-02'),
('Kamrul Hasan Nayeem',   35,'01711000005','O-', 'Bandarbazar',       'Sylhet','Sylhet','3100','2026-02-14'),
('Farhana Yeasmin',       29,'01711000006','A-', 'Chowhatta',         'Sylhet','Sylhet','3100','2026-02-20'),
('Imran Kabir Shuvo',     26,'01711000007','B-', 'Tilagor',           'Sylhet','Sylhet','3114','2026-03-01'),
('Rima Akter Lima',       23,'01711000008','AB-','Mirabazar',         'Sylhet','Sylhet','3100','2026-03-08'),
('Sourav Das Bijoy',      33,'01711000009','O+', 'Ambarkhana',        'Sylhet','Sylhet','3100','2026-03-15'),
('Jannatul Ferdous Nabila',21,'01711000010','A+', 'Kumarpara',        'Sylhet','Sylhet','3100','2026-03-22'),
('Mehedi Hasan Fahim',    28,'01711000011','B+', 'Lamabazar',         'Sylhet','Sylhet','3100','2026-04-02'),
('Tasnim Jahan Ruma',     25,'01711000012','O+', 'Uposhohor',         'Sylhet','Sylhet','3114','2026-04-10');

-- BLOOD INVENTORY (mixed fresh + a couple near/at expiry, for demo variety)
INSERT IGNORE INTO `blood` (`Blood_Group`,`Units`,`Collection_Date`,`Expiry_Date`) VALUES
('O+', 8,'2026-07-20','2026-08-20'),
('A+', 6,'2026-07-22','2026-08-22'),
('B+', 5,'2026-07-25','2026-08-25'),
('AB+',3,'2026-07-26','2026-08-26'),
('O-', 4,'2026-07-15','2026-08-15'),
('A-', 3,'2026-07-18','2026-08-18'),
('B-', 2,'2026-07-19','2026-08-19'),
('AB-',2,'2026-07-21','2026-08-21'),
('O+', 4,'2026-06-01','2026-07-01'); -- intentionally expired, to show expiry filtering in action

-- DONATIONS (linking donors above to the blood units above; Blood_IDs 2-10 assuming table was empty before except seed Blood_ID=1)
INSERT IGNORE INTO `donation` (`Donor_ID`,`Blood_ID`,`Units`,`Donation_Date`,`Expiry_Date`) VALUES
(2,2,8,'2026-07-20','2026-08-20'),
(3,3,6,'2026-07-22','2026-08-22'),
(4,4,5,'2026-07-25','2026-08-25'),
(5,5,3,'2026-07-26','2026-08-26'),
(6,6,4,'2026-07-15','2026-08-15'),
(7,7,3,'2026-07-18','2026-08-18'),
(8,8,2,'2026-07-19','2026-08-19'),
(9,9,2,'2026-07-21','2026-08-21'),
(10,10,4,'2026-06-01','2026-07-01');

-- PATIENTS
INSERT IGNORE INTO `patient` (`Patient_ID`,`Name`,`Disease_Name`,`Diagnosis_Date`,`Notes`,`Address`,`Phone`,`Date_Registered`) VALUES
(3,'Nabila Chowdhury Mitu','Aplastic Anaemia','2026-05-02','Weekly transfusion needed','Sylhet','01800000003','2026-05-02'),
(4,'Rakibul Hasan Emon',   'Leukemia',        '2026-05-20','Post-chemo transfusion',   'Sylhet','01800000004','2026-05-20'),
(5,'Sumaiya Islam Tuli',   'Dengue',          '2026-06-10','Platelet support',         'Sylhet','01800000005','2026-06-10');

-- USER (portal accounts) — all passwords are: demo123
INSERT IGNORE INTO `user` (`Full_Name`,`Email`,`Password`,`Phone`,`Blood_Group`,`Address`,`Hospital_ID`,`Date_Registered`) VALUES
('Rafiul Islam Shanto', 'rafiul@demo.com', '$2y$10$VDW6sl.BU1FD/NLvMqSF7ejIeaS.gs9NjUO3ZKnfeC9zIXm5/Fzqe','01711000001','O+','Sylhet',1,'2026-06-01'),
('Nusrat Jahan Priya',  'nusrat@demo.com', '$2y$10$VDW6sl.BU1FD/NLvMqSF7ejIeaS.gs9NjUO3ZKnfeC9zIXm5/Fzqe','01711000002','A+','Sylhet',2,'2026-06-05'),
('Tamim Iqbal Rafi',    'tamim@demo.com',  '$2y$10$VDW6sl.BU1FD/NLvMqSF7ejIeaS.gs9NjUO3ZKnfeC9zIXm5/Fzqe','01711000003','B+','Sylhet',3,'2026-06-10');

-- REQUESTS (mix of pending / approved / rejected for demo)
INSERT IGNORE INTO `request` (`Patient_ID`,`Hospital_ID`,`User_ID`,`Blood_Group`,`Units`,`Request_Date`,`Status`) VALUES
(1,1,2,'A+', 2,'2026-07-28','Pending'),
(2,2,3,'B+', 3,'2026-07-29','Approved'),
(3,3,4,'O+', 1,'2026-07-30','Pending'),
(4,4,5,'O-', 2,'2026-08-01','Approved'),
(5,5,6,'AB+',1,'2026-08-03','Rejected');

-- DONATION_TO_REQUEST (fulfillment records for the 'Approved' requests above: Request_ID 2 and 4)
INSERT IGNORE INTO `donation_to_request` (`Donation_ID`,`Request_ID`,`Units_Provided`,`Date_Provided`) VALUES
(3,2,3,'2026-07-29'),
(6,4,2,'2026-08-01');
