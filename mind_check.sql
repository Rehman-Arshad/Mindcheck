-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 14, 2023 at 03:48 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

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

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `aemail` varchar(255) NOT NULL,
  `apassword` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`aemail`, `apassword`) VALUES
('admin@edoc.com', '123');

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE `appointment` (
  `appoid` int(11) NOT NULL,
  `pid` int(10) DEFAULT NULL,
  `apponum` int(3) DEFAULT NULL,
  `scheduleid` int(10) DEFAULT NULL,
  `appodate` date DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `appointment`
--

INSERT INTO `appointment` (`appoid`, `pid`, `apponum`, `scheduleid`, `appodate`) VALUES
(1, 1, 1, 1, '2022-06-03'),
(2, 1, 1, 9, '2023-10-07'),
(3, 1, 1, 12, '2023-10-15'),
(4, 3, 2, 12, '2023-10-15'),
(5, 4, 3, 12, '2023-10-16'),
(6, 1, 1, 13, '2023-11-13'),
(7, 1, 1, 14, '2023-11-13');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(30) NOT NULL,
  `name` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `date_updated`) VALUES
(1, 'Sample', 'Samples Only', '2023-10-22 22:59:17'),
(2, 'Programming', 'Sample category 2', '2020-10-16 14:57:13'),
(3, 'Others', 'Sed porta nisi quis nunc gravida, ut ornare velit vulputate. Aenean dictum mauris suscipit ante imperdiet tincidunt. Nulla accumsan mauris eu libero semper, eget faucibus mi vulputate. In hac habitasse platea dictumst. Etiam pulvinar quam quis sapien consectetur, ac volutpat risus ultricies. Suspendisse vel hendrerit massa. Nullam tincidunt purus sit amet elit egestas, sit amet tincidunt odio luctus. Nam eget eros dui. In ultricies nisl id tortor elementum feugiat. Mauris et bibendum nisl, in ultricies turpis.', '2020-10-16 14:58:12'),
(4, 'Tag 1', 'In ipsum magna, aliquam ut fringilla id, finibus vitae est. Donec accumsan nec velit ut dapibus. Praesent at mollis diam. Nulla facilisi. Curabitur tempor blandit purus id pellentesque. Quisque sed ligula aliquam nulla luctus sodales. In risus velit, porttitor at lacus et, consectetur ultrices dolor. Phasellus ac venenatis nibh. Suspendisse potenti. Praesent faucibus ligula sit amet ornare varius. Integer sit amet nunc arcu.\r\n\r\n', '2020-10-17 13:15:31'),
(5, 'Tag 2', 'Phasellus vel placerat ante. Cras sollicitudin quis lacus a blandit. Suspendisse vel cursus mauris. Nulla malesuada metus varius, iaculis lacus vel, facilisis nibh. Cras congue viverra erat, ut hendrerit nunc convallis id. Etiam scelerisque sit amet est nec auctor. Curabitur faucibus convallis tellus, a auctor urna efficitur nec. Praesent luctus malesuada fermentum. Maecenas vestibulum nisi sem. Donec non rhoncus tellus.', '2020-10-17 13:15:43'),
(6, 'Tag 3', 'Vestibulum vel maximus dolor. Quisque in accumsan purus. Duis ut sapien nec massa semper elementum auctor eget odio. Donec vulputate hendrerit libero quis sollicitudin. Sed et varius justo. Maecenas consectetur mollis finibus. Integer at lectus vitae ex commodo condimentum in ut lectus. Fusce porttitor commodo eros, ut condimentum neque faucibus sed. Sed tristique luctus suscipit. Cras tincidunt quam metus, a facilisis justo luctus sed.', '2020-10-17 13:16:23'),
(7, 'test categoryss', 'test Descriptionsss', '2023-10-22 18:04:26');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(30) NOT NULL,
  `topic_id` int(30) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `comment` text NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `topic_id`, `user_id`, `comment`, `date_created`, `date_updated`) VALUES
(1, 2, '1', 'Sample Comment', '2020-10-16 16:55:39', '2020-10-16 16:55:39'),
(2, 2, '2', 'test', '2020-10-16 17:04:34', '2020-10-16 17:04:34'),
(3, 2, '1', 'sample', '2020-10-17 08:54:46', '2020-10-17 08:54:46'),
(4, 2, '1', 'asdasd', '2020-10-17 09:42:04', '2020-10-17 09:42:04'),
(6, 4, 'patient@edoc.com', 'first comment', '2023-10-22 17:46:53', '2023-10-22 17:46:53'),
(7, 5, 'patient@edoc.com', 'faced recently.', '2023-10-24 21:34:47', '2023-10-24 21:34:47'),
(8, 5, 'patient@edoc.com', 'issues to be solved&lt;p&gt;&lt;br&gt;&lt;/p&gt;', '2023-10-24 21:35:00', '2023-10-24 21:35:00');

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(100) NOT NULL,
  `status` int(11) NOT NULL,
  `createdDate` int(11) NOT NULL,
  `createdBy` int(11) NOT NULL,
  `modifiedDate` int(11) NOT NULL,
  `modifiedBy` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_general_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`id`, `name`, `description`, `status`, `createdDate`, `createdBy`, `modifiedDate`, `modifiedBy`) VALUES
(1, 'BSSE', 'Bachelors in Software Engineering', 1, 1, 1, 0, 0),
(2, 'BSCS', 'Bachelors in Computer Sciences', 1, 1, 1, 1, 1),
(3, 'BSIT', 'Bachelors in Information Technology', 1, 1, 1, 1, 1),
(4, 'BS Psychology', 'Bachelors in Psychology', 1, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `forum_views`
--

CREATE TABLE `forum_views` (
  `id` int(30) NOT NULL,
  `topic_id` int(30) NOT NULL,
  `user_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `forum_views`
--

INSERT INTO `forum_views` (`id`, `topic_id`, `user_id`) VALUES
(1, 2, '2'),
(2, 2, '1'),
(3, 2, '3'),
(4, 4, ''),
(5, 5, 'amna@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

CREATE TABLE `patient` (
  `pid` int(11) NOT NULL,
  `pemail` varchar(255) DEFAULT NULL,
  `pname` varchar(255) DEFAULT NULL,
  `ppassword` varchar(255) DEFAULT NULL,
  `paddress` varchar(255) DEFAULT NULL,
  `pnic` varchar(15) DEFAULT NULL,
  `pdob` date DEFAULT NULL,
  `ptel` varchar(15) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `patient`
--

INSERT INTO `patient` (`pid`, `pemail`, `pname`, `ppassword`, `paddress`, `pnic`, `pdob`, `ptel`) VALUES
(1, 'patient@edoc.com', 'Test Patient', '123', '1', '0000000000', '2000-01-01', '1'),
(2, 'emhashenudara@gmail.com', 'Hashen Udara', '123', 'Sri Lanka', '0110000000', '2022-06-03', '0700000000'),
(3, 'amna@gmail.com', NULL, '123', '2', '11559', NULL, '2'),
(4, '11559@riphah.edu.pk', NULL, '123', '1', '11559', NULL, '1');

-- --------------------------------------------------------

--
-- Table structure for table `doctor`
--

CREATE TABLE `doctor` (
  `docid` int(11) NOT NULL,
  `docemail` varchar(255) DEFAULT NULL,
  `docname` varchar(255) DEFAULT NULL,
  `docpassword` varchar(255) DEFAULT NULL,
  `docnic` varchar(15) DEFAULT NULL,
  `doctel` varchar(15) DEFAULT NULL,
  `specialties` int(2) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `doctor`
--

INSERT INTO `doctor` (`docid`, `docemail`, `docname`, `docpassword`, `docnic`, `doctel`, `specialties`) VALUES
(1, 'azwa@gmail.com', 'Azwa ', '123', '000000000', '0110000000', 1),
(4, 'ali@gmail.com', 'ali', '123', '999', '+923080195783', 1),
(3, 'azwarajput@gmail.com', 'azwa rajput', '123', '1224', '+923080195783', 1),
(5, 'ahmad@gmail.com', 'ahmad', '123', '111', '+923080195783', 1);

-- --------------------------------------------------------

--
-- Table structure for table `replies`
--

CREATE TABLE `replies` (
  `id` int(30) NOT NULL,
  `comment_id` int(30) NOT NULL,
  `reply` text NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `replies`
--

INSERT INTO `replies` (`id`, `comment_id`, `reply`, `user_id`, `date_created`, `date_updated`) VALUES
(1, 1, 'sample reply', '1', '2020-10-17 09:48:06', '0000-00-00 00:00:00'),
(2, 2, '&lt;span style=&quot;color: rgb(0, 0, 0); font-family: &amp;quot;Open Sans&amp;quot;, Arial, sans-serif; font-size: 16px; text-align: justify;&quot;&gt;Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec elementum nunc bibendum, luctus diam id, tincidunt nisl. Vestibulum turpis arcu, fringilla sed lacus in, eleifend vulputate purus. Mauris sollicitudin metus in risus finibus fringilla.&lt;/span&gt;&lt;br&gt;', '1', '2020-10-17 09:48:57', '0000-00-00 00:00:00'),
(3, 1, 'asdasd&lt;p&gt;asdasd&lt;/p&gt;', '1', '2020-10-17 09:52:02', '0000-00-00 00:00:00'),
(4, 1, 's', '1', '2020-10-17 10:01:00', '0000-00-00 00:00:00'),
(5, 1, 'asdaasd', '1', '2020-10-17 10:01:06', '0000-00-00 00:00:00'),
(6, 1, 'asdasd&lt;p&gt;&lt;br&gt;&lt;/p&gt;', '1', '2020-10-17 10:01:53', '0000-00-00 00:00:00'),
(7, 1, 'asdsdsd', '1', '2020-10-17 10:16:09', '0000-00-00 00:00:00'),
(8, 1, '1', '1', '2020-10-17 10:16:13', '0000-00-00 00:00:00'),
(9, 1, '2', '1', '2020-10-17 10:16:17', '0000-00-00 00:00:00'),
(11, 6, 'first relp', 'patient@edoc.com', '2023-10-22 17:48:38', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `reportform`
--

CREATE TABLE `reportform` (
  `id` int(11) NOT NULL,
  `patientid` int(11) NOT NULL,
  `patienttype` varchar(50) NOT NULL,
  `casetype` varchar(50) NOT NULL,
  `socialmedia` varchar(50) NOT NULL,
  `discriminatoryharrasment` varchar(50) NOT NULL,
  `sexualharrasement` varchar(50) NOT NULL,
  `cyberbullying` varchar(50) NOT NULL,
  `relationshipbreakdown` varchar(50) NOT NULL,
  `panicattacks` varchar(50) NOT NULL,
  `anxiety` varchar(50) NOT NULL,
  `depression` varchar(50) NOT NULL,
  `comments` text DEFAULT NULL,
  `doctorid` varchar(50) NOT NULL,
  `submissiondate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reportform`
--

INSERT INTO `reportform` (`id`, `patientid`, `patienttype`, `casetype`, `socialmedia`, `discriminatoryharrasment`, `sexualharrasement`, `cyberbullying`, `relationshipbreakdown`, `panicattacks`, `anxiety`, `depression`, `comments`, `doctorid`, `submissiondate`, `status`) VALUES
(2, 1, 'perpetrator', 'critical case', 'facebook', 'dharrasment', 'sharrasement', 'cb', 'rb', 'pa', 'anxiety', 'depression', NULL, '12', '2023-11-06 17:28:32', 1),
(3, 1, 'perpetrator', 'critical case', 'facebook', 'dharrasment', 'sharrasement', 'cb', 'rb', 'pa', 'anxiety', 'depression', NULL, '12', '2023-11-06 17:34:28', 1),
(4, 1, 'perpetrator', 'critical case', 'facebook', 'dharrasment', 'sharrasement', 'cb', 'rb', 'pa', 'anxiety', 'depression', NULL, '12', '2023-11-06 17:36:23', 1),
(5, 1, 'perpetrator', 'critical case', 'facebook', 'dharrasment', 'sharrasement', 'cb', 'rb', 'pa', 'anxiety', 'depression', NULL, '12', '2023-11-06 17:43:12', 1),
(6, 1, 'perpetrator', 'critical case', 'facebook', 'dharrasment', 'sharrasement', 'cb', 'rb', 'pa', 'anxiety', 'depression', NULL, '12', '2023-11-06 17:43:20', 1),
(7, 0, '', '', '', '', '', '', '', '', '', '', NULL, '', '2023-11-06 17:57:25', 1),
(8, 0, '', '', '', '', '', '', '', '', '', '', NULL, '', '2023-11-06 17:57:30', 1),
(9, 0, 'perpetrator', 'CriticalCase', '', '', '', '', '', '', '', '', NULL, '', '2023-11-06 17:58:00', 1),
(10, 0, 'perpetrator', 'CriticalCase', '', '', '', '', '', '', '', '', NULL, '', '2023-11-06 18:03:36', 1),
(11, 1, 'perpetrator', 'CriticalCase', 'Instagram', '', '', '', '', '', '', '', NULL, 'azwa@gmail.com', '2023-11-06 18:10:31', 1),
(12, 1, 'victim', 'CounselingCaseOnly', 'Whatsapp', 'DiscriminatoryHarrasment', 'Sexual Harrasement', 'Cyber Bullying', 'RelationshipBreakdown', 'PanicAttacks', 'Anxiety', 'Depression', NULL, 'azwa@gmail.com', '2023-11-06 18:15:08', 1),
(13, 1, 'bystander', 'CounselingCaseOnly', 'Others', 'DiscriminatoryHarrasment', 'Sexual Harrasement', 'Cyber Bullying', 'RelationshipBreakdown', 'PanicAttacks', 'Anxiety', 'Depression', 'first comment', 'azwa@gmail.com', '2023-11-06 18:20:32', 1),
(14, 3, 'bystander', 'CounselingCaseOnly', 'Twitter', 'DiscriminatoryHarrasment', '', '', '', '', 'Anxiety', '', 'ok', 'azwa@gmail.com', '2023-11-06 19:21:50', 1),
(15, 1, 'bystander', 'CounselingCaseOnly', 'Facebook', '', 'Sexual Harrasement', '', '', '', 'Anxiety', '', '.', 'ali@gmail.com', '2023-11-13 16:59:09', 1);

-- --------------------------------------------------------

--
-- Table structure for table `schedule`
--

CREATE TABLE `schedule` (
  `scheduleid` int(11) NOT NULL,
  `docid` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `scheduledate` date DEFAULT NULL,
  `scheduletime` time DEFAULT NULL,
  `nop` int(4) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `schedule`
--

INSERT INTO `schedule` (`scheduleid`, `docid`, `title`, `scheduledate`, `scheduletime`, `nop`) VALUES
(12, '1', 'Azwa Doctor', '2023-10-18', '05:57:00', 25),
(4, '1', '1', '2022-06-10', '12:32:00', 1),
(5, '1', '1', '2022-06-10', '20:35:00', 1),
(6, '1', '12', '2022-06-10', '20:35:00', 1),
(7, '1', '1', '2022-06-24', '20:36:00', 1),
(8, '1', '12', '2022-06-10', '13:33:00', 1),
(10, '2', 'ok', '2023-10-08', '08:35:00', 283),
(13, '4', 'con', '2023-11-29', '00:25:00', 20),
(14, '5', 'ok', '2023-11-23', '09:24:00', 21);

-- --------------------------------------------------------

--
-- Table structure for table `semester`
--

CREATE TABLE `semester` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(100) NOT NULL,
  `status` int(11) NOT NULL,
  `deptId` int(11) NOT NULL,
  `createdDate` int(11) NOT NULL,
  `createdBy` int(11) NOT NULL,
  `modifiedDate` int(11) NOT NULL,
  `modifiedBy` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_general_ci;

--
-- Dumping data for table `semester`
--

INSERT INTO `semester` (`id`, `name`, `description`, `status`, `deptId`, `createdDate`, `createdBy`, `modifiedDate`, `modifiedBy`) VALUES
(1, 'Semester I', 'Semester I', 1, 1, 1, 1, 1, 1),
(2, 'Semester II', 'Semester II', 1, 1, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `specialties`
--

CREATE TABLE `specialties` (
  `id` int(2) NOT NULL,
  `sname` varchar(50) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `specialties`
--

INSERT INTO `specialties` (`id`, `sname`) VALUES
(1, 'Accident and emergency medicine'),
(2, 'Harrasment'),
(3, 'Anaesthetics'),
(4, 'Biological hematology'),
(5, 'Cardiology'),
(6, 'Child psychiatry'),
(7, 'Clinical biology'),
(8, 'Clinical chemistry'),
(9, 'Clinical neurophysiology'),
(10, 'Clinical radiology'),
(11, 'Dental, oral and maxillo-facial surgery'),
(12, 'Dermato-venerology'),
(13, 'Dermatology'),
(14, 'Endocrinology'),
(15, 'Gastro-enterologic surgery'),
(16, 'Gastroenterology'),
(17, 'General hematology'),
(18, 'General Practice'),
(19, 'General surgery'),
(20, 'Geriatrics'),
(21, 'Immunology'),
(22, 'Infectious diseases'),
(23, 'Internal medicine'),
(24, 'Laboratory medicine'),
(25, 'Maxillo-facial surgery'),
(26, 'Microbiology'),
(27, 'Nephrology'),
(28, 'Neuro-psychiatry'),
(29, 'Neurology'),
(30, 'Neurosurgery'),
(31, 'Nuclear medicine'),
(32, 'Obstetrics and gynecology'),
(33, 'Occupational medicine'),
(34, 'Ophthalmology'),
(35, 'Orthopaedics'),
(36, 'Otorhinolaryngology'),
(37, 'Paediatric surgery'),
(38, 'Paediatrics'),
(39, 'Pathology'),
(40, 'Pharmacology'),
(41, 'Physical medicine and rehabilitation'),
(42, 'Plastic surgery'),
(43, 'Podiatric Medicine'),
(44, 'Podiatric Surgery'),
(45, 'Psychiatry'),
(46, 'Public health and Preventive Medicine'),
(47, 'Radiology'),
(48, 'Radiotherapy'),
(49, 'Respiratory medicine'),
(50, 'Rheumatology'),
(51, 'Stomatology'),
(52, 'Thoracic surgery'),
(53, 'Tropical medicine'),
(54, 'Urology'),
(55, 'Vascular surgery'),
(56, 'Venereology');

-- --------------------------------------------------------

--
-- Table structure for table `topics`
--

CREATE TABLE `topics` (
  `id` int(30) NOT NULL,
  `category_ids` text NOT NULL,
  `title` varchar(250) NOT NULL,
  `content` text NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `topics`
--

INSERT INTO `topics` (`id`, `category_ids`, `title`, `content`, `user_id`, `date_created`) VALUES
(1, '3,2,1', 'Sample Topic', '&lt;h2 style=&quot;margin-bottom: 0px; font-family: &amp;quot;Open Sans&amp;quot;, Arial, sans-serif; color: rgb(0, 0, 0); padding: 0px; text-align: justify;&quot;&gt;Sample Topic&lt;/h2&gt;&lt;p style=&quot;margin-bottom: 15px; padding: 0px;&quot;&gt;Sed porta nisi quis nunc gravida, ut ornare velit vulputate. Aenean dictum mauris suscipit ante imperdiet tincidunt. Nulla accumsan mauris eu libero semper, eget faucibus mi vulputate. In hac habitasse platea dictumst. Etiam pulvinar quam quis sapien consectetur, ac volutpat risus ultricies. Suspendisse vel hendrerit massa. Nullam tincidunt purus sit amet elit egestas, sit amet tincidunt odio luctus. Nam eget eros dui. In ultricies nisl id tortor elementum feugiat. Mauris et bibendum nisl, in ultricies turpis. Maecenas elit justo, molestie vel porta sit amet, commodo et sapien. Nulla porta non leo quis suscipit. Integer eu commodo nisi. Fusce eu sodales lacus.&lt;/p&gt;&lt;p&gt;&lt;br style=&quot;text-align: justify;&quot;&gt;&lt;/p&gt;', '1', '2020-10-16 12:25:14'),
(2, '2', 'Topic 2', '&lt;p style=&quot;margin-bottom: 15px; padding: 0px; text-align: justify; color: rgb(0, 0, 0); font-family: &amp;quot;Open Sans&amp;quot;, Arial, sans-serif;&quot;&gt;Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec elementum nunc bibendum, luctus diam id, tincidunt nisl. Vestibulum turpis arcu, fringilla sed lacus in, eleifend vulputate purus. Mauris sollicitudin metus in risus finibus fringilla. Praesent a magna eget arcu pretium consectetur a semper nisi. Quisque ut enim blandit, pellentesque quam a, ullamcorper diam. Suspendisse eget ultrices felis. Donec eu tortor lobortis, luctus quam quis, lobortis purus. Nunc varius sagittis nisi, in posuere mauris accumsan ac. Integer a suscipit risus. Proin ultrices diam ac nulla mattis vehicula. Aliquam metus urna, fringilla a suscipit vehicula, sollicitudin non neque. Integer tincidunt porta neque in bibendum. Ut cursus, nunc vitae consequat ullamcorper, neque neque viverra sem, sed rutrum metus ante non odio. Vivamus leo orci, consequat et sagittis vel, varius eu mi.&lt;/p&gt;&lt;p style=&quot;margin-bottom: 15px; padding: 0px; text-align: justify; color: rgb(0, 0, 0); font-family: &amp;quot;Open Sans&amp;quot;, Arial, sans-serif;&quot;&gt;Vivamus id odio in diam tincidunt posuere. Morbi tempor, sapien vitae tristique placerat, tortor enim sollicitudin erat, quis ornare erat metus sed ex. Mauris accumsan tristique elit, at tempus odio auctor eget. Nullam ullamcorper convallis orci id condimentum. Donec laoreet est ut feugiat aliquam. Proin porta consectetur hendrerit. Quisque vitae nunc a orci fringilla lobortis. Ut bibendum purus sit amet molestie viverra. Quisque elementum mollis est, sit amet dignissim ligula semper sed. Mauris in nunc mi. Praesent ac felis eget purus ullamcorper porta. Fusce non laoreet mauris. In in sem a sem molestie varius sed id libero.&lt;/p&gt;&lt;p style=&quot;margin-bottom: 15px; padding: 0px; text-align: justify; color: rgb(0, 0, 0); font-family: &amp;quot;Open Sans&amp;quot;, Arial, sans-serif;&quot;&gt;&lt;br&gt;&lt;/p&gt;&lt;pre style=&quot;height: 366px;&quot;&gt;&amp;lt;?php&amp;nbsp;&lt;br style=&quot;height: 366px;&quot;&gt;echo &quot;Hello World&quot;;&lt;br style=&quot;height: 366px;&quot;&gt;?&amp;gt;&lt;/pre&gt;&lt;p style=&quot;height: 366px;&quot;&gt;Aliquam pharetra mollis massa, eu luctus leo vehicula id. Quisque viverra nisl in lorem tincidunt, in mollis ipsum faucibus. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Donec vitae massa ut nunc dapibus consequat a quis elit. Ut felis turpis, tincidunt sed sem vel, auctor bibendum augue. Mauris tempus nisl eu pharetra hendrerit. Sed tincidunt enim quam, sit amet rhoncus purus euismod at. Quisque consectetur libero non elit dictum, at luctus turpis eleifend. Quisque vitae eros interdum, ultricies arcu sed, vulputate justo. Maecenas a metus eget mi tristique porttitor a ac tortor. Vivamus venenatis ornare dolor, at elementum justo sollicitudin quis. Praesent blandit consectetur est nec dapibus. Aliquam erat volutpat. Proin vitae neque vitae elit mattis mollis. Integer ornare pulvinar lectus, vitae venenatis leo mollis nec.&lt;span style=&quot;text-align: justify;&quot;&gt;Sed commodo dui neque, ut faucibus nisl sagittis quis. Nullam ut semper quam, vitae maximus sem. Donec arcu dolor, consectetur eget feugiat gravida, varius sit amet odio. Mauris et lacus in ex rutrum tincidunt. Duis at nibh nec tortor pellentesque lacinia eu non diam. Maecenas id porttitor orci. Maecenas vel consectetur ligula. Donec lacinia et mi ac vulputate.&lt;/span&gt;&lt;span style=&quot;color: rgb(0, 0, 0); font-family: &amp;quot;Open Sans&amp;quot;, Arial, sans-serif; text-align: justify;&quot;&gt;Etiam laoreet rutrum orci, non euismod ex auctor sit amet. Aliquam a pharetra nisi, eget facilisis lacus. Vestibulum pellentesque ut felis ac maximus. Fusce nulla sapien, lobortis sed ex non, tempor varius tellus. Ut rhoncus sapien ante, non luctus libero aliquet at. Quisque gravida ligula a lacus convallis convallis. Curabitur in tempus nunc. Nullam bibendum malesuada malesuada. Quisque eget dapibus erat, et tristique nunc. Maecenas placerat tempus ex in dictum.&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;height: 366px;&quot;&gt;&lt;span style=&quot;color: rgb(0, 0, 0); font-family: &amp;quot;Open Sans&amp;quot;, Arial, sans-serif; text-align: justify;&quot;&gt;Mauris ut placerat urna, ut luctus tellus. Sed eleifend pellentesque vulputate. Morbi vestibulum ultricies placerat. Pellentesque quis orci posuere, mattis felis at, pulvinar enim. Ut odio velit, consequat quis tincidunt sit amet, dapibus ut elit. Duis quam dolor, bibendum quis purus vel, commodo porta ex. Vestibulum euismod eros ut tortor gravida malesuada. Sed sagittis auctor risus eget scelerisque. Donec cursus lorem vitae sapien tempor, quis placerat lorem bibendum. Nunc eu sagittis urna. Maecenas quis fermentum ex.&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;height: 366px;&quot;&gt;&lt;span style=&quot;color: rgb(0, 0, 0); font-family: &amp;quot;Open Sans&amp;quot;, Arial, sans-serif; text-align: justify;&quot;&gt;Integer a leo sem. Suspendisse fringilla rhoncus eros eu tempor. In pellentesque blandit felis eget bibendum. Curabitur tristique laoreet diam, id sodales turpis ultrices non. Mauris et neque cursus nunc auctor dapibus quis vel enim. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse sit amet nisl vitae enim tincidunt luctus. Sed nisi libero, tincidunt quis orci ac, vestibulum luctus nibh. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Donec porttitor accumsan lorem, imperdiet ultrices justo ultricies ut. Suspendisse potenti. Duis luctus arcu vitae massa semper fermentum. Donec eget pulvinar sapien, sit amet sodales nisi. Sed euismod metus vel turpis convallis, vehicula pharetra eros venenatis.&lt;/span&gt;&lt;/p&gt;', '1', '2020-10-16 16:07:54'),
(3, '3,2', 'first test', 'frst testing', '', '2023-10-22 17:34:15'),
(4, '3,2', 'first test', 'frst testing sd', 'patient@edoc.com', '2023-10-22 17:35:48'),
(5, '4', 'Harrasment ', 'Discussions on social media harrasment cases.', 'patient@edoc.com', '2023-10-24 21:31:21');

-- --------------------------------------------------------

--
-- Table structure for table `webuser`
--

CREATE TABLE `webuser` (
  `email` varchar(255) NOT NULL,
  `usertype` char(1) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `webuser`
--

INSERT INTO `webuser` (`email`, `usertype`) VALUES
('admin@edoc.com', 'a'),
('azwa@gmail.com', 'd'),
('patient@edoc.com', 'p'),
('emhashenudara@gmail.com', 'p'),
('ali@gmail.com', 'd'),
('azwarajput@gmail.com', 'd'),
('amna@gmail.com', 'p'),
('11559@riphah.edu.pk', 'p'),
('ahmad@gmail.com', 'd');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`aemail`);

--
-- Indexes for table `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`appoid`),
  ADD KEY `pid` (`pid`),
  ADD KEY `scheduleid` (`scheduleid`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `forum_views`
--
ALTER TABLE `forum_views`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patient`
--
ALTER TABLE `patient`
  ADD PRIMARY KEY (`pid`);

--
-- Indexes for table `doctor`
--
ALTER TABLE `doctor`
  ADD PRIMARY KEY (`docid`),
  ADD KEY `specialties` (`specialties`);

--
-- Indexes for table `replies`
--
ALTER TABLE `replies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reportform`
--
ALTER TABLE `reportform`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `schedule`
--
ALTER TABLE `schedule`
  ADD PRIMARY KEY (`scheduleid`),
  ADD KEY `docid` (`docid`);

--
-- Indexes for table `semester`
--
ALTER TABLE `semester`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `specialties`
--
ALTER TABLE `specialties`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `topics`
--
ALTER TABLE `topics`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `webuser`
--
ALTER TABLE `webuser`
  ADD PRIMARY KEY (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointment`
--
ALTER TABLE `appointment`
  MODIFY `appoid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `forum_views`
--
ALTER TABLE `forum_views`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `patient`
--
ALTER TABLE `patient`
  MODIFY `pid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `doctor`
--
ALTER TABLE `doctor`
  MODIFY `docid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `replies`
--
ALTER TABLE `replies`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `reportform`
--
ALTER TABLE `reportform`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `schedule`
--
ALTER TABLE `schedule`
  MODIFY `scheduleid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `semester`
--
ALTER TABLE `semester`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `topics`
--
ALTER TABLE `topics`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
