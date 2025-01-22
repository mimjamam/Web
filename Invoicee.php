<?php
session_start();
require 'dbConfig.php'; // Include database connection

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$orderNumber = $_GET['order_number'] ?? null;

if (!$orderNumber) {
    die("Order number not provided.");
}

// Fetch order details
$stmt = $conn->prepare("SELECT * FROM invoice WHERE order_number = ?");
$stmt->bind_param("s", $orderNumber);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Order not found.");
}

$order = $result->fetch_assoc();
$items = json_decode($order['items'], true); // Decode JSON items

// Dhaka time
date_default_timezone_set('Asia/Dhaka');
$currentDateTime = date('Y-m-d H:i:s');
$pickupTime = date('H:i:s', strtotime($order['timestamp'] . ' +20 minutes'));
$DeliveryTime = date('H:i:s', strtotime($order['timestamp'] . ' +30 minutes'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <link rel="stylesheet" href="CSS/invoice.css">
</head>
<body>
    <h1>Invoice</h1>
    <p><strong>Order Number:</strong> <?php echo htmlspecialchars($order['order_number']); ?></p>
    <p><strong>Username:</strong> <?php echo htmlspecialchars($order['username']); ?></p>
    <p><strong>Date & Time:</strong> <?php echo htmlspecialchars($order['timestamp']); ?></p>
   

    <!-- Room Number Display (if exists) -->
<?php if (!empty($order['room_number'])): ?>
    <p><strong>Estimated Delivery Time:</strong> <?php echo $DeliveryTime; ?></p>
    <p><strong>Delivery Room Number:</strong> <?php echo htmlspecialchars($order['room_number']); ?></p>
<?php else: ?>
    <p><strong>Estimated Pickup Time:</strong> <?php echo $DeliveryTime; ?></p>
<?php endif; ?>


    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="summary">
        <p><strong>Subtotal:</strong> $<?php echo number_format($order['subtotal'], 2); ?></p>
        <p><strong>Discount:</strong> $<?php echo number_format($order['discount'], 2); ?></p>
        <p><strong>Total:</strong> $<?php echo number_format($order['final_total'], 2); ?></p>
    </div>
    <div class="breadcrumbs">
    <a href="loghome.php">Home</a> &Lt;Invoice
</div>
    <script>
        window.onload = function () {
            window.print();
        };
    </script>
</body>
</html>
