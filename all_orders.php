<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'dbConfig.php';

// Ensure user is staff
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'staff') {
    die("Access denied");
}

// Fetch all orders from the database
$query = "SELECT i.*, u.username, u.email,u.role
          FROM invoice i 
          JOIN users u ON i.username = u.username 
          ORDER BY i.timestamp DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Orders</title>
    <link rel="stylesheet" href="CSS/all_orders.css">
</head>
<body>
    <div class="all-orders-container">
        <h2>All Orders</h2>
        <table class="orders-table">
            <thead>
                <tr>
                    <th>Order Number</th>
                    <th>Customer</th>
                    <Th>Role</th>
                    <th>Email</th>
                    <th>Subtotal</th>
                    <th>Discount</th>
                    <th>Final Total</th>
                    <th>Room Number</th>
                    <th>Date & Time</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($order = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                        <td><?php echo htmlspecialchars($order['username']); ?></td>
                        <td><?php echo htmlspecialchars($order['role']); ?></td>
                        <td><?php echo htmlspecialchars($order['email']); ?></td>
                        <td>$<?php echo number_format($order['subtotal'], 2); ?></td>
                        <td>$<?php echo number_format($order['discount'], 2); ?></td>
                        <td>$<?php echo number_format($order['final_total'], 2); ?></td>
                        <td><?php echo htmlspecialchars($order['room_number'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($order['timestamp']); ?></td>
                        <td>
                            <a href="Invoicee.php?order_number=<?php echo urlencode($order['order_number']); ?>" 
                               class="view-details-btn" target="_blank">
                                View Details
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
