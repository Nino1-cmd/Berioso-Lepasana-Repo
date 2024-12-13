<?php

require 'connect.php'; // Ensure this file includes your database connection

// Function to securely hash the password
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Function to verify hashed password
function verifyPassword($password, $hashedPassword) {
    return password_verify($password, $hashedPassword);
}

// Function to authenticate user
function authenticateUser($email, $password, $conn) {
    try {
        $stmt = $conn->prepare("SELECT * FROM employee WHERE Email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Verify password
            if (verifyPassword($password, $user['password'])) {
                // Password is correct, return user data
                return $user;
            } else {
                // Password is incorrect
                return false;
            }
        } else {
            // User with provided email not found
            return false;
        }
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}

// Check if form is submitted
// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Authenticate user
    $user = authenticateUser($email, $password, $conn);

    if ($user) {
        // Start session (if not already started)
        session_start();
        $_SESSION['visited_login'] = true;

        // Store user data in session
        $_SESSION['user'] = $user;

        // Store employee_id in session
        $_SESSION['Employee_ID'] = $user['Employee_ID'];

        // Check user's job title
        if ($user['SP_pos'] == "admin") {
            // Redirect to loading page with admin role
            header("Location: ../SalaRizz/LoadingPage.html?role=admin");
            exit();
        } else {
            // Redirect to loading page with employee role
            header("Location: ../SalaRizz/LoadingPage.html?role=employee");
            exit();
        }
    } else {
        // Invalid email or password, redirect back to login page
        header("Location: ../SalaRizz/Login.php"); // Change 'login.html' to the login page
        exit();
    }
}

?>


