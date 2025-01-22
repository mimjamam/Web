<?php
require 'dbConfig.php'; // Include database configuration

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user inputs
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm-password'];
    $role = $_POST['role'];

    // Validate inputs
    if (empty($username) || empty($email) || empty($password) || empty($confirmPassword) || empty($role)) {
        die("All fields are required.");
    }

    // Validate password confirmation
    if ($password !== $confirmPassword) {
        die("Passwords do not match.");
    }

    // Check if username or email already exists
    $checkStmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    if (!$checkStmt) {
        die("Database error: " . $conn->error);
    }

    $checkStmt->bind_param("ss", $username, $email);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        die("Username or email already exists. Please try a different one.");
    }

    $checkStmt->close();

    // Hash the password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Handle profile picture upload
    $targetDir2 = "userimages/";

    // Ensure the uploads directory exists
    if (!is_dir($targetDir2)) {
        if (!mkdir($targetDir2, 0777, true)) {
            die("Failed to create the userimages directory.");
        }
    }

    // Generate a unique file name for the uploaded profile picture
    $uniqueFileName = uniqid() . '_' . basename($_FILES['profile-picture']['name']);
    $profilePicturePath = $targetDir2 . $uniqueFileName;

    // Temporary file location
    $tempFile2 = $_FILES['profile-picture']['tmp_name'];

    // Validate and move the uploaded file
    if (is_uploaded_file($tempFile2)) {
        if (!move_uploaded_file($tempFile2, $profilePicturePath)) {
            die("Failed to upload profile picture.");
        }
    } else {
        die("Possible file upload attack detected.");
    }

    // Insert user into the database
    $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, role, profile_picture) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Database error: " . $conn->error);
    }

    $stmt->bind_param("sssss", $username, $email, $passwordHash, $role, $profilePicturePath);

    if ($stmt->execute()) {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Registration Successful</title>
            <link rel="stylesheet" href="CSS/processRegistration.css"> <!-- Include the new CSS file -->
        </head>
        <body>
            <div class="success-popup">
                <h2>Registration Successful!</h2>
                <p>Your account has been created successfully.</p>
                <p>Redirecting to login page...</p>
                <div class="loading"></div>
            </div>
            <script>
                setTimeout(() => {
                    window.location.href = 'login.php';
                }, 3000);
            </script>
        </body>
        </html>
        <?php
    } else {
        die("Error: " . $stmt->error);
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
