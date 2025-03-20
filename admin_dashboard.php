<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Fetch Total Sales and Total Income
$salesQuery = "SELECT COUNT(*) AS total_sales, COALESCE(SUM(total_amount), 0) AS total_income FROM orders";
$salesResult = $conn->query($salesQuery);
$salesData = $salesResult->fetch_assoc();
$totalSales = $salesData['total_sales'] ?? 0;
$totalIncome = $salesData['total_income'] ?? 0;

// Fetch New Orders
$newOrdersQuery = "
    SELECT o.id AS order_id, o.created_at, 
           COALESCE(u.name, g.name) AS name, 
           COALESCE(u.email, g.email) AS email,
           COALESCE(u.phone_number, g.phone) AS phone,
           COALESCE(u.address, g.address) AS address,
           o.total_amount, o.payment_mode, o.status
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    LEFT JOIN guest_users g ON o.guest_user_id = g.id
    ORDER BY o.created_at DESC
";
$newOrdersResult = $conn->query($newOrdersQuery);
$newOrders = $newOrdersResult->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="styles_admin.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>

<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <nav class="bg-dark text-white p-3" id="sidebar" style="height: 100vh; transition: width 0.3s;">
            <div class="d-flex justify-content-end mb-3">
                <i class="fas fa-bars toggle-btn" onclick="toggleSidebar()"></i>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item mb-3">
                    <a href="add_new_admin.html" class="text-decoration-none text-white d-flex align-items-center">
                        <i class="fas fa-user-plus" title="Add New Admin"></i>
                        <span class="d-none sidebar-text">Add New Admin</span>
                    </a>
                </li>
                <li class="nav-item mb-3">
                    <a href="add_product.php" class="text-decoration-none text-white d-flex align-items-center">
                        <i class="fas fa-plus-square" title="Add Product"></i>
                        <span class="d-none sidebar-text">Add Product</span>
                    </a>
                </li>
                <li class="nav-item mb-3">
                    <a href="add_new_qp_ms.php" class="text-decoration-none text-white d-flex align-items-center">
                        <i class="fas fa-file-alt" title="Add Question Paper & Mark Schemes"></i>
                        <span class="d-none sidebar-text">Add Question Paper & Mark Schemes</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="p-4 w-100">
            <!-- Top Header with Logout Button -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Admin Dashboard</h1>
                <button class="btn btn-danger" onclick="location.href='logout.php'">Logout</button>
            </div>

            <!-- Total Sales and Income -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h5 class="card-title">Total Sales</h5>
                            <p class="card-text fs-3"><?= $totalSales; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h5 class="card-title">Total Income</h5>
                            <p class="card-text fs-3">৳ <?= number_format($totalIncome, 2); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- New Orders -->
            <div>
                <h3>New Orders</h3>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Time Stamp</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Total Amount</th>
                            <th>Payment Mode</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($newOrders) > 0): ?>
                            <?php foreach ($newOrders as $order): ?>
                                <tr>
                                    <td><?= $order['order_id']; ?></td>
                                    <td><?= $order['created_at']; ?></td>
                                    <td><?= $order['name'] ?? 'N/A'; ?></td>
                                    <td><?= $order['email'] ?? 'N/A'; ?></td>
                                    <td><?= $order['phone'] ?? 'N/A'; ?></td>
                                    <td><?= $order['address'] ?? 'N/A'; ?></td>
                                    <td>৳ <?= number_format($order['total_amount'], 2); ?></td>
                                    <td><?= $order['payment_mode']; ?></td>
                                    <td>
                                        <select class="form-select">
                                            <option value="pending" <?= $order['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="completed" <?= $order['status'] === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                            <option value="delivered" <?= $order['status'] === 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                            <option value="cancelled" <?= $order['status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                            <option value="confirmed" <?= $order['status'] === 'Confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                        </select>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center">No orders found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const texts = document.querySelectorAll('.sidebar-text');
            if (sidebar.style.width === '250px') {
                sidebar.style.width = '80px';
                texts.forEach(text => text.classList.add('d-none'));
            } else {
                sidebar.style.width = '250px';
                texts.forEach(text => text.classList.remove('d-none'));
            }
        }
    </script>
</body>
</html>
