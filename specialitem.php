<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'dbConfig.php'; // Include database connection

// Check if the user is logged in as staff
$isStaff = isset($_SESSION['role']) && $_SESSION['role'] === 'staff';

// Handle remove request if the 'remove' button is clicked
if (isset($_GET['remove_id']) && $isStaff) {
    $id = $_GET['remove_id'];

    // Prepare the DELETE query
    $stmt = $conn->prepare("DELETE FROM special_items WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Item removed successfully!";
    } else {
        $_SESSION['message'] = "Failed to remove the item.";
    }

    // Redirect to avoid reprocessing the form on refresh
    header("Location: specialitem.php");
    exit;
}

// Fetch all special items
$result = $conn->query("SELECT * FROM special_items ORDER BY id DESC");
$specialItems = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Special Items</title>
    <link rel="stylesheet" href="CSS/special-items.css">
</head>
<body>
    <section id="special-items-content">
        <h2>Special Items</h2>
        <p>Discover the unique and popular items available at our canteen, crafted to satisfy every craving!</p>

        <!-- Display Success or Error Message -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="popup">
                <p><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></p>
            </div>
            <script>
                setTimeout(() => document.querySelector('.popup').style.display = 'none', 2000);
            </script>
        <?php endif; ?>

        <!-- Display Special Items -->
        <?php if (!empty($specialItems)): ?>
            <?php foreach ($specialItems as $item): ?>
                <div class="special-item">
                    <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="special-item-image">
                    <div class="special-item-details">
                        <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                        <p><strong>Price:</strong> $<?php echo number_format($item['price'], 2); ?></p>
                        <p><?php echo htmlspecialchars($item['description']); ?></p>
                        <!-- Remove Button for Staff -->
                        <?php if ($isStaff): ?>
                            <a href="specialitem.php?remove_id=<?php echo $item['id']; ?>" class="btn remove-item">Remove</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No special items available yet.</p>
        <?php endif; ?>
    </section>
</body>
</html>
