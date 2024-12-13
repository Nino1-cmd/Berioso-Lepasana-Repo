<?php

// Start session if not already started
session_start();

// Include the database connection file
require 'salarizz PHP/connect.php';

// Get the data sent via POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST["modal-name"];
    $address = $_POST["modal-homeAddress"];
    $email = $_POST["modal-email"];
    $password = $_POST["pwd"]; // Assuming you want to store the password (not recommended without proper encryption)
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hash the password
    $position = $_POST["modal-jobPosition"];
    $rate = $_POST["modal-salary"];
    $employmentType = $_POST["inputGroupSelect01"];

    // Check if email already exists
    $checkStmt = $conn->prepare("SELECT COUNT(*) AS count FROM employee WHERE Email = :email");
    $checkStmt->bindParam(':email', $email);
    $checkStmt->execute();
    $result = $checkStmt->fetch(PDO::FETCH_ASSOC);
    $emailExists = $result['count'] > 0;

    if ($emailExists) {
        echo "<script>alert('Error: Email already exists.');</script>";
    } else {
        // Call a stored procedure to add employee
        $stmt = $conn->prepare("CALL AddEmployee(:name, :address, :email, :password, :position, :rate, :employmentType)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':position', $position);
        $stmt->bindParam(':rate', $rate);
        $stmt->bindParam(':employmentType', $employmentType);

        // Execute the statement
        if ($stmt->execute()) {
            echo "Employee added successfully!";
        } else {
            echo "Error adding employee: " . $stmt->errorInfo()[2];
        }

        // Close statement
        $stmt->closeCursor();
    }

    // Close check statement
    $checkStmt->closeCursor();
}

// Close connection
$conn = null;
?>
