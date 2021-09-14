-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 26, 2021 at 06:25 PM
-- Server version: 10.4.17-MariaDB
-- PHP Version: 8.0.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bugdatabase2`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `assign_bug_to_user` (IN `userID` INT(25), IN `bugID` INT(25), IN `adminID` INT(25))  NO SQL
BEGIN
-- Check if adminID is in fact an admin
	IF EXISTS(SELECT ID, TypeName FROM user_details WHERE user_details.ID = adminID AND user_details.TypeName = "Admin") THEN
-- if user exists
        IF EXISTS(SELECT ID FROM user WHERE user.ID = userID) THEN        
-- if bug exists
            IF EXISTS(SELECT BugID FROM bug WHERE bug.BugID = bugID) THEN
            UPDATE bug_details SET bug_details.UserID = userID WHERE bug_details.ID = bugID;
            UPDATE bug SET bug.Status = "Assigned" WHERE bug.BugID = bugID;
          	END IF;
   		END IF;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `assign_user_to_project` (IN `userID` INT(25), IN `projectID` INT(25), IN `adminID` INT(25))  NO SQL
BEGIN

IF NOT EXISTS(SELECT UserID, ProjectID FROM user_project WHERE user_project.UserID = userID AND user_project.ProjectID = projectID) THEN 
		IF EXISTS(SELECT ID, TypeName FROM user_details WHERE user_details.ID = adminID AND user_details.TypeName = "Admin") THEN
         -- Check if user exists in user table
        IF EXISTS(SELECT ID FROM user WHERE user.ID = userID) THEN
        	-- Check if project exists in project table
            IF EXISTS(SELECT ID FROM project WHERE project.ID = projectID) THEN
        INSERT INTO user_project(UserID, ProjectID) VALUES(userID,projectID);
                END IF;
            END IF;
        END IF;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `close_bug` (IN `userID` INT(25), IN `bugID` INT(25))  NO SQL
BEGIN
	UPDATE view_bug_user_status 
    SET view_bug_user_status.Status = "Closed"
    WHERE view_bug_user_status.UserID = userID 
    AND view_bug_user_status.BugID = bugID
    AND view_bug_user_status.Status != "Closed";
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `create_new_bug` (IN `priority` VARCHAR(250), IN `description` TEXT, IN `osInfo` VARCHAR(500), IN `hwInfo` VARCHAR(500), IN `projectID` INT(25))  NO SQL
BEGIN
	DECLARE IDOfNewBug INT(25);
    DECLARE statusInfo varchar(250);
	IF EXISTS(SELECT ID FROM project WHERE project.ID = projectID) THEN
    	SET statusInfo = "Not Assigned";
    INSERT bug(ProjectID,Priority,Status) VALUES(projectID,priority,statusInfo);
        SET IDOfNewBug = LAST_INSERT_ID();
        INSERT bug_details(ID,UserID,Description,OSInfo,HWInfo) VALUES(IDOfNewBug,-1,description,osInfo,hwInfo);
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `create_project` (IN `title` VARCHAR(250), IN `description` TEXT, IN `platform` VARCHAR(250))  NO SQL
BEGIN
    DECLARE IDOfNewProject INT(25);
	INSERT INTO project(Title) VALUES(title);
    
    SET IDOfNewProject = LAST_INSERT_ID();
        
    INSERT INTO project_details(ID,Description,Platform) 	VALUES(IDOfNewProject,description,platform);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_bug_details` (IN `bugID` INT(25))  NO SQL
SELECT 
view_bug_details.BugID,
view_bug_details.Priority,
view_bug_details.Status,
view_bug_details.Description,
view_bug_details.OSInfo,
view_bug_details.HWInfo
FROM view_bug_details WHERE view_bug_details.BugID = bugID$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_project_bugs` (IN `projectID` INT(25), IN `statusFlag` VARCHAR(250))  NO SQL
SELECT view_detailed_info_project_bugs.BugID FROM view_detailed_info_project_bugs WHERE view_detailed_info_project_bugs.ProjectID = projectID
AND view_detailed_info_project_bugs.Status = statusFlag$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_project_details` (IN `projectID` INT(25))  NO SQL
SELECT * FROM project_details where project_details.ID = projectID$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_project_user_bugs` (IN `projectID` INT(25), IN `userID` INT(25))  NO SQL
BEGIN
	SELECT view_detailed_info_project_bugs.Title, view_detailed_info_project_bugs.BugID, view_detailed_info_project_bugs.Priority, view_detailed_info_project_bugs.Status
    from view_detailed_info_project_bugs
    WHERE view_detailed_info_project_bugs.ProjectID = projectID AND
    view_detailed_info_project_bugs.UserID = userID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_user_bugs_with_status_flag` (IN `userID` INT(25), IN `statusFlag` VARCHAR(25))  NO SQL
SELECT project.Title, BUG.BugID, bug.Priority,bug.Status 	 FROM bug
	INNER JOIN bug_details ON bug_details.ID = bug.BugID
    INNER JOIN project ON bug.ProjectID = project.ID
    WHERE bug_details.UserID = userID AND bug.Status = statusFlag$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_user_details` (IN `userID` INT(25))  NO SQL
SELECT * from user
INNER JOIN user_details on user_details.ID = user.ID
where user_details.ID = userID$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_user_projects` (IN `userID` INT(25))  NO SQL
BEGIN
SELECT view_user_projects.ProjectID, view_user_projects.Title, view_user_projects.Platform from view_user_projects where view_user_projects.UserID = userID;


END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_user_type_id` (IN `userID` INT(25))  NO SQL
BEGIN

SELECT TypeID FROM user_details WHERE user_details.ID = userID;


END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `register_new_user` (IN `userName` VARCHAR(250), IN `passwordIn` VARCHAR(250), IN `firstName` VARCHAR(250), IN `lastName` VARCHAR(250), IN `typeID` INT(25), IN `deparment` VARCHAR(250))  NO SQL
BEGIN
	DECLARE typeName VARCHAR(25);
    DECLARE IDOfNewUser INT(11);
    
    SET typeName = CASE WHEN typeID = 1 THEN "Admin" WHEN typeID = 0 THEN "User" END;
    
    INSERT INTO user(UserName,FirstName,LastName,Password) VALUES (userName,firstName,lastName,passwordIn);
    SET IDOfNewUser = LAST_INSERT_ID();
    INSERT INTO user_details(ID,TypeID,TypeName,Department) VALUES(IDOfNewUser,typeID,typeName,deparment);

END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `bug`
--

CREATE TABLE `bug` (
  `ProjectID` int(25) NOT NULL,
  `BugID` int(25) NOT NULL,
  `Priority` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `Status` varchar(250) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `bug`
--

INSERT INTO `bug` (`ProjectID`, `BugID`, `Priority`, `Status`) VALUES
(12, 24, 'High', 'Assigned'),
(12, 25, 'High', 'Assigned'),
(8, 26, 'High', 'Assigned'),
(12, 27, 'High', 'Closed'),
(10, 28, 'High', 'Closed'),
(10, 29, 'High', 'Not Assigned'),
(11, 30, 'High', 'Assigned'),
(12, 33, 'High', 'Assigned'),
(10, 35, 'High', 'Not Assigned'),
(14, 44, 'High', 'Assigned'),
(19, 48, 'Medium', 'Closed'),
(20, 49, 'High', 'Closed');

-- --------------------------------------------------------

--
-- Table structure for table `bug_details`
--

CREATE TABLE `bug_details` (
  `ID` int(25) NOT NULL,
  `UserID` int(25) NOT NULL,
  `Description` text COLLATE utf8_unicode_ci NOT NULL,
  `OSInfo` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `HWInfo` varchar(500) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `bug_details`
--

INSERT INTO `bug_details` (`ID`, `UserID`, `Description`, `OSInfo`, `HWInfo`) VALUES
(24, 15, 'V okviru razreda player se zgubi referenca na objekt car\r\n\r\nizpis:\r\n\"Unhandled exception thrown: read access violation. p1 was nullptr.\"\r\n\r\ndatoteka \r\ngame.cpp', '/', '/'),
(25, 15, 'V datoteki client.cpp\r\n\r\nproblem Memory Leak:\r\n\r\n[hello.c:25] **LEAK_ASSIGN**\r\n>>         string_so_far = string;\r\n  Memory leaked due to pointer reassignment: string\r\n  Lost block : 0x0804bd68 thru 0x0804bd6f (8 bytes)\r\n               string, allocated at hello.c, 15\r\n                          malloc()  (interface)\r\n                            main()  hello.c, 15\r\n  Stack trace where the error occurred:\r\n                            main()  hello.c, 25', '/', '/'),
(26, 15, 'napaka v home.html', '/', '/'),
(27, 15, 'test', '/', '/'),
(28, 16, 'Napaka v razredu Socket.java\r\n\r\nat java.net.SocketInputStream.read(Unknown Source)\r\nat sun.nio.cs.StreamDecoder.readBytes(Unknown Source)', '/', '/'),
(29, -1, 'napaka v razredu Test.java\r\n\r\n1 error found:\r\n    File: Test.java  [line: 7]\r\n    Error: Test.java:7: cannot find symbol\r\n    symbol  : variable my_method\r\n    location: class Test\r\n', '/', '/'),
(30, 16, 'Napaka v binaryTreeNode.cpp\r\n\r\n\"Exception thrown: read access violation.\r\np1 was 0xCCCCCCCC.\"', '/', '/'),
(33, 16, 'Napaka v datoteki main.cpp', '/', '/'),
(35, -1, 'error: expected expression before \".\" token\r\n     while(.) ', '/', '/'),
(44, 16, 'error: Integer size cannot hold our age in milliseconds', '/', 'ESP32 '),
(48, 16, 'prva napaka', '/', '/'),
(49, 15, 'finalna napaka', '/', '/');

-- --------------------------------------------------------

--
-- Table structure for table `project`
--

CREATE TABLE `project` (
  `ID` int(25) NOT NULL,
  `Title` varchar(250) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `project`
--

INSERT INTO `project` (`ID`, `Title`) VALUES
(8, 'E-računalniške-komponente\r\n'),
(10, 'klepetalnica'),
(11, 'Geodetska aplikacija'),
(12, 'Racer Game'),
(13, 'AI Model'),
(14, 'vrgajeni sistem'),
(19, 'test projekt'),
(20, 'finalni test');

-- --------------------------------------------------------

--
-- Table structure for table `project_details`
--

CREATE TABLE `project_details` (
  `ID` int(25) NOT NULL,
  `Description` text COLLATE utf8_unicode_ci NOT NULL,
  `Platform` varchar(250) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `project_details`
--

INSERT INTO `project_details` (`ID`, `Description`, `Platform`) VALUES
(8, 'tehnična spletna trgovina', 'brskalnik'),
(10, 'klepetalnica z tcp streznikom in tcp klientom', 'Java'),
(11, 'c++ vizualna aplikacija, zemljevid z geodetskimi podatki', 'Namizna Aplikacija'),
(12, 'c++ račinalniška igra', 'Namizna Aplikacija'),
(13, 'model za predikcijo cene izdelka na trgu', 'Cross platform'),
(14, '/', 'ESP32'),
(19, 'test projekt opis', 'brskalnik'),
(20, 'finalni test opis', '/');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `ID` int(25) NOT NULL,
  `UserName` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `FirstName` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `LastName` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `Password` varchar(250) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`ID`, `UserName`, `FirstName`, `LastName`, `Password`) VALUES
(10, 'Admin', 'Admin', 'Admin', '$2y$10$T87wW8yaw1Yv4xsvT.63VutVhLqPe3Y6uuEmwoEb36MShdl5lN/Lu'),
(15, 'matjaz', 'matjaz', 'muc', '$2y$10$3KFBWiWHc90d1XwyWQG.veGNtbp/eIwfdurD7zI8oIXfpFVKCrgom'),
(16, 'user', 'user ime', 'user priimek', '$2y$10$qTXgg.KSdZPx7guSvGAsluh9Fnu0Hwy996JvxH3uUI3Zz9it/NM5i');

-- --------------------------------------------------------

--
-- Table structure for table `user_details`
--

CREATE TABLE `user_details` (
  `ID` int(25) NOT NULL,
  `TypeID` int(25) NOT NULL,
  `TypeName` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `Department` varchar(250) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `user_details`
--

INSERT INTO `user_details` (`ID`, `TypeID`, `TypeName`, `Department`) VALUES
(10, 1, 'Admin', 'Management'),
(15, 0, 'User', 'fakulteta za elektrotehniko'),
(16, 0, 'User', 'user oddelek');

-- --------------------------------------------------------

--
-- Table structure for table `user_project`
--

CREATE TABLE `user_project` (
  `UserID` int(25) NOT NULL,
  `ProjectID` int(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `user_project`
--

INSERT INTO `user_project` (`UserID`, `ProjectID`) VALUES
(15, 12),
(15, 8),
(16, 11),
(16, 10),
(16, 12),
(16, 14),
(16, 19),
(15, 20);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_all_project`
-- (See below for the actual view)
--
CREATE TABLE `view_all_project` (
`ID` int(25)
,`Title` varchar(250)
,`Description` text
,`Platform` varchar(250)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_all_user`
-- (See below for the actual view)
--
CREATE TABLE `view_all_user` (
`ID` int(25)
,`FirstName` varchar(250)
,`LastName` varchar(250)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_bug_details`
-- (See below for the actual view)
--
CREATE TABLE `view_bug_details` (
`ID` int(25)
,`UserID` int(25)
,`Description` text
,`OSInfo` varchar(500)
,`HWInfo` varchar(500)
,`ProjectID` int(25)
,`BugID` int(25)
,`Priority` varchar(250)
,`Status` varchar(250)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_bug_user_status`
-- (See below for the actual view)
--
CREATE TABLE `view_bug_user_status` (
`BugID` int(25)
,`UserID` int(25)
,`Status` varchar(250)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_detailed_info_project_bugs`
-- (See below for the actual view)
--
CREATE TABLE `view_detailed_info_project_bugs` (
`ProjectID` int(25)
,`UserID` int(25)
,`BugID` int(25)
,`Title` varchar(250)
,`Priority` varchar(250)
,`Status` varchar(250)
,`Description` text
,`OSInfo` varchar(500)
,`HWInfo` varchar(500)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_project_bugs`
-- (See below for the actual view)
--
CREATE TABLE `view_project_bugs` (
`ProjectID` int(25)
,`BugID` int(25)
,`Priority` varchar(250)
,`Status` varchar(250)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_user_projects`
-- (See below for the actual view)
--
CREATE TABLE `view_user_projects` (
`UserID` int(25)
,`ProjectID` int(25)
,`Title` varchar(250)
,`Platform` varchar(250)
);

-- --------------------------------------------------------

--
-- Structure for view `view_all_project`
--
DROP TABLE IF EXISTS `view_all_project`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_all_project`  AS SELECT `project`.`ID` AS `ID`, `project`.`Title` AS `Title`, `project_details`.`Description` AS `Description`, `project_details`.`Platform` AS `Platform` FROM (`project` join `project_details` on(`project`.`ID` = `project_details`.`ID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `view_all_user`
--
DROP TABLE IF EXISTS `view_all_user`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_all_user`  AS SELECT `user`.`ID` AS `ID`, `user`.`FirstName` AS `FirstName`, `user`.`LastName` AS `LastName` FROM `user` ;

-- --------------------------------------------------------

--
-- Structure for view `view_bug_details`
--
DROP TABLE IF EXISTS `view_bug_details`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_bug_details`  AS SELECT `bug_details`.`ID` AS `ID`, `bug_details`.`UserID` AS `UserID`, `bug_details`.`Description` AS `Description`, `bug_details`.`OSInfo` AS `OSInfo`, `bug_details`.`HWInfo` AS `HWInfo`, `bug`.`ProjectID` AS `ProjectID`, `bug`.`BugID` AS `BugID`, `bug`.`Priority` AS `Priority`, `bug`.`Status` AS `Status` FROM (`bug_details` join `bug` on(`bug`.`BugID` = `bug_details`.`ID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `view_bug_user_status`
--
DROP TABLE IF EXISTS `view_bug_user_status`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_bug_user_status`  AS SELECT `bug`.`BugID` AS `BugID`, `bug_details`.`UserID` AS `UserID`, `bug`.`Status` AS `Status` FROM (`bug` join `bug_details` on(`bug_details`.`ID` = `bug`.`BugID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `view_detailed_info_project_bugs`
--
DROP TABLE IF EXISTS `view_detailed_info_project_bugs`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_detailed_info_project_bugs`  AS SELECT `project`.`ID` AS `ProjectID`, `bug_details`.`UserID` AS `UserID`, `bug_details`.`ID` AS `BugID`, `project`.`Title` AS `Title`, `bug`.`Priority` AS `Priority`, `bug`.`Status` AS `Status`, `bug_details`.`Description` AS `Description`, `bug_details`.`OSInfo` AS `OSInfo`, `bug_details`.`HWInfo` AS `HWInfo` FROM ((`project` join `bug` on(`bug`.`ProjectID` = `project`.`ID`)) join `bug_details` on(`bug_details`.`ID` = `bug`.`BugID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `view_project_bugs`
--
DROP TABLE IF EXISTS `view_project_bugs`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_project_bugs`  AS SELECT `bug`.`ProjectID` AS `ProjectID`, `bug`.`BugID` AS `BugID`, `bug`.`Priority` AS `Priority`, `bug`.`Status` AS `Status` FROM (`project` join `bug` on(`bug`.`ProjectID` = `project`.`ID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `view_user_projects`
--
DROP TABLE IF EXISTS `view_user_projects`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_user_projects`  AS SELECT `user`.`ID` AS `UserID`, `project`.`ID` AS `ProjectID`, `project`.`Title` AS `Title`, `project_details`.`Platform` AS `Platform` FROM (((`user` join `user_project` on(`user_project`.`UserID` = `user`.`ID`)) join `project` on(`project`.`ID` = `user_project`.`ProjectID`)) join `project_details` on(`project_details`.`ID` = `project`.`ID`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bug`
--
ALTER TABLE `bug`
  ADD PRIMARY KEY (`BugID`),
  ADD KEY `ProjectID` (`ProjectID`);

--
-- Indexes for table `bug_details`
--
ALTER TABLE `bug_details`
  ADD KEY `ID` (`ID`);

--
-- Indexes for table `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `project_details`
--
ALTER TABLE `project_details`
  ADD KEY `ID` (`ID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `user_details`
--
ALTER TABLE `user_details`
  ADD KEY `ID` (`ID`);

--
-- Indexes for table `user_project`
--
ALTER TABLE `user_project`
  ADD KEY `UserID` (`UserID`),
  ADD KEY `UserID_2` (`UserID`),
  ADD KEY `ProjectID` (`ProjectID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bug`
--
ALTER TABLE `bug`
  MODIFY `BugID` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `project`
--
ALTER TABLE `project`
  MODIFY `ID` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `ID` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bug`
--
ALTER TABLE `bug`
  ADD CONSTRAINT `bug_ibfk_1` FOREIGN KEY (`ProjectID`) REFERENCES `project` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bug_details`
--
ALTER TABLE `bug_details`
  ADD CONSTRAINT `bug_details_ibfk_1` FOREIGN KEY (`ID`) REFERENCES `bug` (`BugID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `project_details`
--
ALTER TABLE `project_details`
  ADD CONSTRAINT `project_details_ibfk_1` FOREIGN KEY (`ID`) REFERENCES `project` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_details`
--
ALTER TABLE `user_details`
  ADD CONSTRAINT `user_details_ibfk_1` FOREIGN KEY (`ID`) REFERENCES `user` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_project`
--
ALTER TABLE `user_project`
  ADD CONSTRAINT `user_project_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_project_ibfk_2` FOREIGN KEY (`ProjectID`) REFERENCES `project` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
