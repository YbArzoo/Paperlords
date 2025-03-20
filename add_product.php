<?php
session_start();

// Check if the user is an admin (add your own authentication logic here)
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}


include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['productName'] ?? '';
    $price = $_POST['productPrice'] ?? '';
    $description = $_POST['productDiscription'] ?? '';
    $weight = $_POST['productWeight'] ?? '';
    $type = $_POST['productType'] ?? '';
    $imageLink = '';

    // Handle file upload
    if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileName = basename($_FILES['productImage']['name']);
        $targetFilePath = $uploadDir . $fileName;
        if (move_uploaded_file($_FILES['productImage']['tmp_name'], $targetFilePath)) {
            $imageLink = $targetFilePath;
        }
    }

    // Get category ID based on type
    $categoryQuery = "SELECT id FROM categories WHERE name = ?";
    $stmt = $conn->prepare($categoryQuery);
    $stmt->bind_param("s", $type);
    $stmt->execute();
    $categoryResult = $stmt->get_result();
    $category = $categoryResult->fetch_assoc();
    $categoryId = $category['id'] ?? null;

    // Insert product into database
    $query = "INSERT INTO products (name, description, price, weight, category_id, image_link) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssddis", $name, $description, $price, $weight, $categoryId, $imageLink);

    if ($stmt->execute()) {
        $successMessage = "Product added successfully!";
    } else {
        $errorMessage = "Failed to add product: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container mt-5">
        <div class="text-center mb-4">
            <h1>Add New Product</h1>
        </div>

        <?php if (isset($successMessage)): ?>
            <div class="alert alert-success text-center"> <?= htmlspecialchars($successMessage) ?> </div>
        <?php endif; ?>
        
        <?php if (isset($errorMessage)): ?>
            <div class="alert alert-danger text-center"> <?= htmlspecialchars($errorMessage) ?> </div>
        <?php endif; ?>

        <form class="w-50 mx-auto" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="productName" class="form-label">Product Name</label>
                <input type="text" class="form-control" id="productName" name="productName" placeholder="Enter product name" required>
            </div>

            <div class="mb-3">
                <label for="productPrice" class="form-label">Product Price</label>
                <input type="number" step="0.01" class="form-control" id="productPrice" name="productPrice" placeholder="Enter product price" required>
            </div>

            <div class="mb-3">
                <label for="productDiscription" class="form-label">Product Description</label>
                <input type="text" class="form-control" id="productDiscription" name="productDiscription" placeholder="Enter product description">
            </div>

            <div class="mb-3">
                <label for="productImage" class="form-label">Upload Image</label>
                <input type="file" class="form-control" id="productImage" name="productImage" required>
            </div>

            <div class="mb-3">
                <label for="productWeight" class="form-label">Weight (kg)</label>
                <input type="number" step="0.01" class="form-control" id="productWeight" name="productWeight" placeholder="Enter weight in kg">
            </div>

            <div class="mb-3">
                <label for="productType" class="form-label">Type</label>
                <select class="form-select" id="productType" name="productType" required>
                    <option value="IGCSE">IGCSE</option>
                    <option value="IAL">IAL</option>
                </select>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary me-3">Add</button>
                <button type="button" class="btn btn-danger" onclick="window.location.href='admin_dashboard.php'">Cancel</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
