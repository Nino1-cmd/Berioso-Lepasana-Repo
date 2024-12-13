<?php
session_start();
require 'salarizz PHP/connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['employee_id'])) {
    $employee_id = $_POST['employee_id'];

    $stmt = $conn->prepare("SELECT * FROM employee WHERE Employee_ID = :employee_id");
    $stmt->bindParam(':employee_id', $employee_id, PDO::PARAM_INT);
    $stmt->execute();
    $employee = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($employee);
    
    // Close the statement and connection
    $stmt->closeCursor();
    $conn = null;
}
?>
