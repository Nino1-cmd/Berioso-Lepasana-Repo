<?php
session_start();
require 'salarizz PHP/connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['employee_id'])) {
    // Gather all input data
    $employee_id = $_POST['employee_id'];
    $name = $_POST['Name'];
    $email = $_POST['username'];
    $address = $_POST['address'];
    $rate = $_POST['Rate'];
    $deduction_type = $_POST['type'];
    $deduction_amount = $_POST['amount'];
    $employment_type = $_POST['inputGroupSelect011'];
    $position = $_POST['inputGroupSelect012'];

    // Start transaction
    $conn->beginTransaction();

    try {
        // Update employee information
        $stmt = $conn->prepare("UPDATE employee SET Name=?, Email=?, Address=?, Rate=?, Employment_type=?, Job_Title=? WHERE Employee_ID=?");
        $stmt->execute([$name, $email, $address, $rate, $employment_type, $position, $employee_id]);
        $stmt->closeCursor();

        // Conditionally add deduction to paycheck
        if (!empty($deduction_type) && !empty($deduction_amount)) {
            $stmt = $conn->prepare("CALL AddDeductionToPaycheck(?, ?, ?)");
            $stmt->execute([$employee_id, $deduction_type, $deduction_amount]);
            $stmt->closeCursor();
        }

        // Commit transaction
        $conn->commit();
        echo "Employee updated ";
        if (!empty($deduction_type) && !empty($deduction_amount)) {
            echo "and deduction added successfully!";
        } else {
            echo "successfully!";
        }
    } catch (PDOException $e) {
        // Rollback transaction if operation fails
        $conn->rollback();
        echo "Error updating employee and adding deduction: " . $e->getMessage();
    }

    $conn = null; // Close connection
}
?>
