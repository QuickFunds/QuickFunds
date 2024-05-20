-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 20, 2024 at 07:40 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `quickfunds`
--

-- --------------------------------------------------------

--
-- Table structure for table `administration`
--

CREATE TABLE `administration` (
  `UserID` int(11) NOT NULL,
  `creditcard` varchar(16) NOT NULL,
  `balance` double NOT NULL,
  `Name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `administration`
--

INSERT INTO `administration` (`UserID`, `creditcard`, `balance`, `Name`) VALUES
(1, '1111111111111111', 170, 'Profit');

-- --------------------------------------------------------

--
-- Table structure for table `adminlogs`
--

CREATE TABLE `adminlogs` (
  `CaseID` int(11) NOT NULL,
  `AdminID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `Action` varchar(30) NOT NULL,
  `Comment` varchar(500) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `adminlogs`
--

INSERT INTO `adminlogs` (`CaseID`, `AdminID`, `UserID`, `Action`, `Comment`, `time`) VALUES
(81, 1, 1, 'Restrict', 'User violated terms of service', '2023-12-31 22:00:00'),
(83, 1, 3, 'Suspended', 'User account temporarily suspended for suspicious activity', '2024-01-02 22:00:00'),
(86, 1, 176, 'Suspended', 'User account suspended for abusive behavior', '2024-01-05 22:00:00'),
(89, 1, 1, 'Restrict', 'User violated terms of service', '2024-01-08 22:00:00'),
(91, 1, 3, 'Suspended', 'User account temporarily suspended for suspicious activity', '2024-01-10 22:00:00'),
(94, 1, 176, 'Suspended', 'User account suspended for abusive behavior', '2024-01-13 22:00:00'),
(95, 1, 177, 'Restrict', 'User restricted access to certain features due to security concerns', '2024-01-14 22:00:00'),
(97, 1, 1, 'Restrict', 'User violated terms of service', '2024-01-16 22:00:00'),
(99, 1, 3, 'Suspended', 'User account temporarily suspended for suspicious activity', '2024-01-18 22:00:00'),
(100, 1, 168, 'Restrict', 'User account restricted due to multiple failed login attempts', '2024-01-19 22:00:00'),
(104, 1, 227, 'Restricted', 'User account restricted by system due to suspicious activity', '2024-05-17 13:58:20'),
(136, 1, 227, 'Restricted', 'Test', '2024-05-18 07:26:03'),
(137, 1, 227, 'Active', 'Your account has been reviews and activated.', '2024-05-18 07:28:26'),
(138, 1, 227, 'Active', 'Your account has been reviews and activated.', '2024-05-18 07:32:32'),
(139, 1, 227, 'Suspended', 'malicious behavior and TOS violation. ', '2024-05-18 07:36:37'),
(140, 1, 227, 'Active', 'successful review.', '2024-05-18 07:38:36'),
(144, 1, 227, 'Restricted', 'Suspicious activiy.', '2024-05-18 07:45:01'),
(149, 1, 227, 'Active', 'a successful review.', '2024-05-18 07:52:41'),
(150, 1, 227, 'Restricted', 'the violation of our terms and services.', '2024-05-18 07:53:21'),
(151, 1, 227, 'Active', 'the successful review from our support team.', '2024-05-18 07:55:15'),
(152, 1, 227, 'Restricted', 'Just a test.', '2024-05-18 07:58:25'),
(153, 1, 3, 'Suspended', 'suspected criminal activity.', '2024-05-18 07:59:38'),
(156, 1, 227, 'Restricted', 'TEST', '2024-05-18 08:00:36'),
(157, 1, 3, 'Suspended', 'suspected illegal activity and several TOS violations.', '2024-05-18 08:01:12'),
(158, 1, 3, 'Active', 'a successful review by our support team.', '2024-05-18 08:04:20'),
(159, 1, 227, 'Active', 'test', '2024-05-18 08:04:26'),
(160, 1, 227, 'Active', 'Testing', '2024-05-18 08:06:47'),
(161, 1, 227, 'Restricted', 'User account restricted by system due to suspicious activity', '2024-05-18 08:17:07'),
(163, 1, 227, 'Restricted', 'User account restricted by system due to suspicious activity', '2024-05-18 08:22:39'),
(173, 1, 169, 'Suspended', 'violating our terms of services and indulging in illegal activity.', '2024-05-18 09:00:26'),
(174, 1, 228, 'Restricted', 'User account restricted by system due to suspicious activity', '2024-05-18 14:38:49'),
(178, 1, 231, 'Restricted', 'User account restricted by system due to suspicious activity', '2024-05-19 15:11:10'),
(182, 1, 232, 'Restricted', 'User account restricted by system due to suspicious activity', '2024-05-19 17:12:17');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `ID` int(11) NOT NULL,
  `comment` varchar(1000) NOT NULL,
  `time` timestamp(4) NOT NULL DEFAULT current_timestamp(4) ON UPDATE current_timestamp(4)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`ID`, `comment`, `time`) VALUES
(24, 'I had a smooth experience transferring money through QuickFunds. Very user-friendly interface!', '2024-04-25 05:23:14.0000'),
(25, 'QuickFunds is my go-to platform for sending money to my family abroad. It\'s fast, secure, and reliable.', '2024-04-12 12:47:32.0000'),
(26, 'I encountered an issue with my transaction, but QuickFunds customer support promptly resolved it. Great service!', '2024-03-30 09:10:56.0000'),
(27, 'I appreciate the transparency and security measures QuickFunds implements. It gives me peace of mind when making transactions.', '2024-03-15 17:05:42.0000'),
(28, 'The exchange rates on QuickFunds are very competitive, and the transaction fees are minimal. Highly recommend!', '2024-04-05 11:36:28.0000'),
(29, 'I love the simplicity of QuickFunds. Sending money has never been easier!', '2024-04-20 07:58:17.0000'),
(30, 'I forgot to log out of my QuickFunds account on a public computer, but I\'m relieved to know that it automatically logs out after a period of inactivity.', '2024-05-02 13:20:39.0000'),
(31, 'I have been using QuickFunds for a while now, and I have never had any issues. It\'s a reliable platform for financial transactions.', '2024-04-18 06:45:51.0000'),
(32, 'The QuickFunds app is so convenient. I can manage my transactions on the go!', '2024-03-25 11:14:20.0000'),
(33, 'I had trouble accessing my account, but the password recovery process on QuickFunds was straightforward and quick.', '2024-04-10 14:30:05.0000'),
(34, 'The security measures on QuickFunds are top-notch. I feel confident using the platform for all my financial transactions.', '2024-03-12 18:02:47.0000'),
(35, 'QuickFunds has made it so easy for me to send money to my relatives abroad. I don\'t know what I\'d do without it!', '2024-03-05 09:55:36.0000'),
(36, 'I like how QuickFunds keeps its users informed about any updates or changes to its policies. Transparency is key!', '2024-04-08 09:40:24.0000'),
(37, 'I appreciate the effort QuickFunds puts into ensuring the security of its users\' personal information. It\'s one less thing to worry about.', '2024-04-30 06:15:18.0000'),
(38, 'QuickFunds has simplified the process of sending money internationally. I can\'t imagine using any other platform.', '2024-03-18 12:25:09.0000'),
(39, 'I\'m impressed by the level of customer support QuickFunds offers. They are always available to assist with any inquiries or issues.', '2024-05-08 07:12:33.0000'),
(40, 'The QuickFunds website is very user-friendly, even for someone like me who isn\'t very tech-savvy.', '2024-03-22 06:50:45.0000'),
(41, 'I have been using QuickFunds for my business transactions, and it has been a game-changer. It\'s efficient and reliable.', '2024-04-15 13:55:57.0000'),
(42, 'I had an urgent transaction to make, and QuickFunds processed it quickly without any delays. Thank you!', '2024-04-02 15:40:13.0000'),
(43, 'QuickFunds has become an essential part of my financial management. It\'s reliable, secure, and easy to use.', '2024-04-22 08:30:55.0000'),
(44, 'Nice website!!\\n\\n', '2024-05-17 09:01:08.0000');

-- --------------------------------------------------------

--
-- Table structure for table `creditcards`
--

CREATE TABLE `creditcards` (
  `ccNum` varchar(16) NOT NULL,
  `ccv` varchar(3) NOT NULL,
  `Expiry date` date NOT NULL,
  `Balance` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `creditcards`
--

INSERT INTO `creditcards` (`ccNum`, `ccv`, `Expiry date`, `Balance`) VALUES
('1111111111111111', '111', '2030-01-02', 0),
('1111222211112222', '121', '2025-12-01', 183166),
('1111333311113333', '131', '2026-12-01', 5100),
('2456789123456789', '789', '2025-10-01', 2264),
('3278569123454567', '789', '2025-10-01', 84),
('3789056123454321', '456', '2027-03-01', 3662),
('4890456123454321', '456', '2028-03-01', 729),
('4998765123458765', '234', '2026-07-01', 4017),
('5198765123455678', '234', '2029-07-01', 893);

-- --------------------------------------------------------

--
-- Table structure for table `giftcards`
--

CREATE TABLE `giftcards` (
  `cardnum` varchar(10) NOT NULL,
  `balance` int(11) NOT NULL,
  `isRedeemed` tinyint(1) NOT NULL,
  `createdby` int(11) NOT NULL,
  `redeemedBy` int(11) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `giftcards`
--

INSERT INTO `giftcards` (`cardnum`, `balance`, `isRedeemed`, `createdby`, `redeemedBy`, `createdAt`) VALUES
('0CF0F59647', 10, 0, 232, 1, '2024-03-20 17:36:27'),
('4940ABE12A', 25, 0, 232, 1, '2024-05-20 16:43:54'),
('998CC3D74B', 10, 0, 232, 1, '2024-05-20 16:41:32'),
('B11C5CA3E0', 50, 0, 232, 1, '2024-03-20 17:36:35'),
('BF16C0A7E1', 500, 0, 232, 1, '2024-04-20 16:36:44'),
('C56117ECDE', 250, 0, 232, 1, '2024-04-02 16:36:41'),
('C6706E4249', 25, 0, 232, 1, '2024-01-20 17:36:31'),
('E6CD3B9C3C', 100, 1, 232, 232, '2024-05-20 17:28:45');

-- --------------------------------------------------------

--
-- Table structure for table `surveyresponses`
--

CREATE TABLE `surveyresponses` (
  `id` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `likes` text NOT NULL,
  `improvements` text NOT NULL,
  `additionalComments` text DEFAULT NULL,
  `experienceRating` int(11) NOT NULL,
  `likelihoodRecommend` int(11) NOT NULL,
  `submissionTime` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `surveyresponses`
--

INSERT INTO `surveyresponses` (`id`, `userID`, `likes`, `improvements`, `additionalComments`, `experienceRating`, `likelihoodRecommend`, `submissionTime`) VALUES
(9, 2, 'I like the user-friendly interface and simplicity of the website. It makes navigating and using the features very convenient.', 'One improvement could be to add more customization options for user profiles.', 'Overall, I am satisfied with the service provided by QuickFunds.', 4, 4, '2024-04-25 05:23:14'),
(10, 3, 'The security measures implemented by QuickFunds are commendable. I feel safe and confident using the platform for my transactions.', 'It would be great to see more international currency options for transactions.', 'QuickFunds has been a reliable platform for me, and I appreciate the convenience it offers.', 4, 4, '2024-04-12 12:47:32'),
(11, 168, 'The website layout is intuitive, and the transaction process is smooth. I also appreciate the transparent fee structure.', 'Adding a feature for scheduled transactions would be beneficial for users who need to make regular payments.', 'QuickFunds has exceeded my expectations in terms of reliability and efficiency.', 5, 5, '2024-03-30 09:10:56'),
(12, 169, 'I like the variety of funding options available on QuickFunds, including bank transfers and peer-to-peer trading.', 'Improving the mobile app is performance and responsiveness would enhance the user experience.', 'QuickFunds has been a convenient solution for my financial transactions, especially for international transfers.', 4, 4, '2024-03-15 17:05:42'),
(13, 176, 'The customer support team at QuickFunds is responsive and helpful, providing assistance whenever needed.', 'Introducing a feature for recurring transactions would be beneficial for users who need to make regular payments or transfers.', 'QuickFunds has been my preferred platform for money transfers due to its reliability and security measures.', 5, 5, '2024-04-05 11:36:28'),
(14, 177, 'The exchange rates offered by QuickFunds are competitive, and the transaction fees are minimal.', 'Enhancing the notification system to provide real-time updates on transaction statuses would improve user experience.', 'I have had a positive experience using QuickFunds for my financial transactions, and I would recommend it to others.', 4, 4, '2024-04-20 07:58:17'),
(16, 2, 'The user interface of QuickFunds is clean and easy to navigate, making transactions hassle-free.', 'Introducing more payment methods would provide users with greater flexibility and convenience.', 'I have had a positive experience using QuickFunds, and I would recommend it to others.', 5, 5, '2024-04-18 06:45:51'),
(17, 3, 'The security measures implemented by QuickFunds instill confidence in the platform, ensuring the safety of user information.', 'Adding a feature for recurring payments would be beneficial for users who need to make regular transactions.', 'QuickFunds has been reliable for my financial transactions, and I appreciate the peace of mind it offers.', 5, 5, '2024-03-25 11:14:20'),
(18, 168, 'I like the convenience of QuickFunds for international transactions, and the exchange rates are reasonable.', 'Improving the customer support response time would enhance the user experience.', 'QuickFunds has been my preferred platform for money transfers, and I have had no issues with its reliability.', 4, 4, '2024-04-10 14:30:05'),
(19, 169, 'The user interface of QuickFunds is intuitive and easy to use, even for beginners.', 'Enhancing the transaction history feature to provide more detailed information would be beneficial for users.', 'I have had a positive experience using QuickFunds, and I would recommend it to others.', 5, 5, '2024-03-12 18:02:47'),
(20, 176, 'The security features on QuickFunds are robust, providing peace of mind for users regarding their personal information.', 'Adding more options for currency conversion would be beneficial for international users.', 'QuickFunds has been reliable for my financial transactions, and I appreciate the convenience it offers.', 4, 4, '2024-03-05 09:55:36'),
(21, 177, 'The transaction process on QuickFunds is straightforward and efficient, saving time for users.', 'Improving the accessibility features of the website would enhance user experience for individuals with disabilities.', 'I have had a positive experience using QuickFunds, and I trust the platform for my financial transactions.', 5, 5, '2024-04-08 09:40:24'),
(23, 2, 'QuickFunds offers competitive exchange rates, making it an attractive option for international transactions.', 'Enhancing the mobile app is functionality would improve user experience for individuals who prefer mobile banking.', 'I have had a positive experience using QuickFunds, and I would recommend it to others.', 5, 5, '2024-03-18 12:25:09'),
(24, 3, 'The customer support team at QuickFunds is responsive and helpful, providing assistance whenever needed.', 'Introducing more customization options for user profiles would enhance personalization.', 'QuickFunds has been reliable for my financial transactions, and I appreciate the peace of mind it offers.', 4, 4, '2024-05-08 07:12:33'),
(25, 168, 'The user interface of QuickFunds is clean and user-friendly, making transactions seamless and efficient.', 'Adding support for additional languages would make the platform more accessible to a global audience.', 'QuickFunds has been my preferred platform for money transfers, and I have had no issues with its reliability.', 5, 5, '2024-03-22 06:50:45'),
(26, 169, 'The transparency in the fee structure of QuickFunds is commendable, and there are no hidden charges.', 'Introducing more educational resources on financial literacy would benefit users.', 'I have had a positive experience using QuickFunds, and I trust the platform for my financial transactions.', 4, 4, '2024-04-15 13:55:57'),
(27, 176, 'QuickFunds offers competitive exchange rates and minimal transaction fees, making it cost-effective for users.', 'Enhancing the notification system to provide real-time updates on transaction statuses would improve user experience.', 'QuickFunds has been my preferred platform for money transfers, and I have had no issues with its reliability.', 5, 5, '2024-04-02 15:40:13'),
(28, 177, 'The user interface of QuickFunds is intuitive and easy to navigate, even for individuals who are not tech-savvy.', 'Introducing more payment options would provide users with greater flexibility.', 'QuickFunds has been reliable for my financial transactions, and I appreciate the convenience it offers.', 4, 4, '2024-04-22 08:30:55');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `TxnID` int(11) NOT NULL,
  `FromID` int(11) DEFAULT NULL,
  `ToID` int(11) DEFAULT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp(),
  `Ammount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`TxnID`, `FromID`, `ToID`, `time`, `Ammount`) VALUES
(1, 3, 169, '2024-05-06 11:07:35', 7777),
(2, 169, 3, '2024-02-09 10:55:51', 7679),
(3, 3, 169, '2023-12-29 22:16:28', 59),
(4, 169, 3, '2024-02-21 07:34:47', 33),
(5, 3, 176, '2024-05-02 22:04:34', 96),
(6, 176, 3, '2024-01-01 20:38:32', 36),
(7, 176, 229, '2024-05-15 11:58:57', 25),
(8, 229, 176, '2024-02-25 03:59:13', 52),
(9, 229, 230, '2024-02-19 08:59:14', 61),
(10, 230, 229, '2024-04-11 10:58:34', 93),
(11, 230, 168, '2024-02-29 07:55:10', 26),
(12, 168, 230, '2024-01-13 08:40:07', 60),
(13, 168, 177, '2024-03-04 20:35:04', 20),
(14, 177, 168, '2023-12-14 08:55:04', 39),
(15, 177, 227, '2024-03-01 06:50:08', 64),
(16, 227, 177, '2024-02-24 11:25:32', 65),
(17, 227, 228, '2024-04-21 14:37:14', 64),
(18, 228, 227, '2024-04-04 18:49:38', 34),
(19, 228, 231, '2023-12-31 05:16:28', 76),
(20, 231, 228, '2024-02-21 17:53:26', 51),
(21, 231, 2, '2024-05-01 04:37:48', 79),
(22, 2, 231, '2023-12-22 22:28:44', 54),
(23, 2, 3, '2024-04-01 01:30:17', 17),
(24, 3, 2, '2024-01-23 17:05:14', 42),
(25, 3, 2, '2024-01-21 09:55:19', 99),
(26, 3, 2, '2024-02-24 00:20:53', 101),
(27, 232, 169, '2024-03-09 22:58:30', 49),
(28, 232, 2, '2024-02-12 20:49:53', 22),
(29, 232, 3, '2024-01-28 13:13:58', 52),
(30, 232, 3, '2024-01-30 05:40:10', 9),
(31, 232, 3, '2024-03-24 14:38:56', 9),
(32, 232, 3, '2024-01-31 12:14:16', 9),
(33, 232, 3, '2024-03-22 18:14:30', 9),
(34, 232, 3, '2024-01-18 10:29:46', 9),
(35, 232, 3, '2024-01-22 22:45:20', 9),
(36, 232, 3, '2024-03-18 12:17:23', 9),
(37, 232, 3, '2024-01-22 21:10:58', 9),
(38, 232, 3, '2024-02-26 22:02:42', 9),
(39, 232, 3, '2024-03-20 01:40:39', 9),
(40, 232, 3, '2024-03-25 17:15:09', 9),
(41, 232, 3, '2024-02-16 12:13:49', 9),
(42, 232, 3, '2023-12-30 11:23:39', 9),
(43, 232, 3, '2024-02-02 21:16:48', 9),
(44, 232, 3, '2024-01-30 03:13:07', 9),
(45, 232, 3, '2024-03-08 02:15:11', 9),
(46, 232, 3, '2024-04-18 04:36:36', 9),
(47, 3, 232, '2024-02-09 20:17:28', 122),
(48, 3, 232, '2024-02-24 16:37:33', 122),
(49, 3, 232, '2024-01-15 01:15:21', 122),
(50, 3, 232, '2024-03-27 05:24:09', 122),
(51, 3, 232, '2024-03-30 03:14:45', 122),
(52, 3, 232, '2024-02-17 03:01:18', 122),
(53, 3, 232, '2023-12-20 07:22:17', 122),
(54, 3, 232, '2023-12-12 04:47:29', 122),
(55, 3, 232, '2024-04-26 02:50:36', 122),
(56, 3, 232, '2024-01-21 05:50:39', 122),
(57, 3, 232, '2024-04-02 20:41:23', 122),
(58, 3, 232, '2024-04-13 17:55:01', 19),
(59, 3, 232, '2024-04-10 06:30:59', 19),
(60, 3, 232, '2024-02-20 05:49:54', 19),
(61, 3, 232, '2024-05-10 10:36:34', 19),
(62, 3, 232, '2024-02-11 16:33:11', 19),
(63, 3, 232, '2023-12-29 03:56:14', 19),
(64, 3, 232, '2024-02-10 19:01:36', 19),
(65, 3, 232, '2024-03-13 14:19:18', 19),
(66, 3, 232, '2024-04-10 17:31:47', 19),
(67, 3, 232, '2023-12-18 00:41:04', 19),
(68, 3, 232, '2024-05-07 21:49:54', 19),
(69, 3, 232, '2024-03-02 17:06:25', 19),
(70, 3, 232, '2024-04-14 21:02:22', 19),
(71, 3, 232, '2024-02-09 09:52:39', 19),
(72, 3, 232, '2024-03-03 11:16:06', 19),
(73, 3, 232, '2024-02-24 05:42:38', 19),
(74, 3, 232, '2024-04-13 20:44:53', 19),
(75, 3, 232, '2024-02-26 18:36:33', 19),
(76, 3, 232, '2023-12-24 08:48:09', 19),
(77, 3, 232, '2024-05-11 12:11:02', 19),
(78, 3, 232, '2024-03-01 16:30:50', 19),
(79, 3, 232, '2024-03-29 23:01:01', 19),
(80, 3, 232, '2024-05-02 20:32:39', 19),
(81, 3, 232, '2024-02-17 13:40:15', 19),
(82, 3, 232, '2024-02-19 07:43:17', 19),
(83, 3, 232, '2024-05-03 23:35:39', 19),
(84, 3, 232, '2024-01-13 03:48:31', 19),
(85, 3, 232, '2024-01-29 20:15:37', 19),
(86, 3, 232, '2024-05-07 19:52:31', 19),
(87, 3, 232, '2024-04-02 19:35:49', 19),
(88, 3, 232, '2024-04-10 16:21:33', 19),
(89, 3, 232, '2024-03-26 02:00:21', 19),
(90, 3, 232, '2023-12-15 11:57:08', 19),
(91, 3, 232, '2024-01-03 05:44:35', 19),
(92, 3, 232, '2024-03-21 18:51:31', 19),
(93, 3, 232, '2024-04-07 09:03:55', 19),
(94, 3, 232, '2024-04-14 15:45:02', 19),
(95, 3, 232, '2024-04-01 06:33:28', 19),
(96, 3, 232, '2024-01-03 12:32:17', 19),
(97, 3, 232, '2024-03-19 19:00:59', 19),
(98, 3, 232, '2024-03-27 13:28:09', 19),
(99, 3, 232, '2024-02-26 13:17:48', 19),
(100, 3, 232, '2024-02-13 04:04:32', 19),
(101, 3, 232, '2024-03-07 06:29:19', 19),
(102, 3, 232, '2024-03-02 23:13:02', 19),
(103, 3, 232, '2024-05-11 02:37:12', 19),
(104, 3, 232, '2024-01-10 20:27:00', 19),
(105, 3, 232, '2023-12-27 22:23:22', 19),
(106, 3, 232, '2024-05-10 03:35:52', 19),
(107, 3, 232, '2024-02-13 04:49:16', 19),
(108, 3, 232, '2024-01-06 14:18:44', 19),
(109, 3, 232, '2024-03-19 10:05:55', 19),
(110, 3, 232, '2024-03-16 11:33:23', 19),
(111, 3, 232, '2024-01-03 06:29:11', 19),
(112, 3, 232, '2024-05-04 21:45:46', 19),
(113, 3, 232, '2023-12-29 23:21:20', 19),
(114, 3, 232, '2024-04-25 02:29:21', 19),
(115, 3, 232, '2024-05-01 19:22:51', 19),
(116, 3, 232, '2024-05-03 20:26:12', 19),
(117, 3, 232, '2024-04-23 23:02:27', 19),
(118, 3, 232, '2024-02-28 08:53:44', 19),
(119, 3, 232, '2024-05-10 00:21:17', 19),
(120, 3, 232, '2024-01-16 04:05:18', 19),
(121, 3, 232, '2024-01-26 19:22:39', 19),
(122, 3, 232, '2024-04-13 14:37:21', 19),
(123, 3, 232, '2023-12-12 19:58:52', 19),
(124, 3, 232, '2024-04-03 07:02:13', 19),
(125, 3, 232, '2024-03-05 04:14:34', 19),
(126, 3, 232, '2024-03-02 02:06:13', 19),
(127, 3, 232, '2024-05-12 23:47:24', 19),
(128, 3, 232, '2024-01-22 21:38:23', 19),
(129, 3, 232, '2024-02-20 12:49:43', 19),
(130, 3, 232, '2024-02-17 02:13:30', 19),
(131, 3, 232, '2024-04-13 22:38:19', 19),
(132, 3, 232, '2024-03-19 14:31:09', 19),
(133, 3, 232, '2024-04-11 06:40:48', 19),
(134, 3, 232, '2024-05-10 16:50:35', 19),
(135, 3, 232, '2024-02-19 20:10:33', 19),
(136, 3, 232, '2024-02-07 03:21:00', 19),
(137, 3, 232, '2024-02-26 06:13:24', 19),
(138, 3, 232, '2024-01-31 00:04:02', 19),
(139, 3, 232, '2024-01-02 07:40:00', 19),
(140, 3, 232, '2024-04-06 15:07:51', 19),
(141, 3, 232, '2024-01-20 09:39:20', 19),
(142, 3, 232, '2023-12-19 03:53:05', 19),
(143, 3, 232, '2024-02-27 15:27:28', 19),
(144, 3, 232, '2024-01-27 21:38:07', 19),
(145, 3, 232, '2023-12-13 15:48:12', 19),
(146, 3, 232, '2024-01-08 15:06:39', 19),
(147, 3, 232, '2024-04-23 06:08:38', 19),
(148, 3, 232, '2024-03-24 14:23:19', 19),
(149, 3, 232, '2024-04-08 07:30:42', 19),
(150, 3, 232, '2024-04-10 21:23:35', 19),
(151, 3, 232, '2024-03-10 15:25:49', 19),
(152, 3, 232, '2024-03-06 14:58:24', 19),
(153, 3, 232, '2024-05-19 06:34:31', 19),
(154, 3, 232, '2024-02-09 16:57:28', 19),
(155, 3, 232, '2024-04-30 17:03:46', 19),
(156, 3, 232, '2024-01-25 15:26:32', 19),
(157, 3, 232, '2024-04-11 02:01:17', 19),
(158, 3, 232, '2024-05-11 15:46:55', 19),
(159, 3, 232, '2024-02-25 04:50:46', 19),
(160, 3, 232, '2024-03-02 01:53:07', 19),
(161, 3, 232, '2024-05-19 19:48:23', 99),
(162, 232, 2, '2024-05-20 16:40:25', 9);

-- --------------------------------------------------------

--
-- Table structure for table `treasury`
--

CREATE TABLE `treasury` (
  `ccNum` varchar(16) NOT NULL,
  `ccv` varchar(3) NOT NULL,
  `Expiry date` date NOT NULL,
  `Balance` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `treasury`
--

INSERT INTO `treasury` (`ccNum`, `ccv`, `Expiry date`, `Balance`) VALUES
('2222222222222222', '222', '2032-02-02', 12000);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `UserID` int(11) NOT NULL,
  `UserName` varchar(25) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Email` varchar(90) NOT NULL,
  `verification_code` varchar(40) NOT NULL,
  `ccNum` varchar(16) DEFAULT NULL,
  `balance` decimal(10,0) NOT NULL,
  `LastLogOn` timestamp NULL DEFAULT NULL,
  `isActive` tinyint(1) NOT NULL,
  `isVerifiedEmail` tinyint(1) NOT NULL DEFAULT 0,
  `phoneNum` varchar(13) NOT NULL,
  `AccStatus` varchar(25) NOT NULL,
  `loginAttempts` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`UserID`, `UserName`, `Password`, `Email`, `verification_code`, `ccNum`, `balance`, `LastLogOn`, `isActive`, `isVerifiedEmail`, `phoneNum`, `AccStatus`, `loginAttempts`, `created_at`) VALUES
(1, 'quickfunds', '$2y$10$vAXhB8A009dliFORwvPzpO/1EUwnb/Cjb.CKWZIDCwyrsonDNvSBW', 'admin@quickfunds.com', '', '1111111111111111', 170, '2024-02-26 18:45:44', 0, 1, '+96137785132', 'Active', 0, '2023-12-18 15:53:07'),
(2, 'Taha', '$2y$10$Lb6hOyGlhfzDAomCrNnvdeM5opC.lqaEzKzA/errOlOmrpduk68oy', 'Taha.alfil@gmail.com', '', '4890456123454321', 231, '2024-02-06 18:15:15', 0, 1, '0', 'Active', 0, '2024-01-18 15:53:07'),
(3, 'M.Mekdash', '$2y$10$vAXhB8A009dliFORwvPzpO/1EUwnb/Cjb.CKWZIDCwyrsonDNvSBW', 'hamoudi.com1@gmail.com', '7ae9e95221b556b3f88996291e9678dc', '1111222211112222', 7889, '2024-01-07 15:08:57', 0, 1, '0', 'Active', 0, '2024-01-18 15:53:07'),
(168, 'test1', '$2y$10$1Or4GKN..0SSf.QhwlBKqegSXDiX2dkgbFMgl2aysf4rkq9der2i6', 'test23123@gmail.com', '06b755f0d34a4fec1c35295368080472', '1111222211112222', 0, '2024-04-14 18:24:28', 0, 1, '0', 'Active', 0, '2024-03-18 15:53:07'),
(169, 'Samir', '$2y$10$619BXUosaEpAnUJrwIjGxe07GTEOHsiILapC0B6Le5koJmEYfn7Ii', 'samirrr90210@gmail.com', '6e83c040678fa38dc4462da811dae7bd', NULL, 149, '2024-04-27 13:35:44', 0, 1, '+9613778570', 'Suspended', 0, '2024-04-18 14:53:07'),
(176, 'ali123', '$2y$10$OHvFXqplcQo/Lj53wnU/ceq2fKqSkeKaiy7yNUsCQi.HshPpRJCku', 'aliasd@gmail.com', 'cdff6c51b3ec72dbb88d5f5c2a41a4ce', NULL, 0, '2024-04-27 14:31:07', 0, 1, '0', 'Restricted', 0, '2024-04-19 14:53:07'),
(177, 'TahaF', '$2y$10$RDU.MnKZnufqHONkaeAHkOw5o./DjY11oH4tUzTNH5bkEk049s1jq', 'asd9823hdsadsd@gmail.com', '', '1111222211112222', 0, '2024-05-01 14:23:21', 0, 1, '', 'Active', 0, '2024-04-20 14:53:07'),
(227, 'FreeMoney', '$2y$10$LDNEpf7t0B68h6lEbrdguO7wrE0t6GPJ/.EyekxJHl4U5S29ZEF.e', 'asdasd23qeda@gas.com', '75399b95d42278730df9b7fbbc1dc6ec', '1111222211112222', 0, '2024-05-17 10:48:24', 0, 1, '+9613778570', 'Active', 0, '2024-04-21 14:53:07'),
(228, 'Taha99', '$2y$10$lBZ7RfDuiF6CZrOvnGq9MeeZSro8/mTHzdpCuRwY01IaW2CPf7fuy', 'asdasd@gmail.com', '', '1111222211112222', 0, '2024-05-18 09:03:45', 0, 1, '', 'Active', 0, '2024-05-06 14:53:07'),
(229, 'testStats', '$2y$10$mrXeYmu9Mn0ohT1Iq7CwJuj0ajBXUAPY3J0o91ITi7.7hKHrGVPHi', 'joerdanTSF234234@gmail.com', '3654a54649b19c925de84f3c73a452e8', NULL, 0, '2024-05-18 14:47:39', 0, 0, '', 'Active', 0, '2024-05-18 14:53:07'),
(230, 'test1235', '$2y$10$uwKmIE.CEa.HV8uCqNKBfeW7p7OLrilzN4.kvBtjKqt/tYN12gQDe', 'taha.alfil1231231@gmail.com', '5cd3894a3c6e8eb0c7dd20692b1057f0', NULL, 0, '2024-05-18 14:59:44', 0, 0, '', 'Active', 0, '2024-05-18 14:59:44'),
(231, 'Taha999', '$2y$10$Wkl/aWBnqkCTQkvutEodWezYAIapDzEbBQhwqpQLsxmZE..59lzPS', 'tasdasdft@gmail.com', 'c4d76530d3560eecc44528097f2b3a4b', '1111222211112222', 0, '2024-05-19 15:01:09', 0, 1, '+9613778570', 'Active', 0, '2024-05-19 15:01:09'),
(232, 'SignUpProcess', '$2y$10$V4u0I.2fZmPWXpkZFGuuRekv2LACXojHVdRXvp.lqLlZC1Pmu/DJq', 'thehypocritenft@gmail.com', '', '1111222211112222', 2675, '2024-05-19 16:56:26', 0, 1, '', 'Active', 0, '2024-05-19 16:56:26'),
(233, 'inactive', '$2y$10$oDhQcCYipK6QU8sUfSu0Se6hB3bj1Mh0vFe5uXyQnT6qIAS4jfTMK', 'taha.alf2312wdsil@gmail.com', '8fc48da25cd164ad858a8a5967cfba31', NULL, 0, '2024-05-19 17:36:45', 0, 0, '', 'Active', 0, '2024-05-19 17:36:45');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `administration`
--
ALTER TABLE `administration`
  ADD PRIMARY KEY (`UserID`),
  ADD KEY `ccard` (`creditcard`);

--
-- Indexes for table `adminlogs`
--
ALTER TABLE `adminlogs`
  ADD PRIMARY KEY (`CaseID`),
  ADD KEY `aID` (`AdminID`),
  ADD KEY `userID` (`UserID`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `creditcards`
--
ALTER TABLE `creditcards`
  ADD PRIMARY KEY (`ccNum`);

--
-- Indexes for table `giftcards`
--
ALTER TABLE `giftcards`
  ADD PRIMARY KEY (`cardnum`),
  ADD KEY `creatorID` (`createdby`),
  ADD KEY `redeemerID` (`redeemedBy`);

--
-- Indexes for table `surveyresponses`
--
ALTER TABLE `surveyresponses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`userID`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`TxnID`),
  ADD KEY `from_user` (`FromID`),
  ADD KEY `to_User` (`ToID`);

--
-- Indexes for table `treasury`
--
ALTER TABLE `treasury`
  ADD PRIMARY KEY (`ccNum`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`UserID`),
  ADD KEY `Fkey` (`ccNum`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `adminlogs`
--
ALTER TABLE `adminlogs`
  MODIFY `CaseID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=184;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `surveyresponses`
--
ALTER TABLE `surveyresponses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `TxnID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=163;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=234;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `administration`
--
ALTER TABLE `administration`
  ADD CONSTRAINT `adminid` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `ccard` FOREIGN KEY (`creditcard`) REFERENCES `creditcards` (`ccNum`);

--
-- Constraints for table `adminlogs`
--
ALTER TABLE `adminlogs`
  ADD CONSTRAINT `aID` FOREIGN KEY (`AdminID`) REFERENCES `administration` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `userID` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `giftcards`
--
ALTER TABLE `giftcards`
  ADD CONSTRAINT `creatorID` FOREIGN KEY (`createdby`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `redeemerID` FOREIGN KEY (`redeemedBy`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `surveyresponses`
--
ALTER TABLE `surveyresponses`
  ADD CONSTRAINT `uid` FOREIGN KEY (`userID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `from_user` FOREIGN KEY (`FromID`) REFERENCES `user` (`UserID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `to_User` FOREIGN KEY (`ToID`) REFERENCES `user` (`UserID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `Fkey` FOREIGN KEY (`ccNum`) REFERENCES `creditcards` (`ccNum`) ON UPDATE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
