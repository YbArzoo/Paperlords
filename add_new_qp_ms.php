<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include 'db.php'; // Include database connection file here

// Fetch categories from the database
$categoriesQuery = "SELECT * FROM categories";
$categoriesResult = $conn->query($categoriesQuery);
$categories = [];
if ($categoriesResult->num_rows > 0) {
    while ($row = $categoriesResult->fetch_assoc()) {
        $categories[] = $row;
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Question Paper/Mark Scheme</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="text-center mb-4">
            <h1>Add New Question Paper or Mark Scheme</h1>
        </div>

        <!-- Form for adding resources -->
        <form action="add_resources_handler.php" method="POST" class="w-75 mx-auto">
            <div class="mb-3">
                <label for="name" class="form-label">Resource Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter resource name" required>
            </div>

            <div class="mb-3">
                <label for="type" class="form-label">Type</label>
                <select class="form-select" id="type" name="type" required>
                    <option value="" disabled selected>Select Type</option>
                    <option value="Question Paper">Question Paper</option>
                    <option value="Mark Scheme">Mark Scheme</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="category_id" class="form-label">Category</label>
                <select class="form-select" id="category_id" name="category_id" required>
                    <option value="" disabled selected>Select Category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id']; ?>"><?= $category['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="pdf_link" class="form-label">PDF Link</label>
                <input type="url" class="form-control" id="pdf_link" name="pdf_link" placeholder="Enter PDF link" required>
            </div>



            <div class="mb-3">
                <label for="existing_year_subject" class="form-label">Add to Existing Year/Subject</label>
                <select id="existing_year_subject" name="existing_year_subject" class="form-select">
                    <option value="" selected>None (Add New)</option>
                    <?php
                    $existingResourcesQuery = "SELECT DISTINCT category_id, subject, year FROM resources ORDER BY category_id, subject, year";
                    $existingResourcesResult = $conn->query($existingResourcesQuery);
                    if ($existingResourcesResult->num_rows > 0):
                        while ($row = $existingResourcesResult->fetch_assoc()): ?>
                            <option value="<?= $row['category_id'] . "|" . $row['subject'] . "|" . $row['year']; ?>">
                                <?= "Category ID: " . $row['category_id'] . " | " . $row['subject'] . " (" . $row['year'] . ")"; ?>
                            </option>
                        <?php endwhile; endif; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="year" class="form-label">Year</label>
                <div class="input-group">
                    <select class="form-select" id="existing_year" name="existing_year">
                        <option value="" disabled selected>Select Existing Year</option>
                        <?php
                        $yearsQuery = "SELECT DISTINCT year FROM resources ORDER BY year";
                        $yearsResult = $conn->query($yearsQuery);
                        if ($yearsResult->num_rows > 0):
                            while ($row = $yearsResult->fetch_assoc()): ?>
                                <option value="<?= $row['year']; ?>"><?= $row['year']; ?></option>
                            <?php endwhile; endif; ?>
                    </select>
                    <input type="number" class="form-control" id="new_year" name="new_year" placeholder="Add New Year">
                </div>
                <small class="text-muted">Select an existing year or add a new year.</small>
            </div>


            <div class="mb-3">
                <label for="month" class="form-label">Month</label>
                <select class="form-select" id="month" name="month" required>
                    <option value="" disabled selected>Select Month</option>
                    <?php
                    $months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
                    foreach ($months as $month): ?>
                        <option value="<?= $month; ?>"><?= $month; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="subject" class="form-label">Subject</label>
                <select class="form-select" id="subject" name="subject">
                    <option value="" disabled selected>Select Subject</option>
                    <?php
                    $subjectsQuery = "SELECT DISTINCT subject FROM resources";
                    $subjectsResult = $conn->query($subjectsQuery);
                    if ($subjectsResult->num_rows > 0) {
                        while ($row = $subjectsResult->fetch_assoc()) {
                            echo "<option value='" . htmlspecialchars($row['subject'], ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($row['subject'], ENT_QUOTES, 'UTF-8') . "</option>";
                        }
                    }
                    ?>
                    <option value="new">Add New Subject</option>
                </select>
            </div>

<div class="mb-3" id="new-subject-field" style="display: none;">
    <label for="new_subject" class="form-label">New Subject</label>
    <input type="text" class="form-control" id="new_subject" name="new_subject" placeholder="Enter New Subject">
</div>



            <script>
                document.getElementById("subject").addEventListener("change", function () {
                    if (this.value === "new") {
                        document.getElementById("new-subject-field").style.display = "block";
                    } else {
                        document.getElementById("new-subject-field").style.display = "none";
                    }
                });
            </script>

            <script>
                document.getElementById('subject').addEventListener('change', function() {
                    const newSubjectContainer = document.getElementById('new-subject-container');
                    newSubjectContainer.style.display = this.value === 'new' ? 'block' : 'none';
                });
            </script>
            
            <script>
                document.getElementById('existing_year_subject').addEventListener('change', function () {
                    if (this.value !== "") {
                        // Disable Year input if existing year/subject is selected
                        document.getElementById('existing_year').disabled = true;
                        document.getElementById('new_year').disabled = true;
                    } else {
                        // Enable Year input when adding new
                        document.getElementById('existing_year').disabled = false;
                        document.getElementById('new_year').disabled = false;
                    }
                });
            </script>

            <div class="text-center">
                <button type="submit" class="btn btn-success">Add Resource</button>
                <button type="button" class="btn btn-danger" onclick="window.location.href='admin_dashboard.php'">Cancel</button>
            </div>
        </form>
    </div>
</body>
</html>
