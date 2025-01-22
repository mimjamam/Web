<?php
session_start();
require 'dbConfig.php'; // Include database configuration

// Check if the user is staff
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit();
}

// Handle Add Food Item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addFood'])) {
    $canteen = $_POST['canteen'];
    $itemName = $_POST['item-name'];
    $itemType = $_POST['item-type'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $status = isset($_POST['status']) ? 1 : 0;

    // Handle Image Upload
    $targetDir = "uploads/";
    // Ensure the uploads directory exists
    if (!is_dir($targetDir)) {
        if (!mkdir($targetDir, 0777, true)) {
            die("Failed to create uploads directory.");
        }
    }

    // Generate a unique file name to avoid conflicts
    $uniqueFileName = uniqid() . '_' . basename($_FILES['item-image']['name']);
    $targetFile = $targetDir . $uniqueFileName;

    // Open the temporary file for reading
    $tempFile = $_FILES['item-image']['tmp_name'];

    if (is_uploaded_file($tempFile)) {
        // Use file_put_contents to move the file
        $fileData = file_get_contents($tempFile);
        if (file_put_contents($targetFile, $fileData)) {
           
        } else {
            die("Failed to upload image using file_put_contents.");
        }
    } else {
        die("Possible file upload attack detected.");
    }

    // Insert into Database
    $stmt = $conn->prepare("INSERT INTO food_items (canteen, name, type, price, description, image, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssissi", $canteen, $itemName, $itemType, $price, $description, $targetFile, $status);
    if ($stmt->execute()) {
        echo '
        <div class="success-popup">
        <link rel="stylesheet" href="profile-actions.css">
            <p>Item updated successfully! Redirecting to menu...</p>
        </div>
        <script>
            // Redirect to menu.php after 3 seconds
            setTimeout(() => {
                window.location.href = "loghome.php";
            }, 1000);
        </script>
        ';
    } else {
        die("Failed to add food item: " . $stmt->error);
    }
}
?>
