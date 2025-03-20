<?php
// Connect to the database
include 'db.php';

session_start();

// Fetch categories
$categoriesQuery = "SELECT * FROM categories";
$categoriesResult = $conn->query($categoriesQuery);
$categories = [];
if ($categoriesResult->num_rows > 0) {
    while ($row = $categoriesResult->fetch_assoc()) {
        $categories[$row['id']] = $row['name'];
    }
}

// Fetch resources grouped by category, subject, and year
$resourcesQuery = "
    SELECT r.*, c.name AS category_name
    FROM resources r
    INNER JOIN categories c ON r.category_id = c.id
    ORDER BY c.name, r.subject, r.year, r.month";
$resourcesResult = $conn->query($resourcesQuery);

$resourcesGrouped = [];
if ($resourcesResult->num_rows > 0) {
    while ($row = $resourcesResult->fetch_assoc()) {
        $resourcesGrouped[$row['category_name']][$row['subject']][$row['year']][] = $row;
    }
}

// Pass data to the frontend
$resourcesJson = json_encode($resourcesGrouped, JSON_PRETTY_PRINT);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Papers</title>
    <link rel="stylesheet" href="style.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alice&family=Averia+Serif+Libre:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header id = "main-header" class="d-flex justify-content-between align-items-center p-3 bg-dark text-white">
        <img src="images/logo.png" alt="Paperlords Logo" height="50">
        <nav class="navbar navbar-expand-md navbar-dark">
            <div class="navbar-nav">
                <a class="nav-item nav-link text-white mx-2" href="index.php">Home</a>
                <a class="nav-item nav-link text-white mx-2" href="paperbank.php">Paper Bank</a>
                <a class="nav-item nav-link text-white mx-2" href="about.php">About</a>
                <a class="nav-item nav-link text-white mx-2" href="all-products.php">All Products</a>
                <button class="btn btn-warning mx-2">Your Cart</button>
                <button class="btn btn-warning mx-2" onclick="location.href='login.html'">Login</button> <!-- Admin Login Button -->
                <?php if (isset($_SESSION['user_id'])): ?> <!-- Check if user is logged in -->
                    <button class="btn btn-warning mx-2" onclick="location.href='user_panel.php'">My Account</button> <!-- My Account Button -->
                    <button class="btn btn-danger mx-2" onclick="location.href='logout.php'">Logout</button> <!-- Logout Button -->
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->


    <!-- Selection Section -->
    <section id="selection-section">
        <!-- Step 1: Choose Category -->
        <div id="category-selection">
            <h2 class="section-title">Select Category</h2>
            <?php foreach ($categories as $id => $name): ?>
                <button class="btn btn-primary" onclick="selectCategory('<?php echo $name; ?>')"><?php echo $name; ?></button>
            <?php endforeach; ?>
        </div>

        <!-- Step 2: Choose Subject -->
        <div id="subject-selection" style="display: none;">
            <h2 class="section-title" id="subject-title">Select Subject</h2>
            <div id="subject-buttons"></div>
            <button class="btn btn-secondary" onclick="goBackToCategories()">Back</button>
        </div>

        <!-- Step 3: Choose Year -->
        <div id="year-selection" style="display: none;">
            <h2 class="section-title" id="year-title">Select Year/Session</h2>
            <div id="year-buttons"></div>
            <button class="btn btn-secondary" onclick="goBackToSubjects()">Back</button>
        </div>
    </section>

    <!-- Modal -->
    <div class="modal fade" id="paperModal" tabindex="-1" aria-labelledby="paperModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paperModalLabel">Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="modal-details"></p>
                    <ul id="modal-links"></ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer id="main-footer" class="text-center bg-dark text-white p-3">
        <a class="text-warning mx-2" href="#">Home</a> |
        <a class="text-warning mx-2" href="#">About Us</a> |
        <a class="text-warning mx-2" href="#">Shop</a> |
        <a class="text-warning mx-2" href="#">Newsletter</a>
    </footer>

    <!-- JavaScript -->
    <script>
        const resourcesGrouped = <?php echo $resourcesJson; ?>;

        function selectCategory(categoryName) {
            const subjects = Object.keys(resourcesGrouped[categoryName] || {});
            const subjectButtons = document.getElementById("subject-buttons");
            subjectButtons.innerHTML = "";
            subjects.forEach(subject => {
                const btn = document.createElement("button");
                btn.className = "btn btn-primary";
                btn.textContent = subject;
                btn.onclick = () => selectSubject(subject, categoryName);
                subjectButtons.appendChild(btn);
            });
            document.getElementById("category-selection").style.display = "none";
            document.getElementById("subject-selection").style.display = "block";
        }

        function selectSubject(subject, categoryName) {
            const years = Object.keys(resourcesGrouped[categoryName][subject] || {});
            const yearButtons = document.getElementById("year-buttons");
            yearButtons.innerHTML = "";
            years.forEach(year => {
                const btn = document.createElement("button");
                btn.className = "btn btn-primary";
                btn.textContent = `Year ${year}`;
                btn.onclick = () => showPaperDetails(subject, year, categoryName);
                yearButtons.appendChild(btn);
            });
            document.getElementById("subject-selection").style.display = "none";
            document.getElementById("year-selection").style.display = "block";
        }

        function showPaperDetails(subject, year, categoryName) {
            const papers = resourcesGrouped[categoryName][subject][year] || [];
            const modalDetails = document.getElementById("modal-details");
            const modalLinks = document.getElementById("modal-links");
            modalDetails.textContent = `${subject} Papers (${year})`;
            modalLinks.innerHTML = "";
            papers.forEach(paper => {
                const li = document.createElement("li");
                const a = document.createElement("a");
                a.textContent = `${paper.type}: ${paper.name}`;
                a.href = paper.pdf_link;
                a.target = "_blank";
                li.appendChild(a);
                modalLinks.appendChild(li);
            });
            $('#paperModal').modal('show');
        }

        function goBackToCategories() {
            document.getElementById("category-selection").style.display = "block";
            document.getElementById("subject-selection").style.display = "none";
            document.getElementById("year-selection").style.display = "none";
        }

        function goBackToSubjects() {
            document.getElementById("subject-selection").style.display = "block";
            document.getElementById("year-selection").style.display = "none";
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.4.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
