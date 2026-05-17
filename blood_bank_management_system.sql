-- Blood Bank Management System
-- Final Version
-- MariaDB / MySQL

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- Database
CREATE DATABASE IF NOT EXISTS `blood_bank_management_system`
  CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE `blood_bank_management_system`;

-- --------------------------------------------------------
-- ADMIN
-- --------------------------------------------------------
CREATE TABLE `admin` (
  `Admin_ID`  int(11)      NOT NULL AUTO_INCREMENT,
  `Username`  varchar(50)  NOT NULL,
  `Password`  varchar(255) NOT NULL,
  PRIMARY KEY (`Admin_ID`),
  UNIQUE KEY `Username` (`Username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `admin` (`Username`, `Password`) VALUES ('admin', 'admin123');

-- --------------------------------------------------------
-- STAFF
-- --------------------------------------------------------
CREATE TABLE `staff` (
  `Staff_ID` int(11)       NOT NULL AUTO_INCREMENT,
  `Name`     varchar(100)  NOT NULL,
  `Phone`    varchar(20)   DEFAULT NULL,
  `Role`     varchar(50)   DEFAULT NULL,
  `Salary`   decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`Staff_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- DONOR
-- --------------------------------------------------------
CREATE TABLE `donor` (
  `Donor_ID`     int(11)      NOT NULL AUTO_INCREMENT,
  `Name`         varchar(100) NOT NULL,
  `Age`          int(11)      DEFAULT NULL,
  `Phone`        varchar(20)  DEFAULT NULL,
  `Address`      text         DEFAULT NULL,
  `Units_Donated` int(11)     DEFAULT 0,
  PRIMARY KEY (`Donor_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `donor` (`Name`, `Age`, `Phone`, `Address`, `Units_Donated`) VALUES
('Abdullah Ayman', 23, '01744952489', 'Nobarun 364, Sonarpara, Shibganj, Sylhet', 5);

-- --------------------------------------------------------
-- BLOOD  (individual bag / unit records)
-- --------------------------------------------------------
CREATE TABLE `blood` (
  `Blood_ID`       int(11)     NOT NULL AUTO_INCREMENT,
  `Blood_Group`    varchar(5)  NOT NULL,
  `Units`          int(11)     NOT NULL DEFAULT 1,
  `Collection_Date` date       DEFAULT NULL,
  `Expiry_Date`    date        DEFAULT NULL,
  PRIMARY KEY (`Blood_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `blood` (`Blood_Group`, `Units`, `Collection_Date`, `Expiry_Date`) VALUES
('A+', 5, '2026-05-12', '2026-06-12');

-- --------------------------------------------------------
-- BLOOD_STOCK  (aggregated inventory per blood group)
-- --------------------------------------------------------
CREATE TABLE `blood_stock` (
  `Blood_Group`     varchar(5) NOT NULL,
  `Units_Available` int(11)    NOT NULL DEFAULT 0,
  PRIMARY KEY (`Blood_Group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `blood_stock` (`Blood_Group`, `Units_Available`) VALUES
('A+',  5),
('A-',  0),
('B+',  0),
('B-',  0),
('AB+', 0),
('AB-', 0),
('O+',  0),
('O-',  0);

-- --------------------------------------------------------
-- DONATION
-- --------------------------------------------------------
CREATE TABLE `donation` (
  `Donation_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Donor_ID`    int(11) DEFAULT NULL,
  `Blood_ID`    int(11) DEFAULT NULL,
  `Date`        date    DEFAULT NULL,
  PRIMARY KEY (`Donation_ID`),
  KEY `Donor_ID` (`Donor_ID`),
  KEY `Blood_ID` (`Blood_ID`),
  CONSTRAINT `donation_ibfk_1` FOREIGN KEY (`Donor_ID`) REFERENCES `donor` (`Donor_ID`) ON DELETE SET NULL,
  CONSTRAINT `donation_ibfk_2` FOREIGN KEY (`Blood_ID`) REFERENCES `blood`  (`Blood_ID`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `donation` (`Donor_ID`, `Blood_ID`, `Date`) VALUES (1, 1, '2026-05-12');

-- --------------------------------------------------------
-- HOSPITAL
-- --------------------------------------------------------
CREATE TABLE `hospital` (
  `Hospital_ID` int(11)      NOT NULL AUTO_INCREMENT,
  `Name`        varchar(100) NOT NULL,
  `Address`     text         DEFAULT NULL,
  PRIMARY KEY (`Hospital_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `hospital` (`Name`, `Address`) VALUES
('Ibn Sina',                'Subhanighat'),
('Sylhet Imperial Hospital','Naiorpul');

-- --------------------------------------------------------
-- CONTRACT
-- --------------------------------------------------------
CREATE TABLE `contract` (
  `Contract_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Hospital_ID` int(11) DEFAULT NULL,
  `Start_Date`  date    DEFAULT NULL,
  `End_Date`    date    DEFAULT NULL,
  PRIMARY KEY (`Contract_ID`),
  KEY `Hospital_ID` (`Hospital_ID`),
  CONSTRAINT `contract_ibfk_1` FOREIGN KEY (`Hospital_ID`) REFERENCES `hospital` (`Hospital_ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- PATIENT
-- --------------------------------------------------------
CREATE TABLE `patient` (
  `Patient_ID`  int(11)      NOT NULL AUTO_INCREMENT,
  `Name`        varchar(100) NOT NULL,
  `Age`         int(11)      DEFAULT NULL,
  `Blood_Group` varchar(5)   DEFAULT NULL,
  `Disease`     varchar(100) DEFAULT NULL,
  PRIMARY KEY (`Patient_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `patient` (`Name`, `Age`, `Blood_Group`, `Disease`) VALUES
('Abhijit Kar Bappi', 22, 'B+', 'Anaemia'),
('Prohor Sarkar',     22, 'A+', 'Thalassemia');

-- --------------------------------------------------------
-- BLOOD_REQUEST  (the single request table used throughout)
-- --------------------------------------------------------
CREATE TABLE `blood_request` (
  `Request_ID`     int(11)     NOT NULL AUTO_INCREMENT,
  `Patient_ID`     int(11)     DEFAULT NULL,
  `Hospital_ID`    int(11)     DEFAULT NULL,
  `Blood_Group`    varchar(5)  DEFAULT NULL,
  `Units_Required` int(11)     DEFAULT NULL,
  `Request_Date`   date        DEFAULT NULL,
  `Status`         varchar(20) DEFAULT 'Pending',
  PRIMARY KEY (`Request_ID`),
  KEY `Patient_ID`  (`Patient_ID`),
  KEY `Hospital_ID` (`Hospital_ID`),
  CONSTRAINT `blood_request_ibfk_1` FOREIGN KEY (`Patient_ID`)  REFERENCES `patient`  (`Patient_ID`) ON DELETE SET NULL,
  CONSTRAINT `blood_request_ibfk_2` FOREIGN KEY (`Hospital_ID`) REFERENCES `hospital` (`Hospital_ID`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;
