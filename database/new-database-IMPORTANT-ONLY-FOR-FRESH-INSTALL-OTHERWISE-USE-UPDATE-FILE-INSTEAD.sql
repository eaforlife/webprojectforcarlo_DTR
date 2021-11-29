SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `datetimerecorddb` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `datetimerecorddb`;

DROP TABLE IF EXISTS `emp_accounts`;
CREATE TABLE `emp_accounts` (
  `acctID` int(11) NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `userName` varchar(255) NOT NULL,
  `passWord` varchar(32) NOT NULL COMMENT 'md5 values',
  `email` varchar(255) NOT NULL,
  `photo` varchar(100) NOT NULL DEFAULT 'generic_avatar.png',
  `status` tinyint(1) NOT NULL COMMENT '0 or 1. False or True',
  `isAdmin` tinyint(1) NOT NULL COMMENT '0 for false, 1 for true'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `emp_accounts` (`acctID`, `firstName`, `lastName`, `userName`, `passWord`, `email`, `photo`, `status`, `isAdmin`) VALUES
(1000000001, 'Reaganaa', 'Villegasaa', 'admin', '1a1dc91c907325c69271ddf0c944bc72', 'reagan.villegas@feu.edu', 'generic_avatar.png', 1, 1),

DROP TABLE IF EXISTS `emp_time`;
CREATE TABLE `emp_time` (
  `timeID` int(11) NOT NULL,
  `empID` int(11) NOT NULL,
  `timeMode` varchar(2) NOT NULL COMMENT '0 for time in, 1 for time out, 2 for idle time',
  `timeDateTime` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

ALTER TABLE `emp_accounts`
  ADD PRIMARY KEY (`acctID`),
  ADD UNIQUE KEY `acctID` (`acctID`);

ALTER TABLE `emp_time`
  ADD PRIMARY KEY (`timeID`);


ALTER TABLE `emp_accounts`
  MODIFY `acctID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100000001;

ALTER TABLE `emp_time`
  MODIFY `timeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
