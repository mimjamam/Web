<?php
session_start();
require 'dbConfig.php'; // Include database configuration

// Ensure the user has the right role (staff or admin)
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'staff')) {
    die("Access denied. You do not have permission to remove items.");
}



$itemId = intval($_GET['id']);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("DELETE FROM food_items WHERE id = ?");
    $stmt->bind_param("i", $itemId);

    if ($stmt->execute()) {
        header("Location: loghome.php?success=Item+removed+successfully");
        exit();
    } else {
        die("Error removing item: " . $stmt->error);
    }
}
?>

<div class="remove-container">
    <link rel="stylesheet" href="CSS/profile-actions.css">
    <h2>Delete Item</h2>
    <p>Are you sure you want to delete this item? This action cannot be undone.</p>
    <form action="removeitem.php?id=<?php echo $itemId; ?>" method="POST">
        <button type="submit" class="btn delete-btn">Yes, Delete Item</button>
        <a href="menu.php" class="btn cancel-btn">Cancel</a>
    </form>
</div>
