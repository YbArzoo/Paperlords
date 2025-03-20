<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $type = $_POST['type'];
    $pdf_link = $_POST['pdf_link'];

    if (!empty($_POST['existing_year_subject'])) {
        // Handle adding to an existing year/subject
        list($category_id, $subject, $year) = explode("|", $_POST['existing_year_subject']);
        $month = $_POST['month']; // Add month even for existing year/subject
    } else {
        // Handle as a new entry
        $category_id = $_POST['category_id'];
        $subject = $_POST['subject'] === 'new' ? $_POST['new_subject'] : $_POST['subject'];
        $year = !empty($_POST['new_year']) ? $_POST['new_year'] : $_POST['year'];
        $month = $_POST['month'];

        // Check if the subject already exists in the subjects table
        $checkSubjectStmt = $conn->prepare("SELECT id FROM subjects WHERE name = ?");
        $checkSubjectStmt->bind_param("s", $subject);
        $checkSubjectStmt->execute();
        $checkSubjectStmt->store_result();

        if ($checkSubjectStmt->num_rows === 0) {
            // Insert the new subject into the subjects table
            $insertSubjectStmt = $conn->prepare("INSERT INTO subjects (name) VALUES (?)");
            $insertSubjectStmt->bind_param("s", $subject);
            $insertSubjectStmt->execute();
            $insertSubjectStmt->close();
        }

        $checkSubjectStmt->close();
    }

    // Validate that required fields are not empty
    if (empty($name) || empty($type) || empty($category_id) || empty($pdf_link) || empty($year) || empty($month) || empty($subject)) {
        $_SESSION['error'] = "All fields are required. Please try again.";
        header("Location: add_new_qp_ms.php");
        exit();
    }

    // Insert the resource into the resources table
    $stmt = $conn->prepare("INSERT INTO resources (name, type, category_id, pdf_link, year, month, subject) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisiss", $name, $type, $category_id, $pdf_link, $year, $month, $subject);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Resource added successfully!";
        header("Location: add_new_qp_ms.php");
    } else {
        $_SESSION['error'] = "Failed to add resource. Please try again.";
        header("Location: add_new_qp_ms.php");
    }

    $stmt->close();
    $conn->close();
}
?>
