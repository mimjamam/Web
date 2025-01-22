<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require 'db_connection.php'; // Ensure this path is correct

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_SESSION['email'];
    $otp = $_POST['otp'];
    $new_password = $_POST['new_password'];

    // Check if email and OTP are correct
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND otp_code = ?");
    $stmt->bind_param("ss", $email, $otp);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update the user's password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password_hash = ?, otp_code = NULL WHERE email = ?");
        $stmt->bind_param("ss", $hashed_password, $email);
        $stmt->execute();

        $_SESSION['message'] = "Your password has been reset successfully.";
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['error'] = "Invalid OTP code.";
    }

    header("Location: verify_otp.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - Friends Catering</title>
    <link rel="stylesheet" href="CSS/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('Untitled design.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background: rgba(255, 255, 255, 0.8); /* Semi-transparent background */
            padding: 40px;
            border-radius: 10px;
            max-width: 400px;
            margin: 110px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Verify OTP</h1>
        <?php if (isset($_SESSION['message'])): ?>
            <p style="color: green;"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></p>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <p style="color: red;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <form action="verify_otp.php" method="POST">
            <div class="form-group">
                <label for="otp">
                    <i class="fas fa-key"></i> OTP
                </label>
                <input type="text" 
                       name="otp" 
                       id="otp" 
                       required 
                       autocomplete="one-time-code"
                       placeholder="Enter OTP code">
            </div>
            <div class="form-group">
                <label for="new_password">
                    <i class="fas fa-lock"></i> New Password
                </label>
                <input type="password" 
                       name="new_password" 
                       id="new_password" 
                       required 
                       autocomplete="new-password"
                       placeholder="Enter new password">
            </div>
            <button type="submit">
                Reset Password <i class="fas fa-arrow-right"></i>
            </button>
        </form>
    </div>
</body>
</html>
