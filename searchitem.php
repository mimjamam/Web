<?php
session_start();
require 'dbConfig.php'; // Include database configuration

// Initialize query
$query = "SELECT * FROM food_items WHERE status = 1"; // Only show available items

// Bind parameters dynamically
$bindParams = [];
$bindTypes = "";

// Handle Canteen Filter
if (isset($_GET['canteen']) && $_GET['canteen'] !== 'all') {
    $canteen = filter_var($_GET['canteen'], FILTER_SANITIZE_STRING);
    $query .= " AND canteen = ?";
    $bindParams[] = $canteen;
    $bindTypes .= "s";
}

// Handle Price Range Filter
if (!empty($_GET['min-price']) && !empty($_GET['max-price'])) {
    $minPrice = filter_var($_GET['min-price'], FILTER_VALIDATE_FLOAT);
    $maxPrice = filter_var($_GET['max-price'], FILTER_VALIDATE_FLOAT);
    $query .= " AND price BETWEEN ? AND ?";
    $bindParams[] = $minPrice;
    $bindParams[] = $maxPrice;
    $bindTypes .= "dd";
} elseif (!empty($_GET['min-price'])) {
    $minPrice = filter_var($_GET['min-price'], FILTER_VALIDATE_FLOAT);
    $query .= " AND price >= ?";
    $bindParams[] = $minPrice;
    $bindTypes .= "d";
} elseif (!empty($_GET['max-price'])) {
    $maxPrice = filter_var($_GET['max-price'], FILTER_VALIDATE_FLOAT);
    $query .= " AND price <= ?";
    $bindParams[] = $maxPrice;
    $bindTypes .= "d";
}

// Handle Item Name Search
if (!empty($_GET['item-name'])) {
    $itemName = '%' . filter_var($_GET['item-name'], FILTER_SANITIZE_STRING) . '%';
    $query .= " AND name LIKE ?";
    $bindParams[] = $itemName;
    $bindTypes .= "s";
}

// Handle Item Type Filter
if (isset($_GET['item-type']) && $_GET['item-type'] !== 'all') {
    $itemType = filter_var($_GET['item-type'], FILTER_SANITIZE_STRING);
    $query .= " AND type = ?";
    $bindParams[] = $itemType;
    $bindTypes .= "s";
}

// Prepare and execute the query
$stmt = $conn->prepare($query);
if (!empty($bindParams)) {
    $stmt->bind_param($bindTypes, ...$bindParams);
}
$stmt->execute();
$result = $stmt->get_result();

// Display results
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<div class="result-item">';
        echo '<img src="' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['name']) . '">';
        echo '<div class="details">';
        echo '<h4>' . htmlspecialchars($row['name']) . '</h4>';
        echo '<p>Price: $' . htmlspecialchars($row['price']) . '</p>';
        echo '<p>Canteen: ' . htmlspecialchars($row['canteen']) . '</p>';
        echo '<p>' . htmlspecialchars($row['description']) . '</p>';
        
        // Show buttons based on role
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'staff') {
            echo '<a href="modifyitem.php?id=' . $row['id'] . '" class="btn modify-item">Modify</a>';
            echo '<a href="removeitem.php?id=' . $row['id'] . '" class="btn remove-item">Remove</a>';
            echo '<a href="add_to_special.php?id=' . $row['id'] . '" class="btn add-to-special">Add to Special Items</a>';
        } else {
            echo '<a href="#" data-id="' . $row['id'] . '" class="btn add-to-cart">Add to Cart</a>';
        }

        echo '</div>';
        echo '</div>';
    }
} else {
    echo '<p>No results found.</p>';
}
?>

