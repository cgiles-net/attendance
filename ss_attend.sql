-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Aug 01, 2016 at 08:17 PM
-- Server version: 10.1.13-MariaDB
-- PHP Version: 5.6.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ss_attend`
--

-- --------------------------------------------------------

--
-- Table structure for table `ss_attend`
--

CREATE TABLE `ss_attend` (
  `attend_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `attend_date` date NOT NULL,
  `inroute` tinyint(1) NOT NULL,
  `inclass` tinyint(1) NOT NULL,
  `memory_verse` tinyint(1) DEFAULT '0',
  `offering` decimal(6,2) DEFAULT '0.00',
  `visitor` tinyint(1) DEFAULT '0',
  `bonus_1` tinyint(1) DEFAULT '0',
  `bonus_2` tinyint(1) DEFAULT '0',
  `verified` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ss_attend`
--

INSERT INTO `ss_attend` (`attend_id`, `student_id`, `attend_date`, `inroute`, `inclass`, `memory_verse`, `offering`, `visitor`, `bonus_1`, `bonus_2`, `verified`) VALUES
(1, 5, '2016-07-31', 2, 1, 0, '0.00', 0, 0, 0, 0),
(2, 4, '2016-07-31', 2, 1, 0, '0.00', 0, 0, 0, 0),
(3, 16, '2016-07-31', 0, 0, 0, '0.00', 0, 0, 0, 0),
(4, 6, '2016-07-31', 2, 1, 0, '0.00', 0, 0, 0, 0),
(5, 7, '2016-07-31', 2, 0, 0, '0.00', 0, 0, 0, 0),
(6, 11, '2016-07-31', 2, 1, 0, '0.00', 0, 0, 0, 0),
(7, 10, '2016-07-31', 2, 1, 0, '0.00', 0, 0, 0, 0),
(8, 8, '2016-07-31', 2, 1, 0, '0.00', 0, 0, 0, 0),
(9, 9, '2016-07-31', 2, 1, 0, '0.00', 0, 0, 0, 0),
(10, 2, '2016-07-31', 2, 1, 1, '0.00', 0, 0, 0, 0),
(11, 3, '2016-07-31', 0, 0, 0, '0.00', 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `ss_family`
--

CREATE TABLE `ss_family` (
  `guardian_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `relation` varchar(255) NOT NULL,
  `er_contact` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ss_guardians`
--

CREATE TABLE `ss_guardians` (
  `guardian_id` int(11) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `home_phone` varchar(11) DEFAULT NULL,
  `cell_phone` varchar(11) DEFAULT NULL,
  `work_phone` varchar(11) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(2) NOT NULL,
  `zip` varchar(10) NOT NULL,
  `notes` blob
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ss_guardian_notes`
--

CREATE TABLE `ss_guardian_notes` (
  `guardian_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `note` blob NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `visible` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ss_rooms`
--

CREATE TABLE `ss_rooms` (
  `room_id` int(11) NOT NULL,
  `room_name` varchar(255) NOT NULL,
  `room_captain` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ss_rooms`
--

INSERT INTO `ss_rooms` (`room_id`, `room_name`, `room_captain`) VALUES
(1, 'Room 3 - 2nd Grade Boys', 1),
(2, 'Random 1', 4),
(3, 'Random 2', 1),
(4, 'eh', 1),
(5, 'classroom 5', 1),
(6, 'Room 2, 2nd Grade Girls ', 1),
(7, 'Room 5, 3rd Grade Boys', 2);

-- --------------------------------------------------------

--
-- Table structure for table `ss_routes`
--

CREATE TABLE `ss_routes` (
  `route_id` int(11) NOT NULL,
  `route_name` varchar(255) NOT NULL,
  `route_captain` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ss_staff`
--

CREATE TABLE `ss_staff` (
  `teacher_id` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `phone` varchar(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `room_id` int(10) DEFAULT NULL,
  `route_id` int(10) DEFAULT NULL,
  `approved` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ss_staff`
--

INSERT INTO `ss_staff` (`teacher_id`, `username`, `password`, `last_name`, `first_name`, `phone`, `email`, `room_id`, `route_id`, `approved`) VALUES
(1, 'nmaas', 'c1a23116461d5856f98ee072ea319bc9', 'Maas', 'Nicole', '', '', 1, NULL, 1),
(2, 'erowel', '943e9d0354f42030e2a74743ac447fce', 'Rowel', 'Eli', '', '', 1, NULL, 0),
(3, 'arandol', '943e9d0354f42030e2a74743ac447fce', 'Randol', 'Andrea', '', '', 1, NULL, 0),
(4, 'jwilliams', '943e9d0354f42030e2a74743ac447fce', 'Williams', 'Jeff', '', '', 2, NULL, 0),
(5, 'pastor', '5e027396789a18c37aeda616e3d7991b', 'Randol', 'Gary', '', 'seniorpastor@apostolicsanctuary.org', NULL, NULL, 1),
(6, 'svanduss', 'e9197133b96a26a76dd18ca04605f9c2', 'VanDusseldorp', 'Stephen', '3093735002', 'svanduss@gmail.com', NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `ss_students`
--

CREATE TABLE `ss_students` (
  `student_id` int(11) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle` varchar(1) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `phone` varchar(11) DEFAULT NULL,
  `cell` varchar(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(2) DEFAULT NULL,
  `zip` varchar(10) DEFAULT NULL,
  `allergies` blob,
  `notes` blob,
  `image` varchar(255) NOT NULL DEFAULT 'default_profile.jpg',
  `room_id` int(10) NOT NULL DEFAULT '0',
  `route_id` int(10) NOT NULL DEFAULT '0',
  `card_id` int(11) DEFAULT NULL,
  `current_points` int(11) NOT NULL DEFAULT '0',
  `lifetime_points` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ss_students`
--

INSERT INTO `ss_students` (`student_id`, `last_name`, `first_name`, `middle`, `birthdate`, `phone`, `cell`, `email`, `address`, `city`, `state`, `zip`, `allergies`, `notes`, `image`, `room_id`, `route_id`, `card_id`, `current_points`, `lifetime_points`) VALUES
(1, 'VanDusseldorp', 'Layla', 'A', '2011-09-20', '', '', '', '', '', '', '', NULL, NULL, 'default_profile.jpg', 0, 0, NULL, 0, 0),
(2, 'Hines', 'Maliyah', '', '2008-01-25', '', '', '', '', '', '', '', NULL, NULL, 'default_profile.jpg', 0, 0, NULL, 0, 0),
(3, 'Bawihnem', 'Esther ', '', '2010-01-04', '', '', '', '', '', '', '', NULL, NULL, 'default_profile.jpg', 6, 0, NULL, 0, 0),
(4, 'Par', 'Jenevy', '', '2010-02-10', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'default_profile.jpg', 0, 0, NULL, 0, 0),
(5, 'Hin', 'Michael', '', '2008-05-18', '', '', '', '', '', '', '', NULL, NULL, 'default_profile.jpg', 1, 0, NULL, 0, 0),
(6, 'Swithapar', 'Julie', '', '2010-05-06', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'default_profile.jpg', 0, 0, NULL, 0, 0),
(7, 'Chin Tial', 'Ngun ', '', '2007-11-07', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'default_profile.jpg', 0, 0, NULL, 0, 0),
(8, 'Houessou', 'Sharone', 'c', '2008-02-08', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'default_profile.jpg', 0, 0, NULL, 0, 0),
(9, 'Houessou', 'Jorhinda', '', '2004-04-26', '', '', '', '', '', '', '', NULL, NULL, 'default_profile.jpg', 0, 0, NULL, 0, 0),
(10, 'Houessou', 'Sweety', 'f', '2006-11-20', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'default_profile.jpg', 0, 0, NULL, 0, 0),
(11, 'Houessou', 'Miranda', 'p', '2010-08-11', '', '', '', '', '', '', '', NULL, NULL, 'default_profile.jpg', 3, 0, NULL, 0, 0),
(16, 'Rubinate ', 'Carsynn', '', '0000-00-00', NULL, '', '', '', '', '', '', NULL, NULL, 'default_profile.jpg', 0, 0, NULL, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `ss_student_notes`
--

CREATE TABLE `ss_student_notes` (
  `note_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `note` mediumtext NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `visible` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ss_student_notes`
--

INSERT INTO `ss_student_notes` (`note_id`, `student_id`, `teacher_id`, `note`, `created`, `visible`) VALUES
(1, 5, 6, ':) <B>test</B>', '2016-07-31 21:17:26', 1),
(2, 5, 6, '<div style="color: rgb(255, 255, 255);font-size: 30px;background-color: rgb(85, 147, 194);text-shadow: rgb(204, 204, 204) 0px 1px 0px, rgb(201, 201, 201) 0px 2px 0px, rgb(187, 187, 187) 0px 3px 0px, rgb(185, 185, 185) 0px 4px 0px, rgb(170, 170, 170) 0px 5px 0px, rgba(0, 0, 0, 0.1) 0px 6px 1px, rgba(0, 0, 0, 0.1) 0px 0px 5px, rgba(0, 0, 0, 0.3) 0px 1px 3px, rgba(0, 0, 0, 0.15) 0px 3px 5px, rgba(0, 0, 0, 0.2) 0px 5px 10px, rgba(0, 0, 0, 0.2) 0px 10px 10px, rgba(0, 0, 0, 0.1) 0px 20px 20px;">Abusing the notes! I''m not sure if this should be allowed.</div>', '2016-07-31 23:52:27', 1),
(3, 5, 6, 'Well then.... ', '2016-07-31 21:22:18', 1),
(4, 5, 1, 'Secret communication: hi', '2016-07-31 22:45:23', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ss_attend`
--
ALTER TABLE `ss_attend`
  ADD PRIMARY KEY (`attend_id`);

--
-- Indexes for table `ss_guardians`
--
ALTER TABLE `ss_guardians`
  ADD PRIMARY KEY (`guardian_id`);

--
-- Indexes for table `ss_rooms`
--
ALTER TABLE `ss_rooms`
  ADD PRIMARY KEY (`room_id`);

--
-- Indexes for table `ss_routes`
--
ALTER TABLE `ss_routes`
  ADD PRIMARY KEY (`route_id`);

--
-- Indexes for table `ss_staff`
--
ALTER TABLE `ss_staff`
  ADD PRIMARY KEY (`teacher_id`);

--
-- Indexes for table `ss_students`
--
ALTER TABLE `ss_students`
  ADD PRIMARY KEY (`student_id`);

--
-- Indexes for table `ss_student_notes`
--
ALTER TABLE `ss_student_notes`
  ADD PRIMARY KEY (`note_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ss_attend`
--
ALTER TABLE `ss_attend`
  MODIFY `attend_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `ss_guardians`
--
ALTER TABLE `ss_guardians`
  MODIFY `guardian_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ss_rooms`
--
ALTER TABLE `ss_rooms`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `ss_routes`
--
ALTER TABLE `ss_routes`
  MODIFY `route_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ss_staff`
--
ALTER TABLE `ss_staff`
  MODIFY `teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `ss_students`
--
ALTER TABLE `ss_students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT for table `ss_student_notes`
--
ALTER TABLE `ss_student_notes`
  MODIFY `note_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
