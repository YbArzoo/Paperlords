<?php
session_start(); // Start the session
include __DIR__ . '/db.php'; // Include your database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html"); // Redirect to login if not logged in
    exit();
}

// Fetch user information from the database
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc(); // Fetch user data
} else {
    echo "User not found.";
    exit();
}

// Fetch previous orders for the logged-in user
$ordersQuery = "
    SELECT o.id AS order_id, o.created_at, o.total_amount, o.status, 
           GROUP_CONCAT(p.name SEPARATOR ', ') AS items
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    LEFT JOIN products p ON oi.product_id = p.id
    WHERE o.user_id = ? OR o.guest_user_id = ?
    GROUP BY o.id
    ORDER BY o.created_at DESC";
$ordersStmt = $conn->prepare($ordersQuery);
$ordersStmt->bind_param("ii", $user_id, $user_id);
$ordersStmt->execute();
$ordersResult = $ordersStmt->get_result();

$orders = [];
if ($ordersResult->num_rows > 0) {
    while ($row = $ordersResult->fetch_assoc()) {
        $orders[] = $row; // Store each order in the array
    }
}

$stmt->close();
$ordersStmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="container mt-5">
        <div class="text-center mb-4">
            <h1>User Panel</h1>
        </div>

        <!-- Success Message -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success" role="alert">
                Your information has been updated successfully!
            </div>
        <?php endif; ?>

        <!-- Previous Orders -->
        <section class="mb-5">
            <h3>Previous Orders</h3>
            <?php if (count($orders) > 0): ?>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                                <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($order['created_at']))); ?></td>
                                <td><?php echo htmlspecialchars($order['items']); ?></td>
                                <td>৳ <?php echo htmlspecialchars(number_format($order['total_amount'], 2)); ?></td>
                                <td><?php echo htmlspecialchars($order['status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No previous orders found.</p>
            <?php endif; ?>
        </section>

        <!-- Editable Information -->
        <section class="mb-5">
            <h3>Edit Profile Information</h3>
            <form action="update_user.php" method="POST"> <!-- Action points to update_user.php -->
                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>"> <!-- Hidden field for user ID -->
                <div class="mb-3">
                    <label for="userName" class="form-label">Name</label>
                    <input type="text" class="form-control" id="userName" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="userEmail" class="form-label">Email</label>
                    <input type="email" class="form-control" id="userEmail" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="userPassword" class="form-label">Password</label>
                    <input type="password" class="form-control" id="userPassword" name="password" placeholder="Enter new password (leave blank to keep current)">
                </div>
                <div class="mb-3">
                    <label for="userPhone" class="form-label">Phone Number</label>
                    <input type="text" class="form-control" id="userPhone" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>">
                </div>
                <div class="mb-3">
                    <label for="userAddress" class="form-label">Address</label>
                    <textarea class="form-control" id="userAddress" name="address" rows="3"><?php echo htmlspecialchars($user['address']); ?></textarea>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </section>

        <!-- Return Button -->
        <div class="text-center">
            <a href="index.php" class="btn btn-secondary">Return to Home</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
