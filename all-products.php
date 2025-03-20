<?php
session_start(); // Start the session

// Check if user is logged in
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Database connection
$conn = new mysqli("localhost", "root", "", "paperlords");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch categories for filters
$categoriesQuery = "SELECT id, name FROM categories";
$categoriesResult = $conn->query($categoriesQuery);
$categories = [];
if ($categoriesResult->num_rows > 0) {
    while ($row = $categoriesResult->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Initialize filters
$search = $_GET['search'] ?? '';
$categoryFilter = $_GET['category'] ?? '';

// Fetch products
$query = "SELECT p.*, c.name AS category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          WHERE 1=1";

$conditions = [];
if ($search) {
    $conditions[] = "(p.name LIKE '%$search%' OR p.description LIKE '%$search%')";
}
if ($categoryFilter) {
    $conditions[] = "c.name = '$categoryFilter'";
}

// Apply conditions
if (count($conditions) > 0) {
    $query .= " AND (" . implode(' OR ', $conditions) . ")";
}

$query .= " ORDER BY p.created_at DESC";

$result = $conn->query($query);
$products = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Add to Cart logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'] ?? 1;

    // Add item to the session cart
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }

    header("Location: all-products.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Products</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
                <button class="btn btn-warning mx-2" onclick="location.href='login.html'">Login</button>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <button class="btn btn-warning mx-2" onclick="location.href='user_panel.php'">My Account</button>
                    <button class="btn btn-danger mx-2" onclick="location.href='logout.php'">Logout</button>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <section class="hero_shop">
        <img src="images/all-products-hero.png" alt="Shop Hero Image" class="hero_shop_image">
    </section>

    <!-- Search Bar -->
    <div class="search-bar">
        <form method="GET" action="">
            <input type="text" name="search" id="search" placeholder="Search for products..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>

    <div class="container">
        <!-- Filters Section -->
        <aside class="filters">
            <form method="GET" action="">
                <div class="filter-group">
                    <label>Tags</label>
                    <?php foreach ($categories as $category): ?>
                        <button type="submit" name="category" value="<?php echo $category['name']; ?>" class="filter-btn <?php echo ($categoryFilter === $category['name']) ? 'selected' : ''; ?>">
                            <?php echo $category['name']; ?>
                        </button>
                    <?php endforeach; ?>
                    <?php if ($categoryFilter): ?>
                        <button type="submit" name="category" value="" class="filter-btn reset-btn">
                            Remove Filter
                        </button>
                    <?php endif; ?>
                </div>
            </form>
        </aside>

        <div class="main-container">
        <section class="products d-flex flex-wrap">
            <?php foreach ($products as $product): ?>
                <div class="product-box m-2" onclick="showPopup(<?php echo htmlspecialchars(json_encode($product)); ?>)">
                    <img src="<?php echo $product['image_link']; ?>" alt="Product">
                    <h4 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h4>
                    <span class="tag"><?php echo htmlspecialchars($product['category_name']); ?></span>
                    <p class="price">৳<?php echo number_format($product['price'], 2); ?></p>
                </div>
            <?php endforeach; ?>
        </section>
    </div>

    <!-- Popup -->
    <div id="product-popup" class="popup hidden">
        <div class="popup-content">
            <span class="popup-close" onclick="closePopup()">&times;</span>
            <div class="popup-product">
                <img id="popup-product-image" src="" alt="Product Image">
            </div>
            <div class="popup-details">
                <h2 id="popup-product-name"></h2>
                <p id="popup-product-price"></p>
                <p id="popup-product-category"></p>
                <p id="popup-product-description"></p>
                <button class="add-to-cart-btn" onclick="addToCart(currentProduct.id, currentProduct.price)">Add to Cart</button>

            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        const popup = document.getElementById('product-popup');

        let currentProduct = null;

        function showPopup(product) {
            currentProduct = product;
            document.getElementById('popup-product-name').textContent = product.name;
            document.getElementById('popup-product-price').textContent = `৳${product.price}`;
            document.getElementById('popup-product-category').textContent = `Category: ${product.category_name}`;
            document.getElementById('popup-product-description').textContent = product.description;
            document.getElementById('popup-product-image').src = product.image_link;

            popup.classList.remove('hidden');
        }

        function closePopup() {
            popup.classList.add('hidden');
        }

        function addToCart(productId, price) {
            fetch('add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    price: price
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message); // Product added to cart
                    closePopup(); // Close the popup after adding to cart
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        }
    </script>

</body>
</html>




