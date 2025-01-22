<?php
session_start();
require 'dbConfig.php'; // Include database configuration

// Ensure the user has the right role (staff or admin)
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'staff')) {
    die("Access denied. You do not have permission to modify items.");
}

// Check if the item ID is provided
if (!isset($_GET['id'])) {
    die("Item ID is required to modify an item.");
}

$itemId = intval($_GET['id']);

// Fetch the item's current details
$stmt = $conn->prepare("SELECT * FROM food_items WHERE id = ?");
$stmt->bind_param("i", $itemId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $item = $result->fetch_assoc();
} else {
    die("Item not found.");
}
$stmt->close();

// Initialize a success message flag
$successMessage = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemPrice = floatval($_POST['price']);
    $itemCanteen = trim($_POST['canteen']);
    $itemStatus = isset($_POST['status']) ? 1 : 0;

    // Validate inputs
    if ($itemPrice <= 0 || empty($itemCanteen)) {
        die("All fields are required.");
    }

    // Update the item in the database
    $updateStmt = $conn->prepare("UPDATE food_items SET price = ?, canteen = ?, status = ? WHERE id = ?");
    if (!$updateStmt) {
        die("Database error: " . $conn->error);
    }

    $updateStmt->bind_param("dsii", $itemPrice, $itemCanteen, $itemStatus, $itemId);

    if ($updateStmt->execute()) {
        $successMessage = true; // Set success message flag
    } else {
        die("Error updating item: " . $updateStmt->error);
    }

    // Close the prepared statement
    $updateStmt->close();

    // Redirect to loghome.php after successful update
    header("Location: loghome.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify Item</title>
    <link rel="stylesheet" href="CSS/profile-actions.css">
</head>

<body>
    <?php if ($successMessage): ?>
        <!-- Success Popup -->
        <div class="success-popup">
            <p>Item updated successfully! Redirecting to menu...</p>
        </div>
        <script>
            // Redirect to menu.php after 3 seconds
            setTimeout(() => {
                window.location.href = "loghome.php";
            }, 1000);
        </script>
    <?php endif; ?>

    <div class="modify-container">
        <h2>Modify Item</h2>
        <form action="modifyitem.php?id=<?php echo $itemId; ?>" method="POST">
            <label for="price">Price:</label>
            <input type="number" step="0.01" name="price" id="price" value="<?php echo htmlspecialchars($item['price']); ?>" required>

            <label for="canteen">Canteen:</label>
            <input type="text" name="canteen" id="canteen" value="<?php echo htmlspecialchars($item['canteen']); ?>" required>

            <label for="status">Available:</label>
            <input type="checkbox" name="status" id="status" <?php echo $item['Status'] ? 'checked' : ''; ?>>

            <button type="submit">Save Changes</button>
        </form>
    </div>
     
</body>

</html>
