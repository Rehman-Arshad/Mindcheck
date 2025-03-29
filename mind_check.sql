-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mind_check`
--
DROP DATABASE IF EXISTS `mind_check`;
CREATE DATABASE IF NOT EXISTS `mind_check`;
USE `mind_check`;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `aemail` varchar(255) NOT NULL,
  `apassword` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`aemail`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`aemail`, `apassword`) VALUES
('admin@mindcheck.com', '123');

-- --------------------------------------------------------

--
-- Table structure for table `webuser`
--

CREATE TABLE `webuser` (
  `email` varchar(255) NOT NULL,
  `usertype` char(1) DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `webuser`
--

INSERT INTO `webuser` (`email`, `usertype`) VALUES
('admin@mindcheck.com', 'a'),
('doctor1@mindcheck.com', 'd'),
('doctor2@mindcheck.com', 'd'),
('doctor3@mindcheck.com', 'd'),
('doctor4@mindcheck.com', 'd'),
('patient1@mindcheck.com', 'p'),
('patient2@mindcheck.com', 'p'),
('patient3@mindcheck.com', 'p'),
('patient4@mindcheck.com', 'p'),
('john.smith@mindcheck.com', 'd'),
('sarah.johnson@mindcheck.com', 'd'),
('michael.brown@mindcheck.com', 'd'),
('emily.davis@mindcheck.com', 'd'),
('david.wilson@mindcheck.com', 'd');

-- --------------------------------------------------------

--
-- Table structure for table `specialties`
--

CREATE TABLE IF NOT EXISTS `specialties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `specialties`
--

INSERT INTO `specialties` (`id`, `name`, `description`) VALUES
(1, 'Child Psychiatry', 'Specializes in mental, emotional, and behavioral disorders in children'),
(2, 'Child Psychology', 'Focuses on psychological development and behavioral issues'),
(3, 'Developmental Specialist', 'Expertise in child development and developmental disorders'),
(4, 'Behavioral Therapy', 'Specialized in behavioral intervention and modification'),
(5, 'Child Neurology', 'Focuses on neurological conditions affecting children');

-- --------------------------------------------------------

--
-- Table structure for table `doctor`
--

CREATE TABLE IF NOT EXISTS `doctor` (
  `docid` int(11) NOT NULL AUTO_INCREMENT,
  `docemail` varchar(255) NOT NULL,
  `docname` varchar(255) DEFAULT NULL,
  `docpassword` varchar(255) DEFAULT NULL,
  `docnic` varchar(15) DEFAULT NULL,
  `doctel` varchar(15) DEFAULT NULL,
  `specialties` int(11) DEFAULT NULL,
  `docexp` int(11) DEFAULT NULL,
  PRIMARY KEY (`docid`),
  FOREIGN KEY (`specialties`) REFERENCES `specialties`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `doctor`
--

INSERT INTO `doctor` (`docid`, `docemail`, `docname`, `docpassword`, `docnic`, `doctel`, `specialties`, `docexp`) VALUES
(1, 'john.smith@mindcheck.com', 'John Smith', 'password123', '1234567890', '555-0101', 1, 5),
(2, 'sarah.johnson@mindcheck.com', 'Sarah Johnson', 'password123', '2345678901', '555-0102', 2, 3),
(3, 'michael.brown@mindcheck.com', 'Michael Brown', 'password123', '3456789012', '555-0103', 3, 4),
(4, 'emily.davis@mindcheck.com', 'Emily Davis', 'password123', '4567890123', '555-0104', 4, 6),
(5, 'david.wilson@mindcheck.com', 'David Wilson', 'password123', '5678901234', '555-0105', 5, 7);

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

CREATE TABLE `patient` (
  `pid` int(11) NOT NULL AUTO_INCREMENT,
  `pemail` varchar(255) NOT NULL,
  `pname` varchar(255) DEFAULT NULL,
  `ppassword` varchar(255) NOT NULL,
  `pnic` varchar(15) DEFAULT NULL,
  `ptel` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`pid`),
  KEY `pemail` (`pemail`),
  CONSTRAINT `patient_webuser_fk` FOREIGN KEY (`pemail`) REFERENCES `webuser` (`email`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `patient`
--

INSERT INTO `patient` (`pid`, `pemail`, `pname`, `ppassword`, `pnic`, `ptel`) VALUES
(1, 'patient1@mindcheck.com', 'Patient 1', '123', '0000000001', '0300-0000001'),
(2, 'patient2@mindcheck.com', 'Patient 2', '123', '0000000002', '0300-0000002'),
(3, 'patient3@mindcheck.com', 'Patient 3', '123', '0000000003', '0300-0000003'),
(4, 'patient4@mindcheck.com', 'Patient 4', '123', '0000000004', '0300-0000004');

-- --------------------------------------------------------

--
-- Table structure for table `schedule`
--

CREATE TABLE IF NOT EXISTS `schedule` (
  `scheduleid` int(11) NOT NULL AUTO_INCREMENT,
  `docid` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `scheduledate` date NOT NULL,
  `scheduletime` time NOT NULL,
  `nop` int(11) NOT NULL,
  PRIMARY KEY (`scheduleid`),
  KEY `docid` (`docid`),
  CONSTRAINT `schedule_ibfk_1` FOREIGN KEY (`docid`) REFERENCES `doctor` (`docid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `schedule`
--

INSERT INTO `schedule` (`docid`, `title`, `scheduledate`, `scheduletime`, `nop`) VALUES
-- Dr. John Smith (Child Psychiatrist) - Available slots this week
(1, 'Morning Session', '2025-04-01', '09:00:00', 1),
(1, 'Afternoon Session', '2025-04-01', '14:00:00', 1),
(1, 'Morning Session', '2025-04-02', '10:00:00', 1),
(1, 'Afternoon Session', '2025-04-02', '15:00:00', 1),

-- Dr. Sarah Johnson (Child Psychologist) - Available slots this week
(2, 'Morning Session', '2025-04-03', '09:30:00', 1),
(2, 'Afternoon Session', '2025-04-03', '14:30:00', 1),
(2, 'Morning Session', '2025-04-04', '10:30:00', 1),
(2, 'Afternoon Session', '2025-04-04', '15:30:00', 1),

-- Dr. Michael Brown (Developmental Specialist) - Later dates
(3, 'Morning Session', '2025-04-27', '09:00:00', 1),
(3, 'Afternoon Session', '2025-04-27', '14:00:00', 1),
(3, 'Morning Session', '2025-04-28', '10:00:00', 1),
(3, 'Afternoon Session', '2025-04-28', '15:00:00', 1),

-- Dr. Emily Davis (Behavioral Therapist) - Later dates
(4, 'Morning Session', '2025-04-28', '09:30:00', 1),
(4, 'Afternoon Session', '2025-04-28', '14:30:00', 1),
(4, 'Morning Session', '2025-04-29', '10:30:00', 1),
(4, 'Afternoon Session', '2025-04-29', '15:30:00', 1),

-- Dr. David Wilson (Child Neurologist) - Later dates
(5, 'Morning Session', '2025-04-29', '09:00:00', 1),
(5, 'Afternoon Session', '2025-04-29', '14:00:00', 1),
(5, 'Morning Session', '2025-04-30', '10:00:00', 1),
(5, 'Afternoon Session', '2025-04-30', '15:00:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE IF NOT EXISTS `appointment` (
  `appoid` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `scheduleid` int(11) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`appoid`),
  KEY `pid` (`pid`),
  KEY `scheduleid` (`scheduleid`),
  CONSTRAINT `appointment_ibfk_1` FOREIGN KEY (`pid`) REFERENCES `patient` (`pid`) ON DELETE CASCADE,
  CONSTRAINT `appointment_ibfk_2` FOREIGN KEY (`scheduleid`) REFERENCES `schedule` (`scheduleid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `assessment_categories`
--

DROP TABLE IF EXISTS `assessment_categories`;
CREATE TABLE `assessment_categories` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL,
    `description` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `assessment_categories`
--

INSERT INTO `assessment_categories` (`name`, `description`) VALUES
('relating_to_people', 'How the child interacts and relates to other people, including eye contact, social responses, and engagement with others.'),
('emotional_response', 'The child\'s emotional reactions, expressions, and ability to show appropriate feelings in different situations.'),
('body_use', 'How the child uses their body, including coordination, motor skills, and physical movements.'),
('object_use', 'How the child interacts with and uses objects, toys, and other items in their environment.'),
('listening_response', 'How the child responds to sounds, verbal communication, and follows verbal instructions.'),
('adaptation_to_change', 'How well the child adapts to changes in routine, environment, or activities.'),
('fear_or_nervousness', 'The child\'s anxiety levels, fear responses, and ability to cope with stressful situations.'),
('visual_response', 'How the child responds to visual stimuli, including eye contact and visual tracking.'),
('verbal_communication', 'The child\'s verbal communication skills, including speech, language development, and conversation abilities.'),
('activity_level', 'The child\'s energy levels, attention span, and ability to regulate activity appropriately.');

-- --------------------------------------------------------

--
-- Table structure for table `assessments`
--

DROP TABLE IF EXISTS `assessment_scores`;
DROP TABLE IF EXISTS `assessment_responses`;
DROP TABLE IF EXISTS `assessments`;
CREATE TABLE `assessments` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `patient_id` INT NOT NULL,
    `child_name` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20) NOT NULL,
    `gender` ENUM('male', 'female', 'other') NOT NULL,
    `test_date` DATE NOT NULL,
    `birth_date` DATE NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`patient_id`) REFERENCES `patient`(`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `assessment_scores`
--

CREATE TABLE `assessment_scores` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `assessment_id` INT NOT NULL,
    `category` VARCHAR(50) NOT NULL,
    `score` DECIMAL(4,2) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`assessment_id`) REFERENCES `assessments`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
