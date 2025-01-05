<?php
session_start();

// Ensure only printing partners have access
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'printing_partner') {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Fetch orders with user/guest user details and their associated products
$query = "
    SELECT 
        o.id AS order_id, 
        o.created_at, 
        COALESCE(u.name, g.name) AS name, 
        COALESCE(u.email, g.email) AS email,
        oi.product_id, 
        o.payment_mode, 
        o.status, 
        o.total_amount
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    LEFT JOIN guest_users g ON o.guest_user_id = g.id
    INNER JOIN order_items oi ON o.id = oi.order_id
    ORDER BY o.created_at DESC
";

$result = $conn->query($query);
$orders = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Printing Partner Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>

<body>
    <header class="bg-dark text-white p-3 d-flex justify-content-between align-items-center">
        <h1>Printing Partner Panel</h1>
        <button class="btn btn-danger" onclick="window.location.href='logout.php'">Logout</button>
    </header>

    <div class="container my-4">
        <h3 class="mb-4">New Orders</h3>
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Order ID</th>
                    <th>Time Stamp</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Product ID</th>
                    <th>Payment Mode</th>
                    <th>Status</th>
                    <th>Total Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($orders)): ?>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['order_id']) ?></td>
                            <td><?= htmlspecialchars($order['created_at']) ?></td>
                            <td><?= htmlspecialchars($order['name']) ?></td>
                            <td><?= htmlspecialchars($order['email']) ?></td>
                            <td><?= htmlspecialchars($order['product_id']) ?></td>
                            <td><?= htmlspecialchars($order['payment_mode']) ?></td>
                            <td>
                                <select class="form-select form-select-sm">
                                    <option value="pending" <?= $order['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="confirmed" <?= $order['status'] === 'Confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                    <option value="packaging" <?= $order['status'] === 'Packaging' ? 'selected' : '' ?>>Packaging</option>
                                    <option value="completed" <?= $order['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                                    <option value="cancelled" <?= $order['status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                            </td>
                            <td>৳ <?= number_format($order['total_amount'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">No orders available.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
