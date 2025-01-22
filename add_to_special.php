<?php
session_start();
require 'dbConfig.php'; // Include database connection

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the item details from the food_items table
    $stmt = $conn->prepare("SELECT name, price, description, image FROM food_items WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $item = $result->fetch_assoc();

        // Check if the item already exists in the special_items table
        $checkStmt = $conn->prepare("SELECT id FROM special_items WHERE name = ? AND price = ?");
        $checkStmt->bind_param("sd", $item['name'], $item['price']);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            // Item already exists
            $_SESSION['message'] = "This item is already added as a special item.";
        } else {
            // Insert into special_items table
            $insertStmt = $conn->prepare("INSERT INTO special_items (name, price, description, image) VALUES (?, ?, ?, ?)");
            $insertStmt->bind_param("sdss", $item['name'], $item['price'], $item['description'], $item['image']);

            if ($insertStmt->execute()) {
                $_SESSION['message'] = "Item added to special items successfully!";
            } else {
                $_SESSION['message'] = "Failed to add item to special items.";
            }
        }
    } else {
        $_SESSION['message'] = "Item not found.";
    }
} else {
    $_SESSION['message'] = "Invalid request.";
}

if (isset($_SESSION['message'])): ?>
    <div class="success-popup">
    <link rel="stylesheet" href="CSS/profile-actions.css">
        <p><?php echo $_SESSION['message']; unset($_SESSION['message']); ?> Redirecting to menu...</p>
    </div>
    <script>
        // Redirect to menu.php after 3 seconds
        setTimeout(() => {
            window.location.href = "loghome.php";
        }, 1000);
    </script>
<?php endif;
exit;
?>
