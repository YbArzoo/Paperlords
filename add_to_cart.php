<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

include 'db.php';

$data = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($data['product_id'], $data['price']) || !is_numeric($data['price'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input data']);
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = intval($data['product_id']);
$price = floatval($data['price']);

try {
    // Check if the product is already in the cart
    $check_query = "SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update quantity if already in cart
        $row = $result->fetch_assoc();
        $new_quantity = $row['quantity'] + 1;

        $update_query = "UPDATE cart_items SET quantity = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ii", $new_quantity, $row['id']);
    } else {
        // Insert new item into cart
        $insert_query = "INSERT INTO cart_items (user_id, product_id, price, quantity) VALUES (?, ?, ?, 1)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("iid", $user_id, $product_id, $price);
    }

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Product added to cart']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add product to cart']);
    }
} catch (Exception $e) {
    // Handle exceptions and log errors
    error_log("Error in add_to_cart.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while adding product to cart']);
} finally {
    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
