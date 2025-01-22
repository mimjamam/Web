<?php
session_start();
require 'dbConfig.php'; // Include database configuration

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    die("You must be logged in to delete your profile.");
}

// Fetch the logged-in user's details
$username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Delete user from database
    $stmt = $conn->prepare("DELETE FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);

    if ($stmt->execute()) {
        session_destroy(); // Log out the user
        header("Location: register.php?success=Account+deleted+successfully");
        exit();
    } else {
        die("Error deleting account: " . $stmt->error);
    }
}
?>

<div class="remove-container">
    <link rel="stylesheet" href="CSS/profile-actions.css">
    <h2>Delete Profile</h2>
    <p>Are you sure you want to delete your profile? This action cannot be undone.</p>
    <form action="remove.php" method="POST">
        <button type="submit">Yes, Delete My Profile</button>
        <a href="profile.php">Cancel</a>
    </form>
</div>
