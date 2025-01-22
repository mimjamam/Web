<?php
session_start();
if(!isset($_SESSION['role'])){
    header("refresh: 0  ; url = login.php");
    exit();
}


// Mock session role for demonstration
$role = $_SESSION['role'] ?? 'guest'; // Default to 'guest' if not logged in
$cart = $_SESSION['cart'] ?? []; // Retrieve cart data from the session
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Canteen</title>
    <link rel="stylesheet" href="CSS/home.css">
    <link rel="stylesheet" href="CSS/loghome.css">
    <!-- Canteen Section CSS -->
    <link rel="stylesheet" href="CSS/canteen.css">
    <link rel="stylesheet" href="CSS/profile.css">
    <style>
        /* Add basic styling for sections */
        .content-section {
            display: none;
            /* Hide all sections by default */
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-top: 20px;
        }

        .content-section.active {
            display: block;
            /* Show the active section */
        }
    </style>
</head>

<body>
    <!-- Navigation Bar -->
    <header class="header">
        <div class="logo">
            <h1>University Canteen</h1>
        </div>
        <nav class="navbar">
            <ul>


                <!-- Role-Specific Links -->
                <li><a href="#" onclick="showSection('menu')">Food Menu</a></li>
                <li><a href="#" onclick="showSection('Profile')">Profile</a></li>

                <?php if ($role === 'staff'): ?>
                    <li><a href="#" onclick="showSection('allOrders')">All Orders</a></li>
                <?php endif; ?>

                <?php if ($role !== 'staff' && $role !== 'guest'): ?>
                    <li><a href="cart.php" onclick="showSection('cart')">Cart</a></li>
                    <li><a href="#" onclick="showSection('Orders')">Orders</a></li>
                <?php endif; ?>

                <?php if ($role !== 'guest'): ?>
                    <li><a href="#" onclick="showSection('canteens')">Canteens</a></li>
                    <li><a href="#" onclick="showSection('specialItems')">Special Items</a></li>
                    <li><a href="logout.php" class="btn">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="btn">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <!-- Content Sections -->
    <main>

        <!-- Food Menu Section -->
        <section id="menu" class="content-section active">
            <?php include 'menu.php'; ?>
        </section>
        <section id="Profile" class="content-section">
            <link rel="stylesheet" href="CSS/profile.css">
            <?php include 'profile.php'; ?>
        </section>
        <!-- Cart Section -->
        <section id="cart" class="content-section">
            <h2>Your Cart</h2>
            <div id="cart-container">

            </div>
        </section>
    <!-- OrdersSection -->
        <section id="Orders" class="content-section">
            <?php include 'order.php'; ?>
        </section>
        <section id="canteens" class="content-section ">
            <?php include 'canteen.php'; ?>
        </section>
        <section id="specialItems" class="content-section">
            <?php include 'specialitem.php'; ?>
        </section>

        <!-- Add this new section for staff -->
        <?php if ($role === 'staff'): ?>
            <section id="allOrders" class="content-section">
                <?php include 'all_orders.php'; ?>
            </section>
        <?php endif; ?>

        <section id="about" class="content-section">
            <h2>About Us</h2>
            <p>Learn more about our mission to provide delicious meals to our students and staff.</p>
        </section>

        <section id="speciality" class="content-section">
            <h2>Our Speciality</h2>
            <p>We take pride in offering fresh, locally sourced meals with a variety of options.</p>
        </section>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2025 University Canteen. All Rights Reserved.</p>
    </footer>

    <script src="JS/loghome.js"></script>
</body>

</html>