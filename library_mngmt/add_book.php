<?php
include 'sidebar.php';
include "config.php"; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = mysqli_real_escape_string($conn, $_POST["title"]);
    $author = mysqli_real_escape_string($conn, $_POST["author"]);
    $publish_year = intval($_POST["publish_year"]);
    $category = mysqli_real_escape_string($conn, $_POST["category"]);
    $available_copy = intval($_POST["available_copy"]);
    $availability = ($available_copy > 0) ? "Available" : "Not Available";
    $price = floatval($_POST["price"]);

    // Validation checks
    if (empty($title) || empty($author) || empty($publish_year) || empty($category) || empty($available_copy) || empty($price)) {
        $error_message = "All fields are required!";
    } elseif ($publish_year < 1000 || $publish_year > date("Y")) {
        $error_message = "Please enter a valid publish year!";
    } elseif ($available_copy < 0) {
        $error_message = "Available copies cannot be negative!";
    } elseif ($price <= 0) {
        $error_message = "Price must be greater than 0!";
    } else {
        // Check if the book already exists
        $check_sql = "SELECT * FROM books WHERE title='$title' AND author='$author' AND publish_year='$publish_year' AND category='$category'";
        $check_result = $conn->query($check_sql);

        if ($check_result->num_rows > 0) {
            $error_message = "This book already exists in the database!";
        } else {
            // Get the latest book ID and increment it
            $result = $conn->query("SELECT MAX(id) AS max_id FROM books");
            $row = $result->fetch_assoc();
            $next_id = $row['max_id'] + 1;

            $sql = "INSERT INTO books (id, title, author, publish_year, category, available_copy, availability, price) 
                    VALUES ('$next_id', '$title', '$author', '$publish_year', '$category', '$available_copy', '$availability', '$price')";

            if ($conn->query($sql) === TRUE) {
                $success_message = "Book added successfully!";
                $_POST = array();
            } else {
                $error_message = "Error: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>G6 - Library</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Poppins Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s ease, width 0.3s ease;
        }
        .sidebar.collapsed + .main-content {
            margin-left: 60px;
            width: calc(100% - 60px);
        }
        .sidebar.hidden + .main-content {
            margin-left: 0;
            width: 100%;
        }
        .header {
            background: #342519;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);
        }
        .title {
            color: #ede6d9 !important;
        }
        .submit-btn {
            background-color: #684f36 !important;
            color: white !important;
        }
        .go-back-btn {
            background-color: #f5efeb !important;
            color: black !important;
        }
        .form-container {
            transition: margin-left 0.3s ease;
            margin: 0 auto;
            background: white;
            padding: 2rem;
            border-radius: 0.25rem;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 15px;
            }
            .header h2 {
                font-size: 1.5rem;
            }
            .form-container {
                padding: 1.5rem;
            }
        }
        @media (max-width: 576px) {
            .form-container {
                padding: 1rem;
            }
            .mb-3.row > .col-md-6 {
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="main-content py-4" id="main-content">
        <div class="header w-100 p-3 d-flex justify-content-between align-items-center">
            <h2 class="display-5 fw-bold mb-0 title">
                <i class="bi bi-book"></i> Add New Book
            </h2>
            <div>
                <a href="view_books.php" class="btn go-back-btn"><i class="bi bi-arrow-left"></i> Go Back </a>
            </div>
        </div><br>
        
        <div class="form-container">
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form method="POST" novalidate>
                <div class="mb-3">
                    <label for="title" class="form-label">Title:</label>
                    <input type="text" class="form-control" id="title" name="title" value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" placeholder="Enter Title Name" required>
                    <div class="invalid-feedback">Please provide a title.</div> 
                </div>

                <div class="mb-3">
                    <label for="author" class="form-label">Author:</label>
                    <input type="text" class="form-control" id="author" name="author" value="<?php echo isset($_POST['author']) ? htmlspecialchars($_POST['author']) : ''; ?>" placeholder="Enter Author Name" required>
                    <div class="invalid-feedback">Please provide an author.</div>
                </div>

                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label for="publish_year" class="form-label">Publish Year:</label>
                        <input type="number" class="form-control" id="publish_year" name="publish_year" min="1000" max="<?php echo date('Y'); ?>" value="<?php echo isset($_POST['publish_year']) ? htmlspecialchars($_POST['publish_year']) : ''; ?>" placeholder="Enter Publish Year" required>
                        <div class="invalid-feedback">Please provide a valid year (1000-<?php echo date('Y'); ?>).</div>
                    </div>
                    <div class="col-md-6">
                        <label for="category" class="form-label">Category:</label>
                        <select class="form-select" name="category" required>
                            <option value="">Select Category</option>
                            <option value="Adventure" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Adventure') ? 'selected' : ''; ?>>Adventure</option>
                            <option value="Art" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Art') ? 'selected' : ''; ?>>Art</option>
                            <option value="Biography" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Biography') ? 'selected' : ''; ?>>Biography</option>
                            <option value="Business" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Business') ? 'selected' : ''; ?>>Business</option>
                            <option value="Cooking" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Cooking') ? 'selected' : ''; ?>>Cooking</option>
                            <option value="Fiction" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Fiction') ? 'selected' : ''; ?>>Fiction</option>
                            <option value="Health" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Health') ? 'selected' : ''; ?>>Health</option>
                            <option value="History" <?php echo (isset($_POST['category']) && $_POST['category'] == 'History') ? 'selected' : ''; ?>>History</option>
                            <option value="Horror" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Horror') ? 'selected' : ''; ?>>Horror</option>
                            <option value="Non-Fiction" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Non-Fiction') ? 'selected' : ''; ?>>Non-Fiction</option>
                            <option value="Philosophy" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Philosophy') ? 'selected' : ''; ?>>Philosophy</option>
                            <option value="Poetry" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Poetry') ? 'selected' : ''; ?>>Poetry</option>
                            <option value="Psychology" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Psychology') ? 'selected' : ''; ?>>Psychology</option>
                            <option value="Romance" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Romance') ? 'selected' : ''; ?>>Romance</option>
                            <option value="Science" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Science') ? 'selected' : ''; ?>>Science</option>
                            <option value="Self-Help" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Self-Help') ? 'selected' : ''; ?>>Self-Help</option>
                            <option value="Technology" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Technology') ? 'selected' : ''; ?>>Technology</option>
                            <option value="Travel" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Travel') ? 'selected' : ''; ?>>Travel</option>
                        </select>
                        <div class="invalid-feedback">Please select a category.</div>
                    </div>
                </div>

                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label for="available_copy" class="form-label">Available Copies:</label>
                        <input type="number" class="form-control" id="available_copy" name="available_copy" min="0" value="<?php echo isset($_POST['available_copy']) ? htmlspecialchars($_POST['available_copy']) : ''; ?>" placeholder="Enter Number of Copies" required>
                        <div class="invalid-feedback">Please provide a valid number (≥0).</div>
                    </div>
                    <div class="col-md-6">
                        <label for="price" class="form-label">Price:</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" step="0.01" class="form-control" id="price" name="price" min="0.01" value="<?php echo isset($_POST['price']) ? htmlspecialchars($_POST['price']) : ''; ?>" placeholder="Enter Book Price" required>
                            <div class="invalid-feedback">Please provide a valid price (>₱0).</div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn submit-btn px-4">
                        <i class="bi bi-save"></i> Submit New Book
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function() {
            'use strict';
            const forms = document.querySelectorAll('form');
            
            Array.from(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
</body>
</html>