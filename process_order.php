<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

include 'db.php';

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);
$payment_mode = $data['paymentMode'];

// Validate payment mode
if (!in_array($payment_mode, ['Cash on Delivery', 'Online Payment'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid payment mode']);
    exit();
}

// Fetch cart items for the user
$cart_query = "
    SELECT ci.product_id, ci.quantity, ci.price
    FROM cart_items ci
    WHERE ci.user_id = ?";
$stmt = $conn->prepare($cart_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart_items_result = $stmt->get_result();
$cart_items = $cart_items_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if (count($cart_items) === 0) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty']);
    exit();
}

// Calculate total amount
$total_amount = 0;
foreach ($cart_items as $item) {
    $total_amount += $item['quantity'] * $item['price'];
}

// Insert order into the orders table
$order_query = "
    INSERT INTO orders (user_id, total_amount, payment_mode, status)
    VALUES (?, ?, ?, 'Pending')";
$order_stmt = $conn->prepare($order_query);
$order_stmt->bind_param("ids", $user_id, $total_amount, $payment_mode);

if ($order_stmt->execute()) {
    $order_id = $order_stmt->insert_id;
    $order_stmt->close();

    // Insert order items into the order_items table
    $order_items_query = "
        INSERT INTO order_items (order_id, product_id, quantity, price)
        VALUES (?, ?, ?, ?)";
    $order_items_stmt = $conn->prepare($order_items_query);

    foreach ($cart_items as $item) {
        $order_items_stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
        $order_items_stmt->execute();
    }

    $order_items_stmt->close();

    // Clear the cart
    $clear_cart_query = "DELETE FROM cart_items WHERE user_id = ?";
    $clear_cart_stmt = $conn->prepare($clear_cart_query);
    $clear_cart_stmt->bind_param("i", $user_id);
    $clear_cart_stmt->execute();
    $clear_cart_stmt->close();

    echo json_encode(['success' => true, 'message' => 'Order placed successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to place order']);
}

$conn->close();
?>
