<?php
session_start();
require 'dbConfig.php'; // Include database connection

// Check if voucher code is provided
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['code'])) {
    $voucherCode = $_POST['code'];

    // Fetch voucher details from the database
    $stmt = $conn->prepare("SELECT discount_percentage FROM vouchers WHERE code = ?");
    $stmt->bind_param("s", $voucherCode);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $voucher = $result->fetch_assoc();
        // Save discount percentage in the session
        $_SESSION['voucher'] = $voucher['discount_percentage'];
        echo json_encode([
            'status' => 'success',
            'discount' => $voucher['discount_percentage']
            
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid voucher code.'
        ]);
    }
    exit;
}

// Invalid request
echo json_encode([
    'status' => 'error',
    'message' => 'Invalid request.'
]);
exit;
?>
