<?php
// Include the database connection file
require 'salarizz PHP/connect.php';

// Check if employee_id is set and is a valid integer
if (isset($_POST['employee_id']) && filter_var($_POST['employee_id'], FILTER_VALIDATE_INT)) {
    // Sanitize the input
    $employee_id = $_POST['employee_id'];

    // Prepare a delete statement
    $stmt = $conn->prepare("DELETE FROM employee WHERE Employee_ID = :employee_id");
    $stmt->bindParam(':employee_id', $employee_id, PDO::PARAM_INT);

    // Execute the statement
    if ($stmt->execute()) {
        // Deletion successful
        echo "Employee deleted successfully!";
    } else {
        // Deletion failed
        echo "Error deleting employee: " . $stmt->errorInfo()[2];
    }

    // Close statement
    $stmt->closeCursor();
} else {
    // Invalid or missing employee_id
    echo "Error: Invalid employee ID.";
}

// Close connection
$conn = null;
?>
