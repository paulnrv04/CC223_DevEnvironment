<?php
include 'sidebar.php';
include "config.php";

$id = $_GET['id'] ?? null; // Ensure id is set
if (!$id || !is_numeric($id)) {
    die("Invalid Book ID");
}

$result = $conn->query("SELECT * FROM books WHERE id=$id");
if (!$result) {
    die("Database error: " . $conn->error);
}

if ($result->num_rows == 0) {
    die("Book not found");
}

$row = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string($_POST["title"] ?? '');
    $author = $conn->real_escape_string($_POST["author"] ?? '');
    $category = $conn->real_escape_string($_POST["category"] ?? '');
    $publish_year = $conn->real_escape_string($_POST["publish_year"] ?? '');
    $available_copy = intval($_POST["available_copy"] ?? 0);
    $availability = ($available_copy > 0) ? "Available" : "Unavailable";
    $price = floatval($_POST["price"] ?? 0);

    // Check for changes
    $has_changes = 
        $title != $row['title'] || $author != $row['author'] ||
        $category != $row['category'] || $publish_year != $row['publish_year'] ||
        $available_copy != $row['available_copy'] || $price != $row['price'];

    if (!$has_changes) {
        $no_changes_message = "No changes made.";
    } else {
        $sql = "UPDATE books SET title='$title', author='$author', category='$category', 
                    publish_year='$publish_year', available_copy='$available_copy', availability='$availability',
                    price='$price'WHERE id=$id";

        if ($conn->query($sql) === TRUE) {
            if ($conn->affected_rows > 0) {
                header("Location: view_books.php?updated=1");
                exit();
            } else {
                $no_changes_message = "No changes were made to the book.";
            }
        } else {
            echo "<div class='alert alert-danger'>Error updating book: " . htmlspecialchars($conn->error) . "</div>";
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
        .form-container {
            transition: margin-left 0.3s ease;
            margin: 0 auto;
            background: white;
            padding: 2rem;
            border-radius: 0.25rem;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
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
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 15px;
            }
            .form-container {
                padding: 1.5rem;
            }
            .header h2 {
                font-size: 1.5rem;
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
                <i class="bi bi-book"></i> Edit Book
            </h2>
            <div>
                <a href="view_books.php" class="btn go-back-btn">
                    <i class="bi bi-arrow-left"></i> Go Back
                </a>
            </div>
        </div><br>

        <div class="form-container">
            <?php if (isset($no_changes_message)): ?>
                <div class="alert alert-warning"><?php echo $no_changes_message; ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Title:</label> 
                    <input type="text" class="form-control" name="title" value="<?= htmlspecialchars($row['title']) ?>" required>

                    <label class="form-label mt-3">Author:</label>
                    <input type="text" class="form-control" name="author" value="<?= htmlspecialchars($row['author']) ?>" required>
                </div>

                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label class="form-label">Publish Year:</label>
                        <input type="number" class="form-control" name="publish_year" value="<?= htmlspecialchars($row['publish_year']) ?>" min="1000" max="<?= date('Y') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Category:</label>
                        <select class="form-select" name="category" required>
                            <option value="">Select Category</option>
                            <option value="Adventure" <?= ($row['category'] == 'Adventure') ? 'selected' : '' ?>>Adventure</option>
                            <option value="Art" <?= ($row['category'] == 'Art') ? 'selected' : '' ?>>Art</option>
                            <option value="Biography" <?= ($row['category'] == 'Biography') ? 'selected' : '' ?>>Biography</option>
                            <option value="Business" <?= ($row['category'] == 'Business') ? 'selected' : '' ?>>Business</option>
                            <option value="Cooking" <?= ($row['category'] == 'Cooking') ? 'selected' : '' ?>>Cooking</option>
                            <option value="Fiction" <?= ($row['category'] == 'Fiction') ? 'selected' : '' ?>>Fiction</option>
                            <option value="Health" <?= ($row['category'] == 'Health') ? 'selected' : '' ?>>Health</option>
                            <option value="History" <?= ($row['category'] == 'History') ? 'selected' : '' ?>>History</option>
                            <option value="Horror" <?= ($row['category'] == 'Horror') ? 'selected' : '' ?>>Horror</option>
                            <option value="Non-Fiction" <?= ($row['category'] == 'Non-Fiction') ? 'selected' : '' ?>>Non-Fiction</option>
                            <option value="Philosophy" <?= ($row['category'] == 'Philosophy') ? 'selected' : '' ?>>Philosophy</option>
                            <option value="Poetry" <?= ($row['category'] == 'Poetry') ? 'selected' : '' ?>>Poetry</option>
                            <option value="Psychology" <?= ($row['category'] == 'Psychology') ? 'selected' : '' ?>>Psychology</option>
                            <option value="Romance" <?= ($row['category'] == 'Romance') ? 'selected' : '' ?>>Romance</option>
                            <option value="Science" <?= ($row['category'] == 'Science') ? 'selected' : '' ?>>Science</option>
                            <option value="Self-Help" <?= ($row['category'] == 'Self-Help') ? 'selected' : '' ?>>Self-Help</option>
                            <option value="Technology" <?= ($row['category'] == 'Technology') ? 'selected' : '' ?>>Technology</option>
                            <option value="Travel" <?= ($row['category'] == 'Travel') ? 'selected' : '' ?>>Travel</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label class="form-label">Available Copies:</label>
                        <input type="number" class="form-control" name="available_copy" value="<?= htmlspecialchars($row['available_copy']) ?>" min="0" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Price:</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" step="0.01" class="form-control" name="price" value="<?= htmlspecialchars($row['price']) ?>" min="0.01" required>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn submit-btn px-4">
                        <i class="bi bi-save"></i> Update Book
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>