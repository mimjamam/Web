<?php
// Fetch voucher codes from the database
require 'dbConfig.php';
 
$vouchers = [];
$result = $conn->query("SELECT code, discount_percentage ,image FROM vouchers ORDER BY created_at DESC LIMIT 5"); // Limit to 5 latest vouchers
if ($result->num_rows > 0) {
    $vouchers = $result->fetch_all(MYSQLI_ASSOC);
}
?>
 
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Menu</title>
    <link rel="stylesheet" href="CSS/menu.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Include jQuery -->
</head>

<body>
    <header class="header">
        <div class="logo">
            <h1>Food Menu</h1>
        </div>
         <div class="voucher-info">
            <h3>Get Discount Using Voucher Code:</h3>
            <?php if (!empty($vouchers)) : ?>
                <ul>
                    <?php foreach ($vouchers as $voucher) : ?>
                        <li>
                            <strong><?php echo htmlspecialchars($voucher['code']); ?></strong> - <?php echo htmlspecialchars($voucher['discount_percentage']); ?>% Off
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <p>No vouchers available at the moment.</p>
            <?php endif; ?>
        </div> 
    </header>

    <!-- Search and Filter Section -->
    <section class="search-filter">
        <div class="search-box">
            <h3>Search Items</h3>
            <form id="search-form"> <!-- Corrected the form ID -->
                <!-- Canteen Filter -->
                <label for="canteen">Select Canteen:</label>
                <select name="canteen" id="canteen">
                    <option value="all">All Canteens</option>
                    <option value="central">Central Canteen</option>
                    <option value="northside">Northside Cafe</option>
                    <option value="south-campus">South Campus Deli</option>
                </select>

                <!-- Price Range Filter -->
                <label for="price-range">Price Range:</label>
                <input type="number" name="min-price" placeholder="Min Price" min="0">
                <input type="number" name="max-price" placeholder="Max Price" min="0">

                <!-- Item Name Search -->
                <label for="item-name">Item Name:</label>
                <input type="text" name="item-name" placeholder="Search by name">

                <!-- Item Type Filter -->
                <label for="item-type">Item Type:</label>
                <select name="item-type" id="item-type">
                    <option value="all">All Types</option>
                    <option value="snacks">Snacks</option>
                    <option value="heavy-meal">Heavy Meal</option>
                    <option value="appetizer">Appetizer</option>
                    <option value="drinks">Drinks</option>
                </select>

                <!-- Search Button -->
                <button type="submit">Search</button>
                <button type="button" id="clear-form">Clear</button>
            </form>
        </div>

        <!-- Search Results -->
        <div class="search-results">
            <h3>Search Results</h3>
            <div id="results-list"> <!-- Added ID to match AJAX selector -->
                <!-- Search results dynamically loaded here -->
            </div>
        </div>
    </section>

    <!-- Food Menu Navigation -->
    <section class="menu-navigation">
        <ul>
            <li>MENU-NAVIGATION</li>
            <li><a href="#" class="menu-link" data-type="all" data-canteen="all">All Foods</a></li>
            <li><a href="#" class="menu-link" data-type="all" data-canteen="central">Central Canteen</a></li>
            <li><a href="#" class="menu-link" data-type="all" data-canteen="northside">Northside Cafe</a></li>
            <li><a href="#" class="menu-link" data-type="all" data-canteen="south-campus">South Campus Deli</a></li>
            <li><a href="#" class="menu-link" data-type="snacks" data-canteen="all">Snacks</a></li>
            <li><a href="#" class="menu-link" data-type="heavy-meal" data-canteen="all">Heavy Meals</a></li>
            <li><a href="#" class="menu-link" data-type="appetizer" data-canteen="all">Appetizers</a></li>
            <li><a href="#" class="menu-link" data-type="drinks" data-canteen="all">Drinks</a></li>
        </ul>
    </section>

    <!-- Food Items Section -->
    <section class="menu-items">
        <div class="item-list" id="food-items">
            <!-- Dynamic Food Items will be loaded here -->
        </div>
    </section>

    <!-- Add Food Section -->
    <?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['role']) && $_SESSION['role'] == 'staff') {
    ?>
    <!-- Add Food Section -->
    <section class="admin-add-food">
        <h2>Add Food Item</h2>
        <form method="POST" action="menuprocess.php" enctype="multipart/form-data">
            <label for="canteen">Select Canteen:</label>
            <select name="canteen" id="canteen" required>
                <option value="central">Central Canteen</option>
                <option value="northside">Northside Cafe</option>
                <option value="south-campus">South Campus Deli</option>
            </select>

            <label for="item-name">Item Name:</label>
            <input type="text" name="item-name" id="item-name" required>

            <label for="item-type">Item Type:</label>
            <select name="item-type" id="item-type" required>
                <option value="snacks">Snacks</option>
                <option value="heavy-meal">Heavy Meal</option>
                <option value="appetizer">Appetizer</option>
                <option value="drinks">Drinks</option>
            </select>

            <label for="price">Price:</label>
            <input type="number" name="price" id="price" step="0.01" required>

            <label for="description">Description:</label>
            <textarea name="description" id="description" rows="3" required></textarea>

            <label for="item-image">Upload Image:</label>
            <input type="file" name="item-image" id="item-image" required>

            <label for="status" class="checkbox-label">Availability:</label>
            <input type="checkbox" name="status" id="status" class="checkbox-input">

            <button type="submit" name="addFood">Add Food</button>
        </form>
    </section>
    <section class="voucher">
        <?php include 'voucher.php'; ?>
    </section>
    <?php
    }
    ?>

    <!-- Include the new script file -->
    <script src="JS/menu.js"></script>

</body>
</html>