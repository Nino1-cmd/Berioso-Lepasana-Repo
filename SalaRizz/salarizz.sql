-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 06, 2024 at 06:00 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `salarizz`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `AddDeductionToPaycheck` (IN `employeeID` INT, IN `deductionType` VARCHAR(255), IN `deductionAmount` DECIMAL(10,2))   BEGIN
    DECLARE currentYear INT;
    DECLARE currentMonth INT;
    DECLARE paycheckID INT;
    
    -- Retrieve current date components
    SET currentYear = YEAR(CURDATE());
    SET currentMonth = MONTH(CURDATE());
    
    -- Check if the employee has a paycheck for the current month
    SELECT Paycheck_ID INTO paycheckID
    FROM paycheck
    WHERE Employee_ID = employeeID
    AND YEAR(Period_Start_Date) = currentYear
    AND MONTH(Period_Start_Date) = currentMonth;
    
    -- If no paycheck exists for the current month, generate one
    IF paycheckID IS NULL THEN
        CALL GeneratePaycheck(employeeID);
        -- Retrieve the newly generated paycheck ID
        SELECT Paycheck_ID INTO paycheckID
        FROM paycheck
        WHERE Employee_ID = employeeID
        AND YEAR(Period_Start_Date) = currentYear
        AND MONTH(Period_Start_Date) = currentMonth;
    END IF;
    
    -- Insert deduction for the paycheck
    INSERT INTO deductions (Paycheck_ID, Type, Amount)
    VALUES (paycheckID, deductionType, deductionAmount);
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `AddEmployee` (IN `p_Name` VARCHAR(255), IN `p_Address` VARCHAR(255), IN `p_Email` VARCHAR(255), IN `p_Password` VARCHAR(255), IN `p_Job_Title` VARCHAR(255), IN `p_Rate` DECIMAL(10,2), IN `p_Employment_Type` ENUM('full-time','part-time'))   BEGIN
    DECLARE currentYear CHAR(2);
    DECLARE currentMonth CHAR(2);
    DECLARE currentDay CHAR(2);
    DECLARE employeeCount INT;
    DECLARE newEmployeeID VARCHAR(8);
    
    -- Retrieve current date components
    SET currentYear = DATE_FORMAT(CURDATE(), '%y');
    SET currentMonth = DATE_FORMAT(CURDATE(), '%m');
    SET currentDay = DATE_FORMAT(CURDATE(), '%d');
    
    -- Retrieve current number of employees
    SELECT COUNT(*) INTO employeeCount FROM employee WHERE Job_Title != 'admin';
    
    -- Generate Employee_ID
    SET newEmployeeID = CONCAT(
        currentYear, 
        currentMonth, 
        currentDay, 
        LPAD(employeeCount + 1, 2, '0')
    );
    
    -- Check if the job title is 'admin'
    IF p_Job_Title != 'admin' THEN
        -- Insert new employee
        INSERT INTO employee (Employee_ID, Name, Address, Email, password, Job_Title, Rate, Employment_type)
        VALUES (newEmployeeID, p_Name, p_Address, p_Email, p_Password, p_Job_Title, p_Rate, p_Employment_Type);
    END IF;
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CalculateTotalWorkHoursWithOvertime` (IN `employeeID` INT, IN `month` INT, IN `year` INT, OUT `totalHours` DECIMAL(10,2), OUT `overtimeHours` DECIMAL(10,2))   BEGIN
    DECLARE total DECIMAL(10, 2);
    DECLARE overtime DECIMAL(10, 2);

    -- Calculate total hours worked in the month
    SELECT SUM(TIME_TO_SEC(TIMEDIFF(TimeOut, TimeIn)) / 3600)
    INTO total
    FROM time_record
    WHERE Employee_ID = employeeID
    AND YEAR(`Date`) = year
    AND MONTH(`Date`) = month;

    -- Calculate total overtime hours
    SELECT SUM(
        CASE 
            WHEN TIME_TO_SEC(TIMEDIFF(TimeOut, TimeIn)) / 3600 > 8 THEN TIME_TO_SEC(TIMEDIFF(TimeOut, TimeIn)) / 3600 - 8
            ELSE 0
        END
    ) AS overtime_hours
    INTO overtime
    FROM time_record
    WHERE Employee_ID = employeeID
    AND YEAR(`Date`) = year
    AND MONTH(`Date`) = month;

    SET totalHours = total;
    SET overtimeHours = overtime;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GeneratePaycheck` (IN `employeeID` INT)   BEGIN
    DECLARE currentYear INT;
    DECLARE currentMonth INT;
    
    -- Retrieve current date components
    SET currentYear = YEAR(CURDATE());
    SET currentMonth = MONTH(CURDATE());

    -- Insert paycheck record with null values for gross pay and net pay
    INSERT INTO paycheck (Employee_ID, Period_Start_Date, Period_End_Date, Gross_Pay, Net_Pay)
    VALUES (employeeID, CONCAT(currentYear, '-', LPAD(currentMonth, 2, '00'), '-01'), LAST_DAY(CONCAT(currentYear, '-', LPAD(currentMonth, 2, '00'), '-01')), NULL, NULL);

END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `bonus`
--

CREATE TABLE `bonus` (
  `Bonus_ID` int(11) NOT NULL,
  `Type` varchar(255) NOT NULL,
  `Amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bonus`
--

INSERT INTO `bonus` (`Bonus_ID`, `Type`, `Amount`) VALUES
(1, '13th month', '1000.00');

-- --------------------------------------------------------

--
-- Table structure for table `deductions`
--

CREATE TABLE `deductions` (
  `Deduction_ID` int(11) NOT NULL,
  `Paycheck_ID` int(11) DEFAULT NULL,
  `Type` varchar(255) DEFAULT NULL,
  `Amount` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deductions`
--

INSERT INTO `deductions` (`Deduction_ID`, `Paycheck_ID`, `Type`, `Amount`) VALUES
(1, NULL, 'Health insurance', '500.00'),
(2, 5, 'test', '10000.00');

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `Employee_ID` int(11) NOT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `Job_Title` varchar(255) DEFAULT NULL,
  `Rate` decimal(10,2) NOT NULL,
  `Employment_type` enum('Full-Time','Part-Time') NOT NULL,
  `SP_pos` enum('NULL','admin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`Employee_ID`, `Name`, `Address`, `Email`, `password`, `Job_Title`, `Rate`, `Employment_type`, `SP_pos`) VALUES
(0, 'admin', 'admin', 'admin@admin.com', '$2y$10$QRrZfZZZR1l4u0bFw9BG2.hdemNeHV4XugLsn3ooFknxhJs1pPa8G', 'admin', '0.00', 'Full-Time', 'admin'),
(22102959, 'test', 'test', 'test@test.com', '$2y$10$ZSR6elOGzDdyYu7gxB6fguOPIe8EueIpBpdKylQBwSTG/zkAr504a', 'Janitor', '24.50', 'Part-Time', 'NULL'),
(22222222, 'bens', 'besn', 'vinsberioso@gmail.com', '$2y$10$mUwknKNIVztrAFdWJ.BkzOoh646V/jckBRCCI5SNRVUnrapRVMjWO', 'workettest', '230.00', 'Full-Time', 'NULL'),
(24050502, 'John Doe', '123 Main St', 'john@example.com', '$2y$10$97.ZRchawCPHgw1YJyCAcOMSnZ.kGltIBKP0bBNiANLlt0uHFipoa', 'employee', '15.00', 'Full-Time', 'NULL');

-- --------------------------------------------------------

--
-- Table structure for table `paycheck`
--

CREATE TABLE `paycheck` (
  `Paycheck_ID` int(11) NOT NULL,
  `Employee_ID` int(11) DEFAULT NULL,
  `Period_Start_Date` date DEFAULT NULL,
  `Period_End_Date` date DEFAULT NULL,
  `Gross_Pay` decimal(10,2) DEFAULT NULL,
  `Net_Pay` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `paycheck`
--

INSERT INTO `paycheck` (`Paycheck_ID`, `Employee_ID`, `Period_Start_Date`, `Period_End_Date`, `Gross_Pay`, `Net_Pay`) VALUES
(1, 22222222, '2024-04-01', '2024-04-30', '100000.95', '90666.05'),
(2, 22222222, '2024-03-01', '2024-03-31', '96000.55', '80666.05'),
(4, 22222222, '2024-05-01', '2024-05-31', '0.00', NULL),
(5, 22102959, '2024-05-01', '2024-05-31', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tax`
--

CREATE TABLE `tax` (
  `Tax_ID` int(11) NOT NULL,
  `Tax_Type` varchar(255) DEFAULT NULL,
  `Rate` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tax`
--

INSERT INTO `tax` (`Tax_ID`, `Tax_Type`, `Rate`) VALUES
(1, 'income tax', '0.02');

-- --------------------------------------------------------

--
-- Table structure for table `time_record`
--

CREATE TABLE `time_record` (
  `Time_ID` int(11) NOT NULL,
  `Employee_ID` int(11) DEFAULT NULL,
  `Date` date DEFAULT NULL,
  `TimeIn` time DEFAULT NULL,
  `TimeOut` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `time_record`
--

INSERT INTO `time_record` (`Time_ID`, `Employee_ID`, `Date`, `TimeIn`, `TimeOut`) VALUES
(1, 22102959, '2024-05-04', '06:00:37', '20:19:30'),
(2, 22102959, '2024-05-06', '06:00:37', '10:21:02'),
(3, 22102959, '2024-05-07', '08:22:17', '11:22:17');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bonus`
--
ALTER TABLE `bonus`
  ADD PRIMARY KEY (`Bonus_ID`);

--
-- Indexes for table `deductions`
--
ALTER TABLE `deductions`
  ADD PRIMARY KEY (`Deduction_ID`),
  ADD KEY `Paycheck_ID` (`Paycheck_ID`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`Employee_ID`);

--
-- Indexes for table `paycheck`
--
ALTER TABLE `paycheck`
  ADD PRIMARY KEY (`Paycheck_ID`),
  ADD KEY `paycheck_ibfk_1` (`Employee_ID`);

--
-- Indexes for table `tax`
--
ALTER TABLE `tax`
  ADD PRIMARY KEY (`Tax_ID`);

--
-- Indexes for table `time_record`
--
ALTER TABLE `time_record`
  ADD PRIMARY KEY (`Time_ID`),
  ADD KEY `time_record_ibfk_1` (`Employee_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bonus`
--
ALTER TABLE `bonus`
  MODIFY `Bonus_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `deductions`
--
ALTER TABLE `deductions`
  MODIFY `Deduction_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `paycheck`
--
ALTER TABLE `paycheck`
  MODIFY `Paycheck_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tax`
--
ALTER TABLE `tax`
  MODIFY `Tax_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `time_record`
--
ALTER TABLE `time_record`
  MODIFY `Time_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `deductions`
--
ALTER TABLE `deductions`
  ADD CONSTRAINT `deductions_ibfk_1` FOREIGN KEY (`Paycheck_ID`) REFERENCES `paycheck` (`Paycheck_ID`);

--
-- Constraints for table `paycheck`
--
ALTER TABLE `paycheck`
  ADD CONSTRAINT `paycheck_ibfk_1` FOREIGN KEY (`Employee_ID`) REFERENCES `employee` (`Employee_ID`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `time_record`
--
ALTER TABLE `time_record`
  ADD CONSTRAINT `time_record_ibfk_1` FOREIGN KEY (`Employee_ID`) REFERENCES `employee` (`Employee_ID`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
