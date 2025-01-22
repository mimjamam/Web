<?php
//change
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'dbConfig.php'; // Include database connection

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username']; // Fetch username from session
$role = $_SESSION['role'] ?? 'guest'; // Fetch user role from session (default to guest)

// Fetch all orders placed by the user
$stmt = $conn->prepare("SELECT id, order_number, items, subtotal, discount, final_total, timestamp, room_number FROM invoice WHERE username = ? ORDER BY timestamp DESC");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);

// Separate the most recent order as the current order
$currentOrder = $orders[0] ?? null; // Fetch the first (latest) order
$orderHistory = array_slice($orders, 1); // Remaining orders
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <link rel="stylesheet" href="CSS/order.css"> <!-- Include the new CSS file -->
</head>
<body>
    <h2>My Orders</h2>

    <!-- Current Order Section -->
    <?php if ($currentOrder): ?>
        <div class="current-order">
            <h2>Current Order</h2>
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Price</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $items = json_decode($currentOrder['items'], true);
                    foreach ($items as $item):
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td colspan="2" style="text-align: right;"><strong>Total Discount:</strong></td>
                        <td><strong>$<?php echo number_format($currentOrder['discount'], 2); ?></strong></td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="2" style="text-align: right;"><strong>Final Total:</strong></td>
                        <td><strong>$<?php echo number_format($currentOrder['final_total'], 2); ?></strong></td>
                    </tr>
                </tbody>
            </table>
            <a href="Invoicee.php?order_number=<?php echo urlencode($currentOrder['order_number']); ?>" target="_blank" class="print-btn">Print Invoice</a>
        </div>
    <?php else: ?>
        <p>You have not placed any orders yet.</p>
    <?php endif; ?>

    <!-- Order History Section -->
    <?php if (!empty($orderHistory)): ?>
        <div class="order-history">
            <h2>Order History</h2>
            <table>
                <thead>
                    <tr>
                        <th>Order Number</th>
                        <th>Subtotal</th>
                        <th>Discount</th>
                        <th>Final Total</th>
                        <th>Date & Time</th>
                        <?php if ($role === 'faculty'): ?>
                            <th>Room Number</th>
                        <?php endif; ?>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orderHistory as $order): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                            <td>$<?php echo number_format($order['subtotal'], 2); ?></td>
                            <td>$<?php echo number_format($order['discount'], 2); ?></td>
                            <td>$<?php echo number_format($order['final_total'], 2); ?></td>
                            <td><?php echo htmlspecialchars($order['timestamp']); ?></td>
                            <?php if ($role === 'faculty'): ?>
                                <td><?php echo htmlspecialchars($order['room_number'] ?? 'N/A'); ?></td>
                            <?php endif; ?>
                            <td>
                                <a href="Invoicee.php?order_number=<?php echo urlencode($order['order_number']); ?>" target="_blank" class="print-btn">Print Invoice</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</body>
</html>
