<?php
session_start();
require 'dbConfig.php'; // Include database configuration

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize user inputs
    $usernameOrEmail = trim($_POST['username']); // This field will accept either username or email
    $password = trim($_POST['password']);

    // Check for empty inputs
    if (empty($usernameOrEmail) || empty($password)) {
        header('Location: login.php?error=Username/email+and+password+are+required');
        exit();
    }

    // Prepare SQL statement to check both username and email
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    if (!$stmt) {
        header('Location: login.php?error=Database+error');
        exit();
    }
    $stmt->bind_param("ss", $usernameOrEmail, $usernameOrEmail); // Bind the same input for both username and email
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password_hash'])) {
            // Set session variables
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            switch ($user['role']) {
                case 'faculty':
                case 'staff':
                case 'student':
                    header('Location: loghome.php');
                    break;
                default:
                    header('Location: home.php');
                    break;
            }
            exit();
        } else {
            header('Location: login.php?error=Incorrect+password');
            exit();
        }
    } else {
        header('Location: login.php?error=User+not+found');
        exit();
    }

    // Close statement and connection
  
   
}
?>
