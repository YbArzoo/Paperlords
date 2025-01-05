<?php
session_start(); // Start the session
include __DIR__ . '/db.php'; // Include your database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html"); // Redirect to login if not logged in
    exit();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the user ID from the hidden input
    $user_id = $_POST['user_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password']; // Password can be empty if not changing
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];

    // Prepare the SQL update statement
    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone_number = ?, address = ? WHERE id = ?");
    
    // Bind parameters
    $stmt->bind_param("ssssi", $name, $email, $phone_number, $address, $user_id);

    // Execute the statement
    if ($stmt->execute()) {
        // If password is provided, hash it and update
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashedPassword, $user_id);
            $stmt->execute();
        }
        // Redirect back to user panel with a success message
        header("Location: user_panel.php?success=1");
        exit();
    } else {
        echo "Error updating record: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>