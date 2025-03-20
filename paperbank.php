<?php
session_start(); // Start the session

// Check if user is logged in
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null; // Safely check if user_id is set
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paperlords</title>
    <link rel="stylesheet" href="style.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alice&family=Averia+Serif+Libre:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&family=Modak&display=swap" rel="stylesheet">
</head>

<body>
    <!-- Header -->
    <header id = "main-header" class="d-flex justify-content-between align-items-center p-3 bg-dark text-white">
        <img src="images/logo.png" alt="Paperlords Logo" height="50">
        <nav class="navbar navbar-expand-md navbar-dark">
            <div class="navbar-nav">
                <a class="nav-item nav-link text-white mx-2" href="index.php">Home</a>
                <a class="nav-item nav-link text-white mx-2" href="paperbank.php">Paper Bank</a>
                <a class="nav-item nav-link text-white mx-2" href="about.php">About</a>
                <a class="nav-item nav-link text-white mx-2" href="all-products.php">All Products</a>
                <button class="btn btn-warning mx-2">Your Cart</button>
                <button class="btn btn-warning mx-2" onclick="location.href='login.html'">Login</button> <!-- Admin Login Button -->
                <?php if (isset($_SESSION['user_id'])): ?> <!-- Check if user is logged in -->
                    <button class="btn btn-warning mx-2" onclick="location.href='user_panel.php'">My Account</button> <!-- My Account Button -->
                    <button class="btn btn-danger mx-2" onclick="location.href='logout.php'">Logout</button> <!-- Logout Button -->
                <?php endif; ?>
            </div>
        </nav>
    </header>


    <!-- Paper Bank Page -->
    <section class="hero-section">

    </section>

            <!-- Services -->
    <div class="services text-center my-4">
        <h2>Explore The Recent Past Papers and Find your Academic Books!</h2>
    </div>
        

    <div class="paper-sections">
        <!-- Edexcel IGCSE Papers Section -->
        <div class="paper-block">
            <div class="paper-image">
                <img src="images/igcse.png" alt="Edexcel IGCSE Papers">
            </div>
            <div class="paper-content">
                <h2>Edexcel IGCSE Papers</h2>
                <p>Access a wide range of past question papers for Edexcel IGCSE subjects to prepare effectively for your exams.</p>
                <a href="papers.php" class="btn btn-primary">Explore Now</a>
            </div>
        </div>

        <!-- Edexcel IAL Papers Section -->
        <div class="paper-block">
            <div class="paper-image">
                <img src="images/ial.png" alt="Edexcel IAL Papers">
            </div>
            <div class="paper-content">
                <h2>Edexcel IAL Papers</h2>
                <p>Find the most recent and comprehensive Edexcel IAL past papers for your academic needs.</p>
                <a href="papers.php" class="btn btn-primary">Explore Now</a>
            </div>
        </div>

    </div>
















            <!-- Footer -->
    <footer id="main-footer"; class="text-center bg-dark text-white p-3">
                <a class="text-warning mx-2" href="#">Home</a> |
                <a class="text-warning mx-2" href="#">About Us</a> |
                <a class="text-warning mx-2" href="#">Shop</a> |
                <a class="text-warning mx-2" href="#">Newsletter</a>
    </footer>
</body>


