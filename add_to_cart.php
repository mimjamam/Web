<?php
session_start();
require 'dbConfig.php'; // Include your database connection

// Get the item ID from the AJAX request
$itemId = $_POST['id'] ?? null;

if ($itemId) {
    // Fetch the item details from the database
    $stmt = $conn->prepare("SELECT * FROM food_items WHERE id = ?");
    $stmt->bind_param("i", $itemId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $item = $result->fetch_assoc();

        // Initialize the cart if it doesn't exist
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Check if the item already exists in the cart
        if (isset($_SESSION['cart'][$itemId])) {
            $_SESSION['cart'][$itemId]['quantity'] += 1; // Increment quantity
        } else {
            // Add new item to the cart
            $_SESSION['cart'][$itemId] = [
                'name' => $item['name'],
                'price' => $item['price'],
                'image' => $item['image'], // Ensure the image URL is correct
                'quantity' => 1,
            ];
        }

        // Return success response
        echo json_encode([
            'status' => 'success',
            'message' => 'Item added to cart.',
            'cart' => $_SESSION['cart'], // Return the updated cart
        ]);
    } else {
        // Item not found
        echo json_encode([
            'status' => 'error',
            'message' => 'Item not found.',
        ]);
    }
} else {
    // Invalid request
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid item ID.',
    ]);
}
?>
