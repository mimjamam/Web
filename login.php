<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Friends Catering</title>
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

        .logo img {
            width: 110px; /* Adjust the width as needed */
            height: 110px; /* Adjust the height as needed */
            border-radius: 50%; /* Make the image round */
            display: block;
            margin: 0 auto 20px; /* Center the logo and add bottom margin */
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
        <div class="logo">
            <img src="Friends catering.gif" alt="Friends Catering Logo">
        </div>
        <h1>Welcome Back</h1>
        <?php
        if (isset($_GET['error'])) {
            echo "<p style='color: red;'>" . htmlspecialchars($_GET['error']) . "</p>";
        }
        ?>
        <form action="processLogin.php" method="POST">
            <div class="form-group">
                <label for="username">
                    <i class="fas fa-user"></i> Username or Email
                </label>
                <input type="text" 
                       name="username" 
                       id="username" 
                       required 
                       autocomplete="username"
                       placeholder="Enter your username or email">
            </div>
            
            <div class="form-group">
                <label for="password">
                    <i class="fas fa-lock"></i> Password
                </label>
                <input type="password" 
                       name="password" 
                       id="password" 
                       required 
                       autocomplete="current-password"
                       placeholder="Enter your password">
            </div>
            
            <button type="submit">
                Sign In <i class="fas fa-arrow-right"></i>
            </button>
            
            <p style="margin-top: 2rem; color: #666;">
                Don't have an account? 
                <a href="registration.php">Register here</a>
            </p>
            <p style="margin-top: 1rem; font-size: 0.9rem;">
                <a href="forgot_password.php" style="color: #666;">Forgot Password?</a>
            </p>
            <p style="margin-top: 1rem; font-size: 0.9rem;">
                <a href="home.php" style="color: #666;">Back to Home</a>
            </p>
        </form>
    </div>
</body>
</html>
