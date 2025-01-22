<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'dbConfig.php'; // Include your database connection

// Handle form submission to add a voucher
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addVoucher'])) {
    $voucherCode = $_POST['voucherCode'];
    $discountPercentage = $_POST['discountPercentage'];

    if (!empty($voucherCode) && !empty($discountPercentage) && $discountPercentage > 0 && $discountPercentage <= 100) {
        // Insert voucher into the database
        $stmt = $conn->prepare("INSERT INTO vouchers (code, discount_percentage) VALUES (?, ?)");
        $stmt->bind_param("si", $voucherCode, $discountPercentage);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Voucher added successfully!";
        } else {
            $_SESSION['message'] = "Failed to add voucher. The code might already exist.";
        }
    } else {
        $_SESSION['message'] = "Invalid input. Please enter valid data.";
    }
    header("Location: loghome.php");
    exit;
}

// Handle form submission to remove a voucher
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['removeVoucher'])) {
    $voucherId = $_POST['voucherId'];

    if (!empty($voucherId)) {
        // Delete voucher from the database
        $stmt = $conn->prepare("DELETE FROM vouchers WHERE id = ?");
        $stmt->bind_param("i", $voucherId);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Voucher removed successfully!";
        } else {
            $_SESSION['message'] = "Failed to remove voucher.";
        }
    } else {
        $_SESSION['message'] = "Invalid input. Please select a voucher to remove.";
    }
    header("Location: loghome.php");
    exit;
}

// Fetch all vouchers for display
$vouchers = [];
$result = $conn->query("SELECT * FROM vouchers ORDER BY created_at DESC");
if ($result->num_rows > 0) {
    $vouchers = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voucher Management</title>
    <link rel="stylesheet" href="CSS/voucher.css">
</head>
<body>
    <h1>Voucher Management</h1>

    <!-- Display Session Messages -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="message">
            <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <!-- Add Voucher Form -->
    <form action="voucher.php" method="POST">
        <h2>Add Voucher</h2>
        <label for="voucherCode">Voucher Code:</label>
        <input type="text" id="voucherCode" name="voucherCode" required>
        
        <label for="discountPercentage">Discount Percentage:</label>
        <input type="number" id="discountPercentage" name="discountPercentage" min="1" max="100" required>
        
        <button type="submit" name="addVoucher">Add Voucher</button>
    </form>

    <!-- Remove Voucher Form -->
    <form action="voucher.php" method="POST">
        <h2>Remove Voucher</h2>
        <label for="voucherId">Select Voucher:</label>
        <select id="voucherId" name="voucherId" required>
            <option value="">-- Select Voucher --</option>
            <?php foreach ($vouchers as $voucher): ?>
                <option value="<?php echo $voucher['id']; ?>">
                    <?php echo htmlspecialchars($voucher['code']) . " (" . $voucher['discount_percentage'] . "%)"; ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <button type="submit" name="removeVoucher">Remove Voucher</button>
    </form>

    <!-- Display Existing Vouchers -->
    <h2>Existing Vouchers</h2>
    <?php if (!empty($vouchers)): ?>
        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Discount (%)</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vouchers as $voucher): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($voucher['code']); ?></td>
                        <td><?php echo htmlspecialchars($voucher['discount_percentage']); ?>%</td>
                        <td><?php echo htmlspecialchars($voucher['created_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No vouchers found.</p>
    <?php endif; ?>
</body>
</html>
