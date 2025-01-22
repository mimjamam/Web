<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require 'dbConfig.php'; // Ensure this path is correct

// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Update the path to point to the vendor directory in your project
require __DIR__ . '/vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // Check if email exists in the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Generate a random OTP code
        $otp = rand(100000, 999999);

        // Store the OTP in the database
        $stmt = $conn->prepare("UPDATE users SET otp_code = ? WHERE email = ?");
        $stmt->bind_param("ss", $otp, $email);
        $stmt->execute();

        // Send OTP to the user's email using PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'universitycanteen1@gmail.com';
            $mail->Password = 'hiwq iuuq wmrn wzys'; // Replace with your 16-character app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            // Update sender to match Gmail address
            $mail->setFrom('universitycanteen1@gmail.com', 'Friends Catering');

            // Recipients
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'OTP for Password Reset';
            $mail->Body = "Your OTP code is: $otp";

            $mail->send();
            $_SESSION['message'] = "An OTP code has been sent to your email.";
            $_SESSION['email'] = $email;
            header("Location: verify_otp.php");
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = "Failed to send the OTP code. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $_SESSION['error'] = "No account found with that email address.";
    }

    header("Location: forgot_password.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Friends Catering</title>
    <link rel="stylesheet" href="CSS/forgot_password.css">
    <link rel="stylesheet" href="CSS/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
    </style>
</head>
<body>
    <div class="container">
        <h1>Forgot Password</h1>
        <?php if (isset($_SESSION['message'])): ?>
            <p style="color: green;"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></p>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <p style="color: red;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <form action="forgot_password.php" method="POST">
            <div class="form-group">
                <label for="email">
                    <i class="fas fa-envelope"></i> Email
                </label>
                <input type="email" 
                       name="email" 
                       id="email" 
                       required 
                       autocomplete="email"
                       placeholder="Enter your email">
            </div>
            <button type="submit">
                Send OTP <i class="fas fa-arrow-right"></i>
            </button>
            <p style="margin-top: 2rem; color: #666;">
                Remembered your password? 
                <a href="login.php">Login here</a>
            </p>
        </form>
    </div>
</body>
</html>
