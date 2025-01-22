<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="CSS/registration.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        .logo img {
            width: 110px; /* Adjust the width as needed */
            height: 110px; /* Adjust the height as needed */
            border-radius: 50%; /* Make the image round */
            display: block;
            margin: 0 auto 20px; /* Center the logo and add bottom margin */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="Friends catering.gif" alt="Logo">
        </div>
        <h1>Create Account</h1>
        <form action="processRegistration.php" method="POST" enctype="multipart/form-data">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" placeholder="Enter your username" required>
            
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" placeholder="Enter your email" required>
            
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" placeholder="Enter your password" required>
            
            <label for="confirm-password">Confirm Password:</label>
            <input type="password" name="confirm-password" id="confirm-password" placeholder="Re-enter your password" required>

            <label for="role">Account Type:</label>
            <select name="role" id="role" required>
                <option value="student">Student</option>
                <option value="faculty">Faculty</option>
                <option value="staff">Staff</option>
            </select>

            <label for="profile-picture">Profile Picture:</label>
            <input type="file" name="profile-picture" id="profile-picture" accept="image/*" required>

            <button type="submit">Register</button>
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </form>
    </div>
</body>
</html>
