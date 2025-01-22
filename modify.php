<?php
session_start();
require 'dbConfig.php'; // Include database configuration

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    die("You must be logged in to modify your profile.");
}

// Fetch the logged-in user's details
$currentUsername = $_SESSION['username'];
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $currentUsername);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    die("User not found.");
}
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newUsername = trim($_POST['username']);
    $newEmail = trim($_POST['email']);
    $currentPassword = $_POST['current-password'];
    $newPassword = $_POST['new-password'];
    $profilePicturePath = $user['profile_picture'];

    // Check if the new username already exists (exclude the current user)
    $usernameCheckStmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND username != ?");
    $usernameCheckStmt->bind_param("ss", $newUsername, $currentUsername);
    $usernameCheckStmt->execute();
    $usernameCheckResult = $usernameCheckStmt->get_result();

    if ($usernameCheckResult->num_rows > 0) {
        die("The username is already in use. Please try a different one.");
    }
    $usernameCheckStmt->close();

    // Check if the new email already exists (exclude the current user)
    $emailCheckStmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND username != ?");
    $emailCheckStmt->bind_param("ss", $newEmail, $currentUsername);
    $emailCheckStmt->execute();
    $emailCheckResult = $emailCheckStmt->get_result();

    if ($emailCheckResult->num_rows > 0) {
        die("The email is already in use. Please try a different one.");
    }
    $emailCheckStmt->close();

    // Validate current password if changing password
    if (!empty($newPassword)) {
        if (!password_verify($currentPassword, $user['password_hash'])) {
            die("The current password is incorrect.");
        }
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
    } else {
        $passwordHash = $user['password_hash']; // Keep the existing password if not changing
    }

    // Handle profile picture upload
    if (!empty($_FILES['profile-picture']['name'])) {
        $targetDir = "userimages/";
        $uniqueFileName = uniqid() . '_' . basename($_FILES['profile-picture']['name']);
        $profilePicturePath = $targetDir . $uniqueFileName;

        // Move uploaded file
        if (!move_uploaded_file($_FILES['profile-picture']['tmp_name'], $profilePicturePath)) {
            die("Failed to upload new profile picture.");
        }
    }

    // Update user details
    $updateStmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password_hash = ?, profile_picture = ? WHERE username = ?");
    $updateStmt->bind_param("sssss", $newUsername, $newEmail, $passwordHash, $profilePicturePath, $currentUsername);

    if ($updateStmt->execute()) {
        $_SESSION['username'] = $newUsername; // Update session username
        header("Location: profile.php?success=Profile+updated+successfully");
        exit();
    } else {
        die("Error updating profile: " . $updateStmt->error);
    }
}

?>

<div class="modify-container">
    <link rel="stylesheet" href="CSS/profile-actions.css">
    <h2>Modify Profile</h2>
    <form action="modify.php" method="POST" enctype="multipart/form-data">
        <!-- Username -->
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

        <!-- Email -->
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

        <!-- Current Password -->
        <label for="current-password">Current Password (required to change password):</label>
        <input type="text" name="current-password" id="current-password" placeholder="Enter your current password">

        <!-- New Password -->
        <label for="new-password">New Password (leave blank to keep current password):</label>
        <input type="text" name="new-password" id="new-password" placeholder="Enter a new password">

        <!-- Profile Picture -->
        <label for="profile-picture">Profile Picture:</label>
        <input type="file" name="profile-picture" id="profile-picture">

        <?php if (!empty($user['profile_picture'])): ?>
            <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" style="max-width: 100px;">
        <?php endif; ?>

        <!-- Submit Button -->
        <button type="submit">Save Changes</button>
        
    </form>
</div>
