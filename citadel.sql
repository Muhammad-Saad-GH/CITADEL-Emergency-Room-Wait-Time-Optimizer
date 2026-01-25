-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 25, 2026 at 09:22 AM
-- Server version: 8.4.7
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `citadel`
--

-- --------------------------------------------------------

--
-- Stand-in structure for view `above_avg_severity_patients`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `above_avg_severity_patients`;
CREATE TABLE IF NOT EXISTS `above_avg_severity_patients` (
`Hos_ID` int
,`Patient_ID` int
,`Severity` int
,`Username` varchar(50)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `active_patient_checkins`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `active_patient_checkins`;
CREATE TABLE IF NOT EXISTS `active_patient_checkins` (
`Hospital_Name` varchar(150)
,`Patient_ID` int
,`Patient_Username` varchar(50)
,`Severity` int
,`Status` varchar(50)
,`Wait_Time` int
);

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `Admin_ID` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Username` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Password_Hash` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Approved` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`Admin_ID`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`Admin_ID`, `Name`, `Username`, `Password_Hash`, `Approved`) VALUES
(1, 'Bruce Wayne', 'b_wayne', '$2y$10$JyRlRlkgTCZHx9gfPd2FquwGajtkRVldzQWwTIiAolBTdngUCSjqy', 1),
(2, 'Tom Holland', 'tholland', '$2y$10$JyRlRlkgTCZHx9gfPd2FquwGajtkRVldzQWwTIiAolBTdngUCSjqy', 0),
(3, 'Andrew Garfield', 'garfielda', '$2y$10$JyRlRlkgTCZHx9gfPd2FquwGajtkRVldzQWwTIiAolBTdngUCSjqy', 1),
(4, 'Chris Hemsworth', 'c_worth', '$2y$10$JyRlRlkgTCZHx9gfPd2FquwGajtkRVldzQWwTIiAolBTdngUCSjqy', 0),
(5, 'Sean Combs', 'pdid_', '$2y$10$JyRlRlkgTCZHx9gfPd2FquwGajtkRVldzQWwTIiAolBTdngUCSjqy', 1),
(6, 'Chris Evans', 'chrisevans', '$2y$10$JyRlRlkgTCZHx9gfPd2FquwGajtkRVldzQWwTIiAolBTdngUCSjqy', 0);

-- --------------------------------------------------------

--
-- Table structure for table `checkin`
--

DROP TABLE IF EXISTS `checkin`;
CREATE TABLE IF NOT EXISTS `checkin` (
  `Checkin_ID` int NOT NULL AUTO_INCREMENT,
  `Severity` int DEFAULT NULL,
  `Wait_Time` int DEFAULT NULL,
  `Status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Hos_ID` int DEFAULT NULL,
  `Approved` tinyint(1) DEFAULT NULL,
  `AI_reasoning` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`Checkin_ID`),
  KEY `checkin_ibfk_1` (`Hos_ID`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `checkin`
--

INSERT INTO `checkin` (`Checkin_ID`, `Severity`, `Wait_Time`, `Status`, `Notes`, `Hos_ID`, `Approved`, `AI_reasoning`) VALUES
(2, 2, 20, 'Waiting', 'Chest pain, being assessed', 1, 0, 'Chest pain requires immediate, comprehensive evaluation due to the high potential for life-threatening cardiac or pulmonary emergencies.'),
(3, 3, 60, 'Approved', 'Sprained ankle', 1, 1, 'A sprained ankle typically presents with localized pain and swelling but does not involve life-threatening or limb-threatening complications requiring immediate, resource-intensive intervention.'),
(4, 5, 10, 'Approved', 'Severe trauma case handled', 1, 0, 'Severe trauma cases require immediate, life-saving interventions, placing them at the highest acuity level.'),
(5, 4, 90, 'Approved', 'Mild flu symptoms', 1, 1, 'Mild flu symptoms generally represent a low acuity complaint that can be addressed in a non-emergent setting, such as an Urgent Care or Primary Care provider.'),
(6, 5, 30, '', 'Left before triage', 1, 0, 'Since the patient left before triage could be completed, they are assigned the lowest acuity score (ESI 5) as no medical acuity could be assessed.'),
(7, 2, 25, 'Approved', 'Shortness of breath', 1, 1, 'Shortness of breath can indicate life-threatening conditions like myocardial infarction or pulmonary embolism, necessitating rapid evaluation.'),
(8, 3, 35, 'Waiting', 'Back pain', 2, 0, 'Back pain without neurological deficits, severe hemodynamic instability, or obvious signs of life-threatening conditions typically warrants an ESI level of 3, requiring multiple resources or observation.'),
(9, 1, 12, 'Approved', 'Stroke code, active management', 2, 1, 'A stroke code mandates immediate activation of the stroke team and rapid intervention due to the high risk of permanent neurological damage or death.'),
(10, 4, 50, 'Waiting', 'Nausea and vomiting', 2, 0, 'Nausea and vomiting, without associated severe symptoms like chest pain or shortness of breath, typically fall into a lower acuity category requiring a moderate level of resources for evaluation.'),
(11, 3, 40, 'Approved', 'Laceration requiring sutures', 3, 1, 'A simple laceration requiring sutures is generally considered an urgent but not immediately life-threatening condition, placing it in the moderate acuity ESI level 3.'),
(12, 3, 55, 'Waiting', 'Fractured wrist', 4, 0, 'A fractured wrist typically presents with significant pain and functional impairment but is generally not immediately life- or limb-threatening, placing it in the moderate acuity category.'),
(13, 4, 65, 'Approved', 'Ear infection', 5, 1, 'An uncomplicated ear infection (otitis media or externa) is typically not life-threatening and can be managed as a lower acuity complaint unless there are signs of severe systemic infection or complications.'),
(14, 2, 15, 'Waiting', 'Asthma attack', 6, 0, 'An asthma attack is potentially life-threatening and requires prompt attention to ensure adequate oxygenation and ventilation.'),
(24, 1, NULL, 'Waiting', 'My appendix has burst', 1, 0, 'A ruptured appendix constitutes a surgical emergency with high risk for sepsis and requires immediate, definitive treatment.'),
(26, 5, 30, 'Approved', '[Patient Context: Age: 20 | Sex: Male | Pain Level: 5/10]\nMy head really hurts when I get up. When I lie down it\'s okay but I can\'t stand for more than 1min without a migraine occuring', 1, 0, 'The patient is a 20-year-old male with moderate pain (5/10), which does not meet criteria for ESI 1 or 2 based on the provided rules, suggesting an ESI 4 or 5 based on expected resource needs.'),
(27, 3, NULL, 'Waiting', '[Patient Context: Age: 15 | Sex: Male | Pain Level: 10/10]\nI fell off my bike with kneepads and scraped by elbow a little bit', 1, 0, 'AI Service Unavailable (Manual Review Required)'),
(28, 3, NULL, 'Waiting', '[Patient Context: Age: 15 | Sex: Male | Pain Level: 10/10]\nslight bruise on my ring finger', 1, 0, 'AI Service Unavailable (Manual Review Required)'),
(29, 3, NULL, 'Waiting', '[Patient Context: Age: 10 | Sex: Male | Pain Level: 10/10]\nI slightly bruised my knee', 1, 0, 'AI Error: 400 INVALID_ARGUMENT. {\'error\': {\'code\': 400, \'message\': \'API key not valid. Please pass a valid API key.\', \'status\': \'INVALID_ARGUMENT\', \'details\': [{\'@type\': \'type.googleapis.com/google.rpc.ErrorInfo\', \'reason\': \'API_KEY_INVALID\', \'domain\': \'googleapis.com\', \'metadata\': {\'service\': \'generativelanguage.googleapis.com\'}}, {\'@type\': \'type.googleapis.com/google.rpc.LocalizedMessage\', \'locale\': \'en-US\', \'message\': \'API key not valid. Please pass a valid API key.\'}]}}. Defaulted to moderate urgency.'),
(30, 3, NULL, 'Waiting', '[Patient Context: Age: 15 | Sex: Male | Pain Level: 10/10]\nMy tummy hurts', 1, 0, 'AI Service Unavailable (Manual Review Required)'),
(31, 3, 240, 'Waiting', '[Patient Context: Age: 15 | Sex: Male | Pain Level: 10/10]\nMy bum is itchy', 1, 0, 'A 15-year-old male presenting with 10/10 pain requires prompt attention, but without a specific chief complaint indicating immediate life threat or multi-system resource need, this typically defaults to ESI 3 unless the pain is associated with suspected serious pathology.');

-- --------------------------------------------------------

--
-- Table structure for table `checkin_handler`
--

DROP TABLE IF EXISTS `checkin_handler`;
CREATE TABLE IF NOT EXISTS `checkin_handler` (
  `Checkin_ID` int NOT NULL,
  `Staff_ID` int NOT NULL,
  PRIMARY KEY (`Checkin_ID`,`Staff_ID`),
  KEY `checkin_handler_ibfk_2` (`Staff_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `checkin_handler`
--

INSERT INTO `checkin_handler` (`Checkin_ID`, `Staff_ID`) VALUES
(2, 3),
(3, 1),
(3, 2),
(4, 4),
(5, 1),
(6, 5),
(8, 6),
(9, 7),
(11, 8),
(12, 9);

-- --------------------------------------------------------

--
-- Stand-in structure for view `checkin_staff_assignment`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `checkin_staff_assignment`;
CREATE TABLE IF NOT EXISTS `checkin_staff_assignment` (
`Checkin_ID` int
,`Staff_ID` int
,`Staff_Name` varchar(100)
,`Status` varchar(50)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `completed_cases_per_hospital`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `completed_cases_per_hospital`;
CREATE TABLE IF NOT EXISTS `completed_cases_per_hospital` (
`Completed_Cases` bigint
,`Hospital_ID` int
,`Hospital_Name` varchar(150)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `high_avg_wait_hospitals`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `high_avg_wait_hospitals`;
CREATE TABLE IF NOT EXISTS `high_avg_wait_hospitals` (
`Avg_Wait_Time` decimal(14,4)
,`Hospital_ID` int
,`Hospital_Name` varchar(150)
);

-- --------------------------------------------------------

--
-- Table structure for table `hospital`
--

DROP TABLE IF EXISTS `hospital`;
CREATE TABLE IF NOT EXISTS `hospital` (
  `Hospital_ID` int NOT NULL AUTO_INCREMENT,
  `Phone_Num` char(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Rating` int DEFAULT NULL,
  `Name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`Hospital_ID`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hospital`
--

INSERT INTO `hospital` (`Hospital_ID`, `Phone_Num`, `Rating`, `Name`, `Location`) VALUES
(1, '9055768711', 5, 'Lakeridge Health', 'Oshawa, ON'),
(2, '4164382911', 3, 'Scarborough General Hospital ', 'Scarborough, ON'),
(3, '4165964200', 4, 'Mount Sinai Hospital', 'Toronto, ON'),
(4, '4163403111', 4, 'Toronto General Hospital', 'Toronto, ON'),
(5, '4167566000', 3, 'North York General Hospital', 'Toronto, ON'),
(6, '4163604000', 4, 'St. Michaels Hospital', 'Toronto, ON');

-- --------------------------------------------------------

--
-- Stand-in structure for view `hospital_staff_count`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `hospital_staff_count`;
CREATE TABLE IF NOT EXISTS `hospital_staff_count` (
`Hospital_ID` int
,`Hospital_Name` varchar(150)
,`Staff_Count` bigint
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `hospital_waitlist`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `hospital_waitlist`;
CREATE TABLE IF NOT EXISTS `hospital_waitlist` (
`Hospital_Name` varchar(150)
,`Patient` varchar(50)
,`Severity` int
,`Wait_Time` int
);

-- --------------------------------------------------------

--
-- Table structure for table `hos_manager`
--

DROP TABLE IF EXISTS `hos_manager`;
CREATE TABLE IF NOT EXISTS `hos_manager` (
  `Hos_ID` int NOT NULL,
  `Admin_ID` int NOT NULL,
  PRIMARY KEY (`Hos_ID`,`Admin_ID`),
  KEY `hos_manager_ibfk_1` (`Admin_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hos_manager`
--

INSERT INTO `hos_manager` (`Hos_ID`, `Admin_ID`) VALUES
(1, 1),
(1, 2),
(1, 3),
(2, 2),
(2, 4),
(3, 3),
(4, 4),
(5, 5),
(6, 6);

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

DROP TABLE IF EXISTS `patient`;
CREATE TABLE IF NOT EXISTS `patient` (
  `Patient_ID` int NOT NULL AUTO_INCREMENT,
  `HC_Num` char(12) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Username` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Email` varchar(254) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Password_Hash` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Check_ID` int DEFAULT NULL,
  PRIMARY KEY (`Patient_ID`),
  KEY `patient_ibfk_1` (`Check_ID`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `patient`
--

INSERT INTO `patient` (`Patient_ID`, `HC_Num`, `Username`, `Email`, `Password_Hash`, `Check_ID`) VALUES
(1, 'ON1234567890', 'john_smith', 'john.smith@gmail.com', '$2y$10$JyRlRlkgTCZHx9gfPd2FquwGajtkRVldzQWwTIiAolBTdngUCSjqy', 31),
(2, 'ON9876543210', 'susan_lee', 'susan.lee@gmail.com', '$2y$10$JyRlRlkgTCZHx9gfPd2FquwGajtkRVldzQWwTIiAolBTdngUCSjqy', 2),
(3, 'ON6543219870', 'mike_doe', 'mike.doe@yahoo.com', '$2y$10$JyRlRlkgTCZHx9gfPd2FquwGajtkRVldzQWwTIiAolBTdngUCSjqy', 3),
(4, 'ON1122334455', 'rachel_b', 'rachel.b@outlook.com', '$2y$10$JyRlRlkgTCZHx9gfPd2FquwGajtkRVldzQWwTIiAolBTdngUCSjqy', 4),
(5, 'ON5566778899', 'omar_a', 'omar.a@gmail.com', '$2y$10$JyRlRlkgTCZHx9gfPd2FquwGajtkRVldzQWwTIiAolBTdngUCSjqy', 5),
(6, 'ON0099887766', 'tina_w', 'tina.w@gmail.com', '$2y$10$JyRlRlkgTCZHx9gfPd2FquwGajtkRVldzQWwTIiAolBTdngUCSjqy', 6),
(7, 'ON4433221100', 'kevin_p', 'kevin.p@yahoo.com', '$2y$10$JyRlRlkgTCZHx9gfPd2FquwGajtkRVldzQWwTIiAolBTdngUCSjqy', 7),
(8, 'ON2211443366', 'linda_g', 'linda.g@yahoo.com', '$2y$10$JyRlRlkgTCZHx9gfPd2FquwGajtkRVldzQWwTIiAolBTdngUCSjqy', 8),
(9, 'ON3322114455', 'aaron_k', 'aaron.k@outlook.com', '$2y$10$JyRlRlkgTCZHx9gfPd2FquwGajtkRVldzQWwTIiAolBTdngUCSjqy', 9),
(10, 'ON7788990011', 'bianca_r', 'bianca.r@yahoo.com', '$2y$10$JyRlRlkgTCZHx9gfPd2FquwGajtkRVldzQWwTIiAolBTdngUCSjqy', 10),
(11, 'ON1100223344', 'henry_t', 'henry.t@gmail.com', '$2y$10$JyRlRlkgTCZHx9gfPd2FquwGajtkRVldzQWwTIiAolBTdngUCSjqy', 11),
(12, 'ON9900112233', 'sophie_m', 'sophie.m@gmail.com', '$2y$10$JyRlRlkgTCZHx9gfPd2FquwGajtkRVldzQWwTIiAolBTdngUCSjqy', 12),
(13, 'ON2200998877', 'william_g', 'william.g@gmail.com', '$2y$10$JyRlRlkgTCZHx9gfPd2FquwGajtkRVldzQWwTIiAolBTdngUCSjqy', 13),
(14, 'ON7766554433', 'nora_z', 'nora.z@gmail.com', '$2y$10$JyRlRlkgTCZHx9gfPd2FquwGajtkRVldzQWwTIiAolBTdngUCSjqy', 14);

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

DROP TABLE IF EXISTS `staff`;
CREATE TABLE IF NOT EXISTS `staff` (
  `Staff_ID` int NOT NULL AUTO_INCREMENT,
  `Access_Level` int DEFAULT NULL,
  `Name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Role` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Username` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Password_Hash` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Hos_ID` int DEFAULT NULL,
  `Approved` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`Staff_ID`),
  KEY `staff_ibfk_1` (`Hos_ID`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`Staff_ID`, `Access_Level`, `Name`, `Role`, `Username`, `Password_Hash`, `Hos_ID`, `Approved`) VALUES
(1, 1, 'Dr. Sally Alsafadi', 'Physician', 'bestDr', '$2y$10$JyRlRlkgTCZHx9gfPd2FquwGajtkRVldzQWwTIiAolBTdngUCSjqy', 1, 1),
(2, 1, 'Nurse Maria Lopez', 'Nurse', 'mlopez', '$2y$10$JyRlRlkgTCZHx9gfPd2FquwGajtkRVldzQWwTIiAolBTdngUCSjqy', 1, 0),
(3, 3, 'Dr. Abdul Solangi', 'Surgeon', 'solangiA', '$2y$10$JyRlRlkgTCZHx9gfPd2FquwGajtkRVldzQWwTIiAolBTdngUCSjqy', 1, 1),
(4, 1, 'Nurse Daniel Wong', 'Nurse', 'dwong', '$2y$10$JyRlRlkgTCZHx9gfPd2FquwGajtkRVldzQWwTIiAolBTdngUCSjqy', 1, 0),
(5, 2, 'Dr. Hannah Lee', 'Physician', 'hlee', '$2y$10$JyRlRlkgTCZHx9gfPd2FquwGajtkRVldzQWwTIiAolBTdngUCSjqy', 1, 1),
(6, 1, 'Nurse Sarah Kim', 'Nurse', 'skim', '$2y$10$JyRlRlkgTCZHx9gfPd2FquwGajtkRVldzQWwTIiAolBTdngUCSjqy', 2, 0),
(7, 2, 'Dr. Peter Zhou', 'Physician', 'pzhou', '$2y$10$JyRlRlkgTCZHx9gfPd2FquwGajtkRVldzQWwTIiAolBTdngUCSjqy', 2, 1),
(8, 1, 'Nurse Nabil Ahmed', 'Nurse', 'nabila', '$2y$10$JyRlRlkgTCZHx9gfPd2FquwGajtkRVldzQWwTIiAolBTdngUCSjqy', 3, 0),
(9, 2, 'Dr. Maya Singh', 'Physician', 'msingh', '$2y$10$JyRlRlkgTCZHx9gfPd2FquwGajtkRVldzQWwTIiAolBTdngUCSjqy', 4, 1),
(10, 1, 'Tom Evans', 'Clerk', 'tevans', '$2y$10$JyRlRlkgTCZHx9gfPd2FquwGajtkRVldzQWwTIiAolBTdngUCSjqy', 5, 0);

-- --------------------------------------------------------

--
-- Stand-in structure for view `staff_case_count`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `staff_case_count`;
CREATE TABLE IF NOT EXISTS `staff_case_count` (
`Cases_Handled` bigint
,`Staff_ID` int
,`Staff_Name` varchar(100)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `unassigned_patient_checkins`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `unassigned_patient_checkins`;
CREATE TABLE IF NOT EXISTS `unassigned_patient_checkins` (
`Checkin_ID` int
,`Hospital_Name` varchar(150)
,`Patient_ID` int
,`Username` varchar(50)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `waiting_or_cancelled_patients`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `waiting_or_cancelled_patients`;
CREATE TABLE IF NOT EXISTS `waiting_or_cancelled_patients` (
`Patient_ID` int
,`Status` varchar(50)
,`Username` varchar(50)
);

-- --------------------------------------------------------

--
-- Structure for view `above_avg_severity_patients`
--
DROP TABLE IF EXISTS `above_avg_severity_patients`;

DROP VIEW IF EXISTS `above_avg_severity_patients`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `above_avg_severity_patients`  AS SELECT `p`.`Patient_ID` AS `Patient_ID`, `p`.`Username` AS `Username`, `c`.`Severity` AS `Severity`, `c`.`Hos_ID` AS `Hos_ID` FROM (`patient` `p` join `checkin` `c` on((`p`.`Check_ID` = `c`.`Checkin_ID`))) WHERE (`c`.`Severity` > (select avg(`c2`.`Severity`) from `checkin` `c2` where (`c2`.`Hos_ID` = `c`.`Hos_ID`))) ;

-- --------------------------------------------------------

--
-- Structure for view `active_patient_checkins`
--
DROP TABLE IF EXISTS `active_patient_checkins`;

DROP VIEW IF EXISTS `active_patient_checkins`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `active_patient_checkins`  AS SELECT `p`.`Patient_ID` AS `Patient_ID`, `p`.`Username` AS `Patient_Username`, `h`.`Name` AS `Hospital_Name`, `c`.`Severity` AS `Severity`, `c`.`Wait_Time` AS `Wait_Time`, `c`.`Status` AS `Status` FROM ((`patient` `p` join `checkin` `c` on((`p`.`Check_ID` = `c`.`Checkin_ID`))) join `hospital` `h` on((`c`.`Hos_ID` = `h`.`Hospital_ID`))) WHERE (`c`.`Status` = 'Waiting') ;

-- --------------------------------------------------------

--
-- Structure for view `checkin_staff_assignment`
--
DROP TABLE IF EXISTS `checkin_staff_assignment`;

DROP VIEW IF EXISTS `checkin_staff_assignment`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `checkin_staff_assignment`  AS SELECT `c`.`Checkin_ID` AS `Checkin_ID`, `c`.`Status` AS `Status`, `s`.`Staff_ID` AS `Staff_ID`, `s`.`Name` AS `Staff_Name` FROM ((`checkin` `c` left join `checkin_handler` `ch` on((`c`.`Checkin_ID` = `ch`.`Checkin_ID`))) left join `staff` `s` on((`ch`.`Staff_ID` = `s`.`Staff_ID`)))union select `c`.`Checkin_ID` AS `Checkin_ID`,`c`.`Status` AS `Status`,`s`.`Staff_ID` AS `Staff_ID`,`s`.`Name` AS `Staff_Name` from (`checkin` `c` left join (`checkin_handler` `ch` left join `staff` `s` on((`s`.`Staff_ID` = `ch`.`Staff_ID`))) on((`ch`.`Checkin_ID` = `c`.`Checkin_ID`)))  ;

-- --------------------------------------------------------

--
-- Structure for view `completed_cases_per_hospital`
--
DROP TABLE IF EXISTS `completed_cases_per_hospital`;

DROP VIEW IF EXISTS `completed_cases_per_hospital`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `completed_cases_per_hospital`  AS SELECT `h`.`Hospital_ID` AS `Hospital_ID`, `h`.`Name` AS `Hospital_Name`, count(`c`.`Checkin_ID`) AS `Completed_Cases` FROM (`hospital` `h` join `checkin` `c` on((`h`.`Hospital_ID` = `c`.`Hos_ID`))) WHERE (`c`.`Status` = 'Completed') GROUP BY `h`.`Hospital_ID`, `h`.`Name` ;

-- --------------------------------------------------------

--
-- Structure for view `high_avg_wait_hospitals`
--
DROP TABLE IF EXISTS `high_avg_wait_hospitals`;

DROP VIEW IF EXISTS `high_avg_wait_hospitals`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `high_avg_wait_hospitals`  AS SELECT `h`.`Hospital_ID` AS `Hospital_ID`, `h`.`Name` AS `Hospital_Name`, avg(`c`.`Wait_Time`) AS `Avg_Wait_Time` FROM (`hospital` `h` join `checkin` `c` on((`h`.`Hospital_ID` = `c`.`Hos_ID`))) GROUP BY `h`.`Hospital_ID`, `h`.`Name` HAVING avg(`c`.`Wait_Time`) > any (select avg(`c2`.`Wait_Time`) from `checkin` `c2` group by `c2`.`Hos_ID`) ;

-- --------------------------------------------------------

--
-- Structure for view `hospital_staff_count`
--
DROP TABLE IF EXISTS `hospital_staff_count`;

DROP VIEW IF EXISTS `hospital_staff_count`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `hospital_staff_count`  AS SELECT `h`.`Hospital_ID` AS `Hospital_ID`, `h`.`Name` AS `Hospital_Name`, count(`s`.`Staff_ID`) AS `Staff_Count` FROM (`hospital` `h` left join `staff` `s` on((`h`.`Hospital_ID` = `s`.`Hos_ID`))) GROUP BY `h`.`Hospital_ID`, `h`.`Name` ;

-- --------------------------------------------------------

--
-- Structure for view `hospital_waitlist`
--
DROP TABLE IF EXISTS `hospital_waitlist`;

DROP VIEW IF EXISTS `hospital_waitlist`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `hospital_waitlist`  AS SELECT `h`.`Name` AS `Hospital_Name`, `p`.`Username` AS `Patient`, `c`.`Severity` AS `Severity`, `c`.`Wait_Time` AS `Wait_Time` FROM ((`checkin` `c` join `patient` `p` on((`c`.`Checkin_ID` = `p`.`Check_ID`))) join `hospital` `h` on((`c`.`Hos_ID` = `h`.`Hospital_ID`))) WHERE (`c`.`Status` = 'Waiting') ORDER BY `c`.`Severity` DESC, `c`.`Wait_Time` DESC ;

-- --------------------------------------------------------

--
-- Structure for view `staff_case_count`
--
DROP TABLE IF EXISTS `staff_case_count`;

DROP VIEW IF EXISTS `staff_case_count`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `staff_case_count`  AS SELECT `s`.`Staff_ID` AS `Staff_ID`, `s`.`Name` AS `Staff_Name`, count(`ch`.`Checkin_ID`) AS `Cases_Handled` FROM (`staff` `s` left join `checkin_handler` `ch` on((`s`.`Staff_ID` = `ch`.`Staff_ID`))) GROUP BY `s`.`Staff_ID`, `s`.`Name` ;

-- --------------------------------------------------------

--
-- Structure for view `unassigned_patient_checkins`
--
DROP TABLE IF EXISTS `unassigned_patient_checkins`;

DROP VIEW IF EXISTS `unassigned_patient_checkins`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `unassigned_patient_checkins`  AS SELECT `p`.`Patient_ID` AS `Patient_ID`, `p`.`Username` AS `Username`, `c`.`Checkin_ID` AS `Checkin_ID`, `h`.`Name` AS `Hospital_Name` FROM (((`patient` `p` join `checkin` `c` on((`p`.`Check_ID` = `c`.`Checkin_ID`))) join `hospital` `h` on((`c`.`Hos_ID` = `h`.`Hospital_ID`))) left join `checkin_handler` `ch` on((`c`.`Checkin_ID` = `ch`.`Checkin_ID`))) WHERE (`ch`.`Staff_ID` is null) ;

-- --------------------------------------------------------

--
-- Structure for view `waiting_or_cancelled_patients`
--
DROP TABLE IF EXISTS `waiting_or_cancelled_patients`;

DROP VIEW IF EXISTS `waiting_or_cancelled_patients`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `waiting_or_cancelled_patients`  AS SELECT `p`.`Patient_ID` AS `Patient_ID`, `p`.`Username` AS `Username`, `c`.`Status` AS `Status` FROM (`patient` `p` join `checkin` `c` on((`p`.`Check_ID` = `c`.`Checkin_ID`))) WHERE (`c`.`Status` = 'Waiting')union select `p`.`Patient_ID` AS `Patient_ID`,`p`.`Username` AS `Username`,`c`.`Status` AS `Status` from (`patient` `p` join `checkin` `c` on((`p`.`Check_ID` = `c`.`Checkin_ID`))) where (`c`.`Status` = 'Cancelled')  ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
