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
                <button class="btn btn-warning mx-2" onclick="location.href='cart.php'">Your Cart</button>
                <button class="btn btn-warning mx-2" onclick="location.href='login.html'">Login</button> <!-- Admin Login Button -->
                <?php if (isset($_SESSION['user_id'])): ?> <!-- Check if user is logged in -->
                    <button class="btn btn-warning mx-2" onclick="location.href='user_panel.php'">My Account</button> <!-- My Account Button -->
                    <button class="btn btn-danger mx-2" onclick="location.href='logout.php'">Logout</button> <!-- Logout Button -->
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <div class="hero text-center text-white d-flex align-items-center justify-content-center" 
         style="background: url('images/hero.png') no-repeat center center/cover; height: 500px;">
    </div>

    <!-- Services -->
    <div class="services text-center my-4">
        <h2>Explore Our Services</h2>
    </div>

    <div class="carousel-container">
        <div id="carouselExample" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active text-center">
                    <h3>Paper Bank</h3>
                    <button class="btn btn-primary" onclick="window.location.href='paperbank.php'">View</button>
                    <!-- <button class="btn btn-primary">View</button> -->
                </div>
                <div class="carousel-item text-center">
                    <h3>Explore Recent Papers</h3>
                    <button class="btn btn-primary" onclick="window.location.href='papers.php'">View</button>
                </div>
                <div class="carousel-item text-center">
                    <h3>Explore Edexcel Curriculum</h3>
                    <button class="btn btn-primary" onclick="window.location.href='https://qualifications.pearson.com/en/about-us/qualification-brands/edexcel.html'">View</button>
                </div>
            </div>
            <a class="carousel-control-prev" href="#carouselExample" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carouselExample" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
    </div>
    


    <!-- Newsletter -->
    <div class="newsletter text-center my-4">
        <h3>Subscribe to our Newsletter</h3>
        <div class="form-group d-flex justify-content-center align-items-center">
            <input type="email" class="form-control w-50 me-2" placeholder="Enter your email">
            <button class="btn btn-warning">Sign Up</button>
        </div>
    </div>


    <!-- Footer -->
    <footer id="main-footer"; class="text-center bg-dark text-white p-3">
        <a class="text-warning mx-2" href="#">Home</a> |
        <a class="text-warning mx-2" href="#">About Us</a> |
        <a class="text-warning mx-2" href="#">Shop</a> |
        <a class="text-warning mx-2" href="#">Newsletter</a>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
