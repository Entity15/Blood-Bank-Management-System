-- ============================================================
-- Blood Bank Management System — Complete Final Schema
-- Includes: all core tables + user portal table + User_ID on request
-- ============================================================
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS `blood_bank_management_system`
  CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `blood_bank_management_system`;

-- ADMIN
CREATE TABLE IF NOT EXISTS `admin` (
  `Admin_ID` INT NOT NULL AUTO_INCREMENT,
  `Username` VARCHAR(50) NOT NULL,
  `Password` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`Admin_ID`),
  UNIQUE KEY `uq_username` (`Username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- Password hash below is bcrypt('admin123') — verified via PHP password_verify(), not stored/compared in plaintext.
INSERT IGNORE INTO `admin` (`Username`,`Password`) VALUES ('admin','$2y$10$M83jPaO2ntLLx5MHpRlyF.Q0iz992b9VgriOZGDUdm4v4PKr4/PX6');

-- STAFF
CREATE TABLE IF NOT EXISTS `staff` (
  `Staff_ID` INT NOT NULL AUTO_INCREMENT,
  `Name`     VARCHAR(100) NOT NULL,
  `Phone`    VARCHAR(20)  DEFAULT NULL,
  `Role`     VARCHAR(50)  DEFAULT NULL,
  `Salary`   DECIMAL(10,2) DEFAULT NULL,
  PRIMARY KEY (`Staff_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
INSERT IGNORE INTO `staff` (`Name`,`Phone`,`Role`,`Salary`) VALUES
('Prohor Sarkar Pujon','01700000010','Manager',45000.00),
('Nusrath Jahan Mahi', '01700000011','Technician',32000.00);

-- DONOR
CREATE TABLE IF NOT EXISTS `donor` (
  `Donor_ID`    INT NOT NULL AUTO_INCREMENT,
  `Name`        VARCHAR(100) NOT NULL,
  `Age`         INT DEFAULT NULL,
  `Phone`       VARCHAR(20) DEFAULT NULL,
  `Blood_Group` VARCHAR(3)  DEFAULT NULL,
  `Street`      VARCHAR(255) DEFAULT NULL,
  `City`        VARCHAR(150) DEFAULT NULL,
  `State`       VARCHAR(100) DEFAULT NULL,
  `PIN_Code`    VARCHAR(10)  DEFAULT NULL,
  `Date_Joined` DATE DEFAULT NULL,
  PRIMARY KEY (`Donor_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
INSERT IGNORE INTO `donor` (`Name`,`Age`,`Phone`,`Blood_Group`,`Street`,`City`,`State`,`PIN_Code`,`Date_Joined`) VALUES
('Abdullah Ayman',23,'01744952489','A+','Nobarun 364, Sonarpara','Shibganj','Sylhet','3100','2026-05-12');

-- BLOOD
CREATE TABLE IF NOT EXISTS `blood` (
  `Blood_ID`        INT NOT NULL AUTO_INCREMENT,
  `Blood_Group`     VARCHAR(3) NOT NULL,
  `Units`           INT NOT NULL DEFAULT 1,
  `Collection_Date` DATE DEFAULT NULL,
  `Expiry_Date`     DATE DEFAULT NULL,
  PRIMARY KEY (`Blood_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
INSERT IGNORE INTO `blood` (`Blood_ID`,`Blood_Group`,`Units`,`Collection_Date`,`Expiry_Date`) VALUES
(1,'A+',5,'2026-05-12','2026-06-12');

-- DONATION
CREATE TABLE IF NOT EXISTS `donation` (
  `Donation_ID`   INT NOT NULL AUTO_INCREMENT,
  `Donor_ID`      INT DEFAULT NULL,
  `Blood_ID`      INT DEFAULT NULL,
  `Units`         INT NOT NULL DEFAULT 1,
  `Donation_Date` DATE DEFAULT NULL,
  `Expiry_Date`   DATE DEFAULT NULL,
  PRIMARY KEY (`Donation_ID`),
  KEY `fk_don_donor` (`Donor_ID`),
  KEY `fk_don_blood`  (`Blood_ID`),
  CONSTRAINT `fk_don_donor` FOREIGN KEY (`Donor_ID`) REFERENCES `donor` (`Donor_ID`) ON DELETE SET NULL,
  CONSTRAINT `fk_don_blood` FOREIGN KEY (`Blood_ID`) REFERENCES `blood` (`Blood_ID`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
INSERT IGNORE INTO `donation` (`Donation_ID`,`Donor_ID`,`Blood_ID`,`Units`,`Donation_Date`,`Expiry_Date`) VALUES
(1,1,1,5,'2026-05-12','2026-06-12');

-- HOSPITAL
CREATE TABLE IF NOT EXISTS `hospital` (
  `Hospital_ID` INT NOT NULL AUTO_INCREMENT,
  `Name`        VARCHAR(150) NOT NULL,
  `Contact`     VARCHAR(25)  DEFAULT NULL,
  `Street`      VARCHAR(255) DEFAULT NULL,
  `City`        VARCHAR(100) DEFAULT NULL,
  `State`       VARCHAR(100) DEFAULT NULL,
  `PIN_Code`    VARCHAR(10)  DEFAULT NULL,
  PRIMARY KEY (`Hospital_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
INSERT IGNORE INTO `hospital` (`Hospital_ID`,`Name`,`Contact`,`Street`,`City`,`State`,`PIN_Code`) VALUES
(1,'Ibn Sina Hospital',        '01700000001','Subhanighat Road','Sylhet','Sylhet','3100'),
(2,'Sylhet Imperial Hospital', '01700000002','Naiorpul',         'Sylhet','Sylhet','3100');

-- CONTRACT
CREATE TABLE IF NOT EXISTS `contract` (
  `Contract_ID` INT NOT NULL AUTO_INCREMENT,
  `Hospital_ID` INT DEFAULT NULL,
  `Start_Date`  DATE DEFAULT NULL,
  `End_Date`    DATE DEFAULT NULL,
  PRIMARY KEY (`Contract_ID`),
  KEY `fk_con_hospital` (`Hospital_ID`),
  CONSTRAINT `fk_con_hospital` FOREIGN KEY (`Hospital_ID`) REFERENCES `hospital` (`Hospital_ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
INSERT IGNORE INTO `contract` (`Hospital_ID`,`Start_Date`,`End_Date`) VALUES
(1,'2026-01-01','2026-12-31'),
(2,'2026-03-01','2027-02-28');

-- PATIENT
CREATE TABLE IF NOT EXISTS `patient` (
  `Patient_Disease_ID` INT NOT NULL AUTO_INCREMENT,
  `Patient_ID`         INT NOT NULL,
  `Name`               VARCHAR(255) NOT NULL,
  `Disease_Name`       VARCHAR(255) DEFAULT NULL,
  `Diagnosis_Date`     DATE DEFAULT NULL,
  `Notes`              VARCHAR(255) DEFAULT NULL,
  `Address`            VARCHAR(255) DEFAULT NULL,
  `Phone`              VARCHAR(20)  DEFAULT NULL,
  `Date_Registered`    DATE DEFAULT NULL,
  PRIMARY KEY (`Patient_Disease_ID`),
  KEY `idx_patient_id` (`Patient_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
INSERT IGNORE INTO `patient` (`Patient_Disease_ID`,`Patient_ID`,`Name`,`Disease_Name`,`Diagnosis_Date`,`Notes`,`Address`,`Phone`,`Date_Registered`) VALUES
(1,1,'Abhijit Kar Bappi','Anaemia',     '2026-04-01','Needs regular transfusions','Sylhet','01800000001','2026-04-01'),
(2,2,'Prohor Sarkar',    'Thalassemia', '2026-03-15','Monthly requirement',       'Sylhet','01800000002','2026-03-15');

-- USER (portal accounts)
CREATE TABLE IF NOT EXISTS `user` (
  `User_ID`         INT NOT NULL AUTO_INCREMENT,
  `Full_Name`       VARCHAR(100) NOT NULL,
  `Email`           VARCHAR(150) NOT NULL,
  `Password`        VARCHAR(255) NOT NULL,
  `Phone`           VARCHAR(20)  DEFAULT NULL,
  `Blood_Group`     VARCHAR(3)   DEFAULT NULL,
  `Address`         VARCHAR(255) DEFAULT NULL,
  `Hospital_ID`     INT DEFAULT NULL,
  `Date_Registered` DATE DEFAULT NULL,
  PRIMARY KEY (`User_ID`),
  UNIQUE KEY `uq_email` (`Email`),
  KEY `fk_user_hospital` (`Hospital_ID`),
  CONSTRAINT `fk_user_hospital` FOREIGN KEY (`Hospital_ID`) REFERENCES `hospital` (`Hospital_ID`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- Password hash below is bcrypt('user123') — verified via PHP password_verify(), not stored/compared in plaintext.
INSERT IGNORE INTO `user` (`Full_Name`,`Email`,`Password`,`Phone`,`Blood_Group`,`Address`,`Hospital_ID`,`Date_Registered`) VALUES
('Demo User','user@demo.com','$2y$10$YNkwuWXtMsafLyQ8tuVx0./kORo/WKiXfJUlLGJ3FYIIz6SNudRhS','01700000099','A+','Sylhet',1,CURDATE());

-- REQUEST
CREATE TABLE IF NOT EXISTS `request` (
  `Request_ID`   INT NOT NULL AUTO_INCREMENT,
  `Patient_ID`   INT DEFAULT NULL,
  `Hospital_ID`  INT DEFAULT NULL,
  `User_ID`      INT DEFAULT NULL,
  `Blood_Group`  VARCHAR(3)  DEFAULT NULL,
  `Units`        INT DEFAULT NULL,
  `Request_Date` DATE DEFAULT NULL,
  `Status`       VARCHAR(20) DEFAULT 'Pending',
  PRIMARY KEY (`Request_ID`),
  KEY `fk_req_patient`  (`Patient_ID`),
  KEY `fk_req_hospital` (`Hospital_ID`),
  KEY `fk_req_user`     (`User_ID`),
  CONSTRAINT `fk_req_patient`  FOREIGN KEY (`Patient_ID`)  REFERENCES `patient`  (`Patient_Disease_ID`) ON DELETE SET NULL,
  CONSTRAINT `fk_req_hospital` FOREIGN KEY (`Hospital_ID`) REFERENCES `hospital` (`Hospital_ID`)        ON DELETE SET NULL,
  CONSTRAINT `fk_req_user`     FOREIGN KEY (`User_ID`)     REFERENCES `user`     (`User_ID`)            ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- DONATION_TO_REQUEST
CREATE TABLE IF NOT EXISTS `donation_to_request` (
  `Donation_ID`    INT NOT NULL,
  `Request_ID`     INT NOT NULL,
  `Units_Provided` INT DEFAULT NULL,
  `Date_Provided`  DATE DEFAULT NULL,
  PRIMARY KEY (`Donation_ID`,`Request_ID`),
  KEY `fk_dtr_request` (`Request_ID`),
  CONSTRAINT `fk_dtr_donation` FOREIGN KEY (`Donation_ID`) REFERENCES `donation` (`Donation_ID`) ON DELETE CASCADE,
  CONSTRAINT `fk_dtr_request`  FOREIGN KEY (`Request_ID`)  REFERENCES `request`  (`Request_ID`)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;
