-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 09, 2020 at 07:31 PM
-- Server version: 10.4.8-MariaDB
-- PHP Version: 7.3.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `crm`
--

-- --------------------------------------------------------

--
-- Table structure for table `calendar_notification`
--

CREATE TABLE `calendar_notification` (
  `id` int(11) NOT NULL,
  `to_user_id` int(11) NOT NULL,
  `from_user_id` varchar(255) DEFAULT NULL,
  `task` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `task_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `calendar_notification`
--

INSERT INTO `calendar_notification` (`id`, `to_user_id`, `from_user_id`, `task`, `timestamp`, `task_time`) VALUES
(49, 2, NULL, 'test', '2020-01-09 13:17:18', '2020-01-09 11:02:00'),
(51, 2, NULL, '32', '2020-01-09 13:17:33', '2020-01-10 11:02:00'),
(52, 2, NULL, 'ca you mode ball yh hi how are you fine goo come to me ok succes perfect', '2020-01-09 13:21:40', '2020-01-10 15:02:00');

-- --------------------------------------------------------

--
-- Table structure for table `chat_message`
--

CREATE TABLE `chat_message` (
  `chat_message_id` int(11) NOT NULL,
  `id_chat_group` int(11) DEFAULT NULL,
  `to_user_id` varchar(255) NOT NULL,
  `from_user_id` int(11) NOT NULL,
  `chat_message` text DEFAULT NULL,
  `forward` int(11) NOT NULL DEFAULT -1,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `chat_message`
--

INSERT INTO `chat_message` (`chat_message_id`, `id_chat_group`, `to_user_id`, `from_user_id`, `chat_message`, `forward`, `timestamp`, `status`) VALUES
(830, NULL, '6', 2, 'hello', -1, '2020-01-09 13:32:11', 0),
(831, NULL, '2', 6, 'hi', -1, '2020-01-09 13:33:46', 0),
(832, NULL, '2', 6, 'http://crm.loc/chat/chat/../filemessage/da6PyjgoMi.docx', -1, '2020-01-09 13:34:35', 0),
(833, NULL, '6', 2, '111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111', -1, '2020-01-09 13:48:47', 0),
(834, 20, '1,4,2', 2, 'ho', -1, '2020-01-09 16:21:02', 1);

-- --------------------------------------------------------

--
-- Table structure for table `fixed_message`
--

CREATE TABLE `fixed_message` (
  `fixed_message_id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `id_chat_group` int(11) DEFAULT NULL,
  `to_user_id` varchar(255) NOT NULL,
  `from_user_id` int(11) NOT NULL,
  `fixed_message` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `forward_message`
--

CREATE TABLE `forward_message` (
  `id` int(11) NOT NULL,
  `to_user_id` text NOT NULL,
  `from_user_id` int(11) NOT NULL,
  `forward_text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `group_chats`
--

CREATE TABLE `group_chats` (
  `id` int(11) NOT NULL,
  `name_chat` varchar(255) NOT NULL,
  `members_chat` text NOT NULL,
  `creator_group` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `group_chats`
--

INSERT INTO `group_chats` (`id`, `name_chat`, `members_chat`, `creator_group`) VALUES
(11, 'first', '2,6', 2),
(12, 'second', '1,2,4,6', 2),
(18, '45ef', '1,4,2', 2),
(19, '34dfs', '5,4,2', 2),
(20, 'hjg', '1,4,2', 2);

-- --------------------------------------------------------

--
-- Table structure for table `login_details`
--

CREATE TABLE `login_details` (
  `login_details_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `login_details`
--

INSERT INTO `login_details` (`login_details_id`, `user_id`, `last_activity`) VALUES
(42, 2, '2020-01-09 16:21:31'),
(43, 5, '2020-01-06 12:59:19'),
(44, 4, '2019-12-28 18:52:25'),
(45, 1, '2020-01-08 11:53:06'),
(46, 6, '2020-01-09 13:55:59'),
(47, 8, '2020-01-08 14:58:20');

-- --------------------------------------------------------

--
-- Table structure for table `message_bot_notification`
--

CREATE TABLE `message_bot_notification` (
  `id` int(11) NOT NULL,
  `id_task` int(11) NOT NULL,
  `to_user_id` int(11) NOT NULL,
  `from_user_id` int(11) DEFAULT NULL,
  `text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `patronymic` varchar(255) NOT NULL,
  `department` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `confirmation` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `lastname`, `patronymic`, `department`, `position`, `password`, `phone`, `email`, `role`, `confirmation`) VALUES
(1, 'admin', 'admin', 'admin', 'admin', 'admin', '21232f297a57a5a743894a0e4a801fc3', 'admin', 'admin', 'admin', 1),
(2, 'qwerty', 'qwerty', 'qwerty', 'qwerty', 'qwerty', 'd8578edf8458ce06fbc5bb76a58c5ca4', 'qwerty', 'qwerty', 'qwerty', 1),
(4, 'dora', 'dora', 'dora', 'dora', 'dora', '1f545a6d49bd6dc815ddd731d0c2a2ad', 'dora', 'dora', 'dora', 1),
(5, 'josh', 'josh', 'josh', 'josh', 'josh', 'f94adcc3ddda04a8f34928d862f404b4', 'josh', 'josh', 'josh', 1),
(6, '1', '1', '1', '1', '1', 'c4ca4238a0b923820dcc509a6f75849b', '1', '1', '1', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `calendar_notification`
--
ALTER TABLE `calendar_notification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chat_message`
--
ALTER TABLE `chat_message`
  ADD PRIMARY KEY (`chat_message_id`);

--
-- Indexes for table `fixed_message`
--
ALTER TABLE `fixed_message`
  ADD PRIMARY KEY (`fixed_message_id`);

--
-- Indexes for table `forward_message`
--
ALTER TABLE `forward_message`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `group_chats`
--
ALTER TABLE `group_chats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_details`
--
ALTER TABLE `login_details`
  ADD PRIMARY KEY (`login_details_id`);

--
-- Indexes for table `message_bot_notification`
--
ALTER TABLE `message_bot_notification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `calendar_notification`
--
ALTER TABLE `calendar_notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `chat_message`
--
ALTER TABLE `chat_message`
  MODIFY `chat_message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=835;

--
-- AUTO_INCREMENT for table `fixed_message`
--
ALTER TABLE `fixed_message`
  MODIFY `fixed_message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `forward_message`
--
ALTER TABLE `forward_message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=364;

--
-- AUTO_INCREMENT for table `group_chats`
--
ALTER TABLE `group_chats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `login_details`
--
ALTER TABLE `login_details`
  MODIFY `login_details_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `message_bot_notification`
--
ALTER TABLE `message_bot_notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
