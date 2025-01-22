<?php
session_start();
require 'dbConfig.php'; // Include database connection

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username']; // Fetch username from the session

// Fetch cart data from session
$cart = $_SESSION['cart'] ?? [];

// Calculate totals
$subtotal = 0;
foreach ($cart as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

$discountPercentage = $_SESSION['voucher'] ?? 0; // Fetch voucher discount from session
$discountAmount = $subtotal * ($discountPercentage / 100);
$finalTotal = $subtotal - $discountAmount;

// Get current date and time in Dhaka time
date_default_timezone_set('Asia/Dhaka'); // Set timezone to Dhaka
$currentDateTime = date('Y-m-d H:i:s'); // Current date and time
$pickupTime = date('H:i:s', strtotime('+20 minutes')); // Estimated pickup time

// Generate a unique order number
$orderNumber = uniqid('ORD-');

// Get room number from session if it exists
$roomNumber = $_SESSION['room_number'] ?? '';

// Validate room number
if (!empty($roomNumber) && !is_numeric($roomNumber)) {
    echo '
    <div class="error-popup">
        <link rel="stylesheet" href="profile-actions.css">
        <p>Invalid room number. Please enter a valid numeric room number.</p>
    </div>
    <script>
        setTimeout(() => {
            window.location.href = "loghome.php";
        }, 3000);
    </script>
    ';
    exit;
}

// Clear the room number from session after using it
unset($_SESSION['room_number']);

// Save invoice to database
if (!empty($cart)) {
    $items = json_encode($cart); // Convert cart items to JSON for storage

    // Add room_number to the `invoice` table
    $stmt = $conn->prepare("INSERT INTO invoice (username, order_number, items, subtotal, discount, final_total, timestamp, room_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssdddss", $username, $orderNumber, $items, $subtotal, $discountAmount, $finalTotal, $currentDateTime, $roomNumber);

    if ($stmt->execute()) {
        // Order placed successfully
        echo '
        <div class="success-popup">
            <link rel="stylesheet" href="profile-actions.css">
            <p>Order successfully placed! Redirecting to menu...</p>
        </div>
        <script>
            setTimeout(() => {
                window.location.href = "loghome.php";
            }, 3000);
        </script>
        ';
        // Clear the cart and voucher after placing the order
        unset($_SESSION['cart']);
        unset($_SESSION['voucher']);
    } else {
        // Debugging: Log SQL error
        error_log("Database Error: " . $stmt->error);

        // Handle database insertion failure
        echo '
        <div class="error-popup">
            <link rel="stylesheet" href="profile-actions.css">
            <p>Failed to place the order. Please try again later.</p>
        </div>
        <script>
            setTimeout(() => {
                window.location.href = "loghome.php";
            }, 3000);
        </script>
        ';
    }
} else {
    // Handle empty cart
    echo '
    <div class="error-popup">
        <link rel="stylesheet" href="profile-actions.css">
        <p>Your cart is empty. Add items before placing an order!</p>
    </div>
    <script>
        setTimeout(() => {
            window.location.href = "loghome.php";
        }, 3000);
    </script>
    ';
}
?>
