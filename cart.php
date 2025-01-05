<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

include 'db.php';

$user_id = $_SESSION['user_id'];

// Fetch cart items
$query = "
    SELECT ci.id, p.name, p.image_link, ci.quantity, ci.price, (ci.quantity * ci.price) AS total_price
    FROM cart_items ci
    INNER JOIN products p ON ci.product_id = p.id
    WHERE ci.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
$total_amount = 0;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cart_items[] = $row;
        $total_amount += $row['total_price'];
    }
}
$stmt->close();

$delivery_charge = 60; // Fixed delivery charge


// Fetch user information
$user_query = "SELECT name, email, phone_number, address FROM users WHERE id = ?";
$user_stmt = $conn->prepare($user_query);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();
$user_stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <!-- Header -->
    <header id="main-header" class="d-flex justify-content-between align-items-center p-3 bg-dark text-white">
        <img src="images/logo.png" alt="Paperlords Logo" height="50">
        <nav class="navbar navbar-expand-md navbar-dark">
            <div class="navbar-nav">
                <a class="nav-item nav-link text-white mx-2" href="index.php">Home</a>
                <a class="nav-item nav-link text-white mx-2" href="paperbank.php">Paper Bank</a>
                <a class="nav-item nav-link text-white mx-2" href="about.php">About</a>
                <a class="nav-item nav-link text-white mx-2" href="all-products.php">All Products</a>
                <button class="btn btn-warning mx-2" onclick="location.href='cart.php'">Your Cart</button>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <button class="btn btn-warning mx-2" onclick="location.href='user_panel.php'">My Account</button>
                    <button class="btn btn-danger mx-2" onclick="location.href='logout.php'">Logout</button>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <h1 class="text-center mt-4 mb-3">Your Cart</h1>

    <!-- Steps Navigation -->
    <div class="steps-nav d-flex justify-content-center mb-4">
        <button class="btn btn-outline-primary mx-2" onclick="showStep(1)">1. Shopping Cart</button>
        <button class="btn btn-outline-primary mx-2" onclick="showStep(2)">2. Shipping Details</button>
        <button class="btn btn-outline-primary mx-2" onclick="showStep(3)">3. Payment Options</button>
    </div>

    <!-- Step 1: Shopping Cart -->
    <section id="step-1" class="step-section">
        <div class="d-flex justify-content-between">
            <!-- Shopping Cart Details -->
            <div class="cart-details w-50">
                <h2>Shopping Cart</h2>
                <?php if (count($cart_items) > 0): ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Image</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart_items as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['name']) ?></td>
                                    <td><img src="<?= htmlspecialchars($item['image_link']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" style="width: 50px;"></td>
                                    <td><?= htmlspecialchars($item['quantity']) ?></td>
                                    <td>৳<?= htmlspecialchars(number_format($item['price'], 2)) ?></td>
                                    <td>৳<?= htmlspecialchars(number_format($item['total_price'], 2)) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>Your cart is empty.</p>
                <?php endif; ?>
            </div>

            <!-- Summary -->
            <div class="cart-summary w-50 border p-3">
                <h3 class="summary-title">Summary</h3>
                <ul class="list-unstyled">
                    <li><strong>Subtotal:</strong> ৳<?= number_format($total_amount, 2) ?></li>
                    <li><strong>Tax:</strong> ৳0.00</li>
                    <li><strong>Delivery Charge:</strong> ৳<?= number_format($delivery_charge, 2) ?></li>
                    <li><strong>Total:</strong> ৳<?= number_format($total_amount + $delivery_charge, 2) ?></li>
                </ul>
            </div>

        </div>
        <div class="d-flex justify-content-between text-center mt-4">
            <button class="btn btn-success mx-2" onclick="showStep(2)">Next</button>
            <button class="btn btn-danger mx-2" onclick="cancelCart()">Cancel</button>
        </div>
    </section>

    <!-- Step 2: Shipping Details -->
    <section id="step-2" class="step-section" style="display: none;">
        <div class="d-flex justify-content-center align-items-center">
            <div class="shipping-form justify-content-center w-50">
                <h2 class="text-center mb-4">Shipping Details</h2>
                <form id="shipping-form">
                    <div class="form-group mb-3">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="phone_number">Phone Number</label>
                        <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?= htmlspecialchars($user['phone_number']) ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="address">Address</label>
                        <textarea class="form-control" id="address" name="address" required><?= htmlspecialchars($user['address']) ?></textarea>
                    </div>
                    <div class="text-center mt-4">
                        <button type="button" class="btn btn-success mx-2" onclick="showStep(3)">Next</button>
                        <button type="button" class="btn btn-danger mx-2" onclick="cancelCart()">Cancel</button>
                    </div>
                </form>

            </div>
        </div>
    </section>

    <!-- Step 3: Payment Options -->
    <section id="step-3" class="step-section" style="display: none;">
        <h2 class="text-center">Payment Options</h2>
        <div class="payment-options d-flex justify-content-center">
            <button class="btn btn-outline-primary mx-2 payment-option" onclick="processOrder('Cash on Delivery')">
                Cash on Delivery
            </button>
        </div>
        <div class="text-center mt-4">
            <button class="btn btn-success proceed-btn mx-2" onclick="processOrder('Cash on Delivery')">Proceed</button>
            <button class="btn btn-danger mx-2" onclick="cancelCart()">Cancel</button>
        </div>
    </section>


    <script>
        document.querySelectorAll('.payment-option').forEach(button => {
        button.addEventListener('click', function() {
            document.querySelectorAll('.payment-option').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
        });
    });
        function showStep(step) {
            document.querySelectorAll('.step-section').forEach(section => section.style.display = 'none');
            document.getElementById(`step-${step}`).style.display = 'block';
        }

        function cancelCart() {
            if (confirm('Are you sure you want to cancel? This will empty your cart.')) {
                fetch('clear_cart.php', {
                    method: 'POST',
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Your cart has been cleared.');
                        location.reload();
                    } else {
                        alert('Failed to clear cart. Please try again.');
                    }
                });
            }
        }

        function processOrder(paymentMode) {
            if (confirm('Proceed with payment?')) {
                fetch('process_order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ paymentMode })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Your order has been placed successfully.');
                        window.location.href = 'index.php';
                    } else {
                        alert('Failed to process order. ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while processing your order.');
                });
            }
        }
    </script>
</body>
</html>
