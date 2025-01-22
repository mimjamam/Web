<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Canteen</title>
    <link rel="stylesheet" href="CSS/home.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <!-- Navigation Bar -->
    <header class="header">
        <div class="logo">
            <h1>🍽️ University Canteen</h1>
        </div>
        <nav class="navbar">
            <ul>
                <li><a href="canteen.php">Canteens</a></li>
                <li><a href="specialitem.php">Special Items</a></li>

                <li><a href="login.php" class="btn">Login</a></li>
            </ul>
        </nav>
    </header>

    <!-- Cover Section -->
    <section class="cover">
        <div class="slider" id="cover-slider">
            <img src="images/cover1.jpg" alt="Cover 1" class="slide">
            <img src="images/cover2.jpg" alt="Cover 2" class="slide">
            <img src="images/cover3.jpg" alt="Cover 3" class="slide">
        </div>
        <div class="cover-text">
            <h2>Welcome to the University Canteen</h2>
            <p>Order your favorite meals online and pick them up at your convenience!</p>
            <a href="registration.php" class="btn">Start Ordering Now</a>
        </div>
    </section>

    <!-- Canteens and Special Items Section -->
    <section class="canteen-special">
        <!-- Canteens Slider -->
        <div class="canteens" id="canteens-slider">
            <h2>Our Canteens</h2>
            <div class="slider">
                <img src="images/canteen1.jpg" alt="Canteen 1" class="slide">
                <img src="images/canteen2.jpg" alt="Canteen 2" class="slide">
                <img src="images/canteen3.jpg" alt="Canteen 3" class="slide">
            </div>
        </div>

        <!-- Special Items Slider -->
        <?php
        require 'dbConfig.php'; // Include database connection

        // Fetch all special item images from the database
        $stmt = $conn->prepare("SELECT image FROM special_items ORDER BY id DESC");
        $stmt->execute();
        $result = $stmt->get_result();
        $specialItems = $result->fetch_all(MYSQLI_ASSOC);
        ?>

        <div class="special-items" id="special-items-slider">
            <h2>Special Items</h2>
            <div class="slider">
                <?php if (!empty($specialItems)): ?>
                    <?php foreach ($specialItems as $item): ?>
                        <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="Special Item" class="slide">
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No special items available.</p>
                <?php endif; ?>
            </div>
        </div>

    </section>

    <!-- About Us and Our Speciality Section -->
    <section class="info">
        <div class="about-us" id="about">
            <h2>About Us</h2>
            <p>Our university canteen is committed to providing delicious and affordable meals to students, faculty, and staff. We offer a wide variety of options catering to all tastes and dietary preferences.</p>
        </div>
        <div class="speciality" id="speciality">
            <h2>Our Speciality</h2>
            <p>We pride ourselves on using fresh, locally-sourced ingredients to prepare meals that satisfy every craving. From quick snacks to wholesome meals, we’ve got you covered!</p>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2025 University Canteen. All Rights Reserved.</p>
    </footer>

    <script src="JS/home.js"></script>
</body>

</html>