<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify Item</title>
    <link rel="stylesheet" href="CSS/profile-actions.css">
</head>

<body>
    <?php
    session_start();
    require 'dbConfig.php';

    $role = $_SESSION['role'] ?? 'guest';
    $type = $_GET['type'] ?? 'all';
    $canteen = $_GET['canteen'] ?? 'all';

    // Build the SQL query based on role
    $query = "SELECT * FROM food_items";

    // Only add status condition for non-staff users
    if ($role !== 'staff') {
        $query .= " WHERE Status = 1";
    } else {
        $query .= " WHERE 1=1"; // Always true condition for staff to maintain query structure
    }

    // Add type filter
    if ($type !== 'all') {
        $query .= " AND type = '" . $conn->real_escape_string($type) . "'";
    }

    // Add canteen filter
    if ($canteen !== 'all') {
        $query .= " AND canteen = '" . $conn->real_escape_string($canteen) . "'";
    }

    // Execute the query
    $result = $conn->query($query);

    // Display results
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="item">';
            echo '<img src="uploads/' . htmlspecialchars(basename($row['image'])) . '" alt="' . htmlspecialchars($row['name']) . '">';
            echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
            echo '<p>Price: $' . htmlspecialchars($row['price']) . '</p>';
            echo '<p>' . htmlspecialchars($row['description']) . '</p>';
            
            // Show availability status for staff
            if ($role === 'staff') {
                echo '<p class="availability">Status: ' . ($row['Status'] ? 'Available' : 'Not Available') . '</p>';
            }
            
            // Show appropriate buttons based on role
            if ($role === 'staff') {
                echo '<a href="modifyitem.php?id=' . $row['id'] . '" class="btn modify-item">Modify</a>';
                echo '<a href="removeitem.php?id=' . $row['id'] . '" class="btn remove-item">Remove</a>';
                echo '<a href="add_to_special.php?id=' . $row['id'] . '" class="btn add-to-special">Add to Special Items</a>';
            } else {
                echo '<a href="#" data-id="' . $row['id'] . '" class="btn add-to-cart">Add to Cart</a>';
            }

            echo '</div>';
        }
    } else {
        echo '<p>No food items found.</p>';
    }
    ?>
    
</body>

</html>