<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>profile</title>
    <link rel="stylesheet" href="CSS/profile.css">

</head>

<body>
    <?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    require 'dbConfig.php'; // Include database configuration

    // Ensure the user is logged in
    if (!isset($_SESSION['username'])) {
        die("You must be logged in to view this page.");
    }

    // Fetch the logged-in user's details from the database
    $username = $_SESSION['username'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    if (!$stmt) {
        die("Database error: " . $conn->error);
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        die("User not found.");
    }
    $stmt->close();
    ?>
    <div class="profile-container">
        <h2>Your Profile</h2>

        <div class="profile-card">
            <!-- Profile Image Section -->
            <div class="profile-image">
                <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture">
            </div>

            <!-- Profile Details Section -->
            <div class="profile-details">
                <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><strong>Role:</strong> <?php echo ucfirst(htmlspecialchars($user['role'])); ?></p>
                <p><strong>Account Created:</strong> <?php echo htmlspecialchars($user['created_at']); ?></p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="profile-actions">
            <a href="modify.php" class="btn modify-btn">Modify Profile</a>
            <a href="remove.php" class="btn remove-btn">Remove Profile</a>
        </div>
        
        <div class="breadcrumbs">
    <a href="loghome.php">Home</a> &Lt;Profile
</div>
    </div>

</body>
</html>