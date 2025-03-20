<?php
session_start();
include 'db.php'; // Include your database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Hash the input password using SHA256
        $hashedPassword = hash('sha256', $password);

        // Check in the admins table
        $stmt = $conn->prepare("SELECT * FROM admins WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            if ($hashedPassword === $row['password']) {
                $_SESSION['admin_id'] = $row['id'];
                $_SESSION['admin_name'] = $row['name'];
                $_SESSION['user_role'] = 'admin';

                if ($row['role'] === 'Admin') {
                    $_SESSION['user_role'] = 'admin'; // Admin role
                    header("Location: admin_dashboard.php");
                } elseif ($row['role'] === 'Printing Partner') {
                    $_SESSION['user_role'] = 'printing_partner'; // Printing partner role
                    header("Location: printing_panel.php");
                }
                exit();
            } else {
                echo "Invalid password.";
                exit();
            }
        }

        // Check in the users table
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            if ($hashedPassword === $row['password']) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_name'] = $row['name'];
                $_SESSION['user_role'] = 'user';
                header("Location: index.php");
                exit();
            } else {
                echo "Invalid password.";
                exit();
            }
        } else {
            echo "No user found with that email.";
            exit();
        }

        $stmt->close();
    } else {
        echo "Email and password must be provided.";
        exit();
    }
}
$conn->close();
?>
