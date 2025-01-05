<?php
session_start();
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
        <!-- About Section -->
    <section class="about-page">
        <div class="about-content text-center">
            <h2>Welcome to PaperLords</h2>
            <p>Founded in 2018, Paperlords began from a passionate initiative by a group of individuals who called themselves "The IT Buoys." The name "Buoys" reflects our core mission—just as buoys guide ships and boats through challenging waters, we are dedicated to guiding students on their academic journeys. Our founders faced significant challenges in accessing past papers during their IGCSE and IAL studies, which inspired them to create a platform that would make these essential resources easily available to all students. At Paperlords, we believe in empowering students with the tools they need to excel, offering a comprehensive collection of past papers and study materials free of charge.</p>
            <p>Our commitment is to foster a supportive and collaborative learning community, ensuring that every student has the direction and resources needed to achieve outstanding results.</p>
        </div>
    </section>

        <!-- Footer -->
    <footer id="main-footer"; class="text-center bg-dark text-white p-3">
            <a class="text-warning mx-2" href="#">Home</a> |
            <a class="text-warning mx-2" href="#">About Us</a> |
            <a class="text-warning mx-2" href="#">Shop</a> |
            <a class="text-warning mx-2" href="#">Newsletter</a>
    </footer>




</body>