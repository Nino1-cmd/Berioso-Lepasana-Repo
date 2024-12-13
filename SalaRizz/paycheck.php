<?php
// Assuming you have already connected to your database
require 'salarizz PHP/connect.php';

// Function to retrieve employee information
$user_id = $_SESSION['Employee_ID'];
function getEmployeeInfo($user_id) {
    // Connect to the database (replace with your database connection code)
    $mysqli = new mysqli("localhost", "root", "", "salarizz");

    // Check connection
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli->connect_error;
        exit();
    }

    // Prepare SQL statement
    $sql = "SELECT Name, Address, Employee_ID, Job_Title, Rate FROM employee WHERE Employee_ID = $user_id";

    // Execute SQL statement
    $result = $mysqli->query($sql);

    // Check if the query was successful
    if ($result && $result->num_rows > 0) {
        // Fetch employee information
        $row = $result->fetch_assoc();
        $employeeInfo = array(
            "name" => $row['Name'],
            "address" => $row['Address'],
            "employee_id" => $row['Employee_ID'],
            "job_title" => $row['Job_Title'],
            "rate" => $row['Rate']
        );

        // Free result set
        $result->free();
    } else {
        // No employee found with the given ID
        $employeeInfo = array();
    }

    // Close connection
    $mysqli->close();

    return $employeeInfo;
}

// Function to retrieve pay period information
function getPayPeriodInfo($user_id) {
    // Connect to the database (replace with your database connection code)
    $mysqli = new mysqli("localhost", "root", "", "salarizz");

    // Check connection
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli->connect_error;
        exit();
    }

    // Get current month and year
    $currentMonth = date('m');
    $currentYear = date('Y');

    // Prepare SQL statement to check if paycheck exists for the current month
    $sql = "SELECT Period_Start_Date, Period_End_Date FROM paycheck WHERE Employee_ID = $user_id 
            AND MONTH(Period_Start_Date) = $currentMonth AND YEAR(Period_Start_Date) = $currentYear ORDER BY Paycheck_ID DESC LIMIT 1";

    // Execute SQL statement
    $result = $mysqli->query($sql);

    // Check if the query was successful and paycheck exists for the current month
    if ($result && $result->num_rows > 0) {
        // Fetch pay period information
        $row = $result->fetch_assoc();
        $payPeriodInfo = array(
            "start_date" => $row['Period_Start_Date'],
            "end_date" => $row['Period_End_Date']
        );

        // Free result set
        $result->free();
    } else {
        // No pay period found for the current month, call GeneratePaycheck stored procedure
        $mysqli->query("CALL GeneratePaycheck($user_id)");

        // Now retrieve the pay period information for the current month after generating paycheck
        $sql = "SELECT Period_Start_Date, Period_End_Date FROM paycheck WHERE Employee_ID = $user_id 
                AND MONTH(Period_Start_Date) = $currentMonth AND YEAR(Period_Start_Date) = $currentYear ORDER BY Paycheck_ID DESC LIMIT 1";

        // Execute SQL statement again
        $result = $mysqli->query($sql);

        // Fetch pay period information
        $row = $result->fetch_assoc();
        $payPeriodInfo = array(
            "start_date" => $row['Period_Start_Date'],
            "end_date" => $row['Period_End_Date']
        );

        // Free result set
        $result->free();
    }

    // Close connection
    $mysqli->close();

    return $payPeriodInfo;
}

function getWorkHours($user_id, $currentMonth, $currentYear) {
    // Connect to the database (replace with your database connection code)
    $mysqli = new mysqli("localhost", "root", "", "salarizz");

    // Check connection
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli->connect_error;
        exit();
    }

    // Call stored procedure to calculate total work hours with overtime
    $mysqli->query("CALL CalculateTotalWorkHoursWithOvertime($user_id, $currentMonth, $currentYear, @totalHours, @overtimeHours)");

    // Fetch results
    $result = $mysqli->query("SELECT @totalHours AS Total_Work_Hours, @overtimeHours AS Overtime_Hours");

    // Fetch data
    $row = $result->fetch_assoc();

    // Calculate regular work hours (Total_Work_Hours - Overtime_Hours)
    $regularWorkHours = $row['Total_Work_Hours'] - $row['Overtime_Hours'];
    $overtimeHours = $row['Overtime_Hours'];

    // Close connection
    $mysqli->close();

    return array("regular_work_hours" => $regularWorkHours, "overtime_hours" => $overtimeHours);
}

// Outputting HTML content with dynamic data
$currentMonth = date('m');
$currentYear = date('Y');
$workHours = getWorkHours($user_id, $currentMonth, $currentYear);
$regularWorkHours = $workHours['regular_work_hours'];
$overtimeHours = $workHours['overtime_hours'];
$employeeInfo = getEmployeeInfo($user_id);
$totalEarnings = ($regularWorkHours * $employeeInfo['rate']) + ($overtimeHours * ($employeeInfo['rate'] * 2));

// Update Gross_pay in paycheck table
$mysqli = new mysqli("localhost", "root", "", "salarizz");

// Check connection
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}

// Prepare and execute SQL statement to update Gross_pay
$sql = "UPDATE paycheck SET Gross_pay = $totalEarnings 
        WHERE Employee_ID = $user_id AND MONTH(Period_Start_Date) = $currentMonth AND YEAR(Period_Start_Date) = $currentYear";

if ($mysqli->query($sql) === TRUE) {
    echo "Gross pay updated successfully.";
} else {
    echo "Error updating record: " . $mysqli->error;
}

// Close connection
$mysqli->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
            text-align: baseline;
        }
        th {
            background-color: #000;
            padding: 5px;
            color: #fff;
        }
        .theader {
            background-color: #000;
            border-color: #000;
            color: #fff;
        }
        .theader th {
            border-color: #dddddd;
        }
        .tsubheader {
            background-color: #dddddd;
            border-color: #f2f2f2;
        }
        .title {
            font-weight: bold;
            background-color: #dddddd;
        }
        .hrs, .rate {
            text-align: center;
        }
        .amt {
            text-align: right;
        }
        .subtotal{
            font-weight: 600;
        }
        .subtotal td{
            font-weight: 600;
            border-width: 2px;
        }
    </style>
</head>
    <body>
        <table>
        <tr class="theader">
            <th colspan="2">Employee Information</th>
            <th colspan="2">Paystub Information</th>
        </tr>
        <tr>
            <td class="title">Employee Name:</td>
            <td><?php echo getEmployeeInfo($user_id)['name']; ?></td>
            <td class="title">Pay Period Start:</td>
            <td><?php echo getPayPeriodInfo($user_id)['start_date']; ?></td>
        </tr>
        <tr>
            <td rowspan="2" class="title">Address:</td>
            <td><?php echo getEmployeeInfo($user_id)['address']; ?></td>
            <td class="title">Pay Period End:</td>
            <td><?php echo getPayPeriodInfo($user_id)['end_date']; ?></td>
        </tr>
        <tr>
            <!-- Empty cell for alignment -->
            <td></td>
            <td rowspan="3" colspan="3"></td>
        </tr>
        <tr>
            <td class="title">Employee ID:</td>
            <td><?php echo getEmployeeInfo($user_id)['employee_id']; ?></td>
        </tr>
        <tr>
            <td class="title">Job Title:</td>
            <td><?php echo getEmployeeInfo($user_id)['job_title']; ?></td>
        </tr>
    </table>
    <table>
    <tr>
        <th colspan="4">Earnings</th>
    </tr>
    <tr class="tsubheader">
        <td>Pay Description</td>
        <td class="hrs">Hours/Qty</td>
        <td class="rate">Rate</td>
        <td class="amt">Amount</td>
    </tr>
    <?php
    $currentMonth = date('m');
    $currentYear = date('Y');
    $workHours = getWorkHours($user_id, $currentMonth, $currentYear);
    $regularWorkHours = $workHours['regular_work_hours'];
    $overtimeHours = $workHours['overtime_hours'];
    $employeeInfo = getEmployeeInfo($user_id);
    ?>
    <tr>
        <td>Regular Work</td>
        <td class="hrs"><?php echo $regularWorkHours; ?></td>
        <td class="rate">₱<span><?php echo $employeeInfo['rate']; ?></span></td>
        <td class="amt">₱<span><?php echo number_format($regularWorkHours * $employeeInfo['rate'], 2); ?></span></td>
    </tr>
    <tr>
        <td>Overtime</td>
        <td class="hrs"><?php echo $overtimeHours; ?></td>
        <td class="rate">₱<span><?php echo $employeeInfo['rate'] * 2; ?></span></td>
        <td class="amt">₱<span><?php echo number_format($overtimeHours * ($employeeInfo['rate'] * 2), 2); ?></span></td>
    </tr>
    <?php
    // Display bonus if the current month is December
    if ($currentMonth == 12) {
        ?>
        <tr>
            <td>Bonus</td>
            <td class="hrs">1</td>
            <td class="rate">₱<span>5,000.00</span></td>
            <td class="amt">₱<span>5,000.00</span></td>
        </tr>
        <?php
    }
    ?>
    <tr class="subtotal">
        <td colspan="3" class="amt">TOTAL EARNINGS</td>
        <td class="amt">₱<span><?php echo number_format(($regularWorkHours * $employeeInfo['rate']) + ($overtimeHours * ($employeeInfo['rate'] * 2)), 2); ?></span></td>
    </tr>
</table>
<table>
    <tr>
        <th colspan="4">Deductions</th>
    </tr>
    <tr class="tsubheader">
        <td>Pay Description</td>
        <td>Rate<td>
        <td class="amt">Amount</td>
    </tr>
    <tr>
        <td>Health Insurance</td>
        <td><td>
        <td class="amt">₱<span>500.00</span></td>
    </tr>
    <tr class="subtotal">
        <td colspan="1" class="amt">TOTAL DEDUCTIONS</td>
        <td class="amt">₱<span>500.00</span></td>
    </tr>
</table>
<h3>Net Payable: ₱<span><?php echo number_format((($regularWorkHours * $employeeInfo['rate']) + ($overtimeHours * ($employeeInfo['rate'] * 2))) - 500, 2); ?></span></h3>
</body>
</html>