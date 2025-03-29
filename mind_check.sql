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
('patient4@mindcheck.com', 'p');

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
-- Table structure for table `doctor`
--

CREATE TABLE `doctor` (
  `docid` int(11) NOT NULL AUTO_INCREMENT,
  `docemail` varchar(255) NOT NULL,
  `docname` varchar(255) DEFAULT NULL,
  `docpassword` varchar(255) NOT NULL,
  `docnic` varchar(15) DEFAULT NULL,
  `doctel` varchar(15) DEFAULT NULL,
  `specialties` int(2) DEFAULT NULL,
  `docexp` int(11) DEFAULT NULL,
  PRIMARY KEY (`docid`),
  KEY `docemail` (`docemail`),
  CONSTRAINT `doctor_webuser_fk` FOREIGN KEY (`docemail`) REFERENCES `webuser` (`email`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `doctor`
--

INSERT INTO `doctor` (`docid`, `docemail`, `docname`, `docpassword`, `docnic`, `doctel`, `specialties`, `docexp`) VALUES
(1, 'doctor1@mindcheck.com', 'Doctor 1', '123', '1000000001', '0300-1000001', 1, 5),
(2, 'doctor2@mindcheck.com', 'Doctor 2', '123', '1000000002', '0300-1000002', 2, 3),
(3, 'doctor3@mindcheck.com', 'Doctor 3', '123', '1000000003', '0300-1000003', 3, 4),
(4, 'doctor4@mindcheck.com', 'Doctor 4', '123', '1000000004', '0300-1000004', 4, 6);

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
  CONSTRAINT `schedule_doctor_fk` FOREIGN KEY (`docid`) REFERENCES `doctor` (`docid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `schedule`
--

INSERT INTO `schedule` (`scheduleid`, `docid`, `title`, `scheduledate`, `scheduletime`, `nop`) VALUES
(1, 1, 'Regular Checkup', '2022-06-10', '12:32:00', 1),
(4, 1, 'Follow-up', '2022-06-10', '12:32:00', 1),
(5, 1, 'Evening Session', '2022-06-10', '20:35:00', 1),
(6, 1, 'Consultation', '2022-06-10', '20:35:00', 1),
(7, 1, 'Regular Visit', '2022-06-24', '20:36:00', 1),
(8, 1, 'Check-up', '2022-06-10', '13:33:00', 1),
(9, 1, 'General Session', '2023-10-08', '08:35:00', 283),
(12, 1, 'Azwa Doctor', '2023-10-18', '05:57:00', 25),
(13, 4, 'Consultation', '2023-11-29', '00:25:00', 20),
(14, 4, 'Regular Session', '2023-11-23', '09:24:00', 21);

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE `appointment` (
  `appoid` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `apponum` int(11) NOT NULL,
  `scheduleid` int(11) NOT NULL,
  `appodate` date NOT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`appoid`),
  KEY `pid` (`pid`),
  KEY `scheduleid` (`scheduleid`),
  CONSTRAINT `appointment_patient_fk` FOREIGN KEY (`pid`) REFERENCES `patient` (`pid`) ON DELETE CASCADE,
  CONSTRAINT `appointment_schedule_fk` FOREIGN KEY (`scheduleid`) REFERENCES `schedule` (`scheduleid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `appointment`
--

INSERT INTO `appointment` (`appoid`, `pid`, `apponum`, `scheduleid`, `appodate`, `status`, `reason`, `created_at`) VALUES
(1, 1, 1, 1, '2022-06-03', 'completed', NULL, '2022-06-03 10:00:00'),
(2, 1, 1, 9, '2023-10-07', 'completed', NULL, '2023-10-07 08:30:00'),
(3, 1, 1, 12, '2023-10-15', 'completed', NULL, '2023-10-15 05:57:00'),
(4, 3, 2, 12, '2023-10-15', 'completed', NULL, '2023-10-15 06:30:00'),
(5, 4, 3, 12, '2023-10-16', 'completed', NULL, '2023-10-16 05:57:00'),
(6, 1, 1, 13, '2023-11-13', 'pending', NULL, '2023-11-13 00:25:00'),
(7, 1, 1, 14, '2023-11-13', 'pending', NULL, '2023-11-13 09:24:00');

-- --------------------------------------------------------

--
-- Table structure for table `specialties`
--

CREATE TABLE `specialties` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `sname` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `specialties`
--

INSERT INTO `specialties` (`id`, `sname`, `description`) VALUES
(1, 'Child & Adolescent Psychiatry', 'Specializes in diagnosing and treating mental health disorders in children and teenagers'),
(2, 'Behavioral Psychology', 'Focuses on analyzing and modifying problematic behaviors in children'),
(3, 'Developmental Psychology', 'Expertise in child development, developmental delays, and disorders'),
(4, 'Pediatric Neuropsychology', 'Specializes in brain-behavior relationships and cognitive development'),
(5, 'Clinical Child Psychology', 'Focuses on emotional and behavioral disorders in children'),
(6, 'Educational Psychology', 'Specializes in learning disabilities and educational interventions'),
(7, 'Family Therapy', 'Focuses on family dynamics and their impact on child development'),
(8, 'Autism Spectrum Disorders', 'Specializes in diagnosis and treatment of autism and related conditions'),
(9, 'ADHD & Learning Disorders', 'Expertise in attention deficit disorders and learning difficulties'),
(10, 'Child Behavioral Therapy', 'Focuses on behavioral interventions and modifications'),
(11, 'Pediatric Mental Health', 'General child mental health assessment and treatment'),
(12, 'Early Intervention Specialist', 'Focuses on early detection and intervention of developmental issues');

-- --------------------------------------------------------

--
-- Table structure for table `assessment_questions`
--

CREATE TABLE `assessment_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(50) NOT NULL,
  `question` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `assessment_questions`
--

INSERT INTO `assessment_questions` (`category`, `question`) VALUES
('social_interaction', 'Does the child make eye contact when spoken to?'),
('social_interaction', 'Does the child respond to their name when called?'),
('social_interaction', 'Does the child show interest in playing with other children?'),
('communication', 'Does the child use gestures like pointing or waving?'),
('communication', 'Does the child engage in pretend play?'),
('communication', 'Can the child follow simple instructions?'),
('behavior_patterns', 'Does the child have strong reactions to certain sounds or textures?'),
('behavior_patterns', 'Does the child engage in repetitive movements?'),
('behavior_patterns', 'Does the child have difficulty with changes in routine?'),
('emotional_regulation', 'Can the child express basic emotions appropriately?'),
('emotional_regulation', 'Does the child show empathy towards others?'),
('emotional_regulation', 'Can the child calm themselves when upset?');

-- --------------------------------------------------------

--
-- Table structure for table `assessments`
--

CREATE TABLE `assessments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `child_name` varchar(255) NOT NULL,
  `age` int(11) NOT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `total_score` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`),
  CONSTRAINT `assessments_patient_fk` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`pid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `assessment_responses`
--

CREATE TABLE `assessment_responses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `assessment_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `assessment_id` (`assessment_id`),
  KEY `question_id` (`question_id`),
  CONSTRAINT `assessment_responses_assessment_fk` FOREIGN KEY (`assessment_id`) REFERENCES `assessments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assessment_responses_question_fk` FOREIGN KEY (`question_id`) REFERENCES `assessment_questions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
