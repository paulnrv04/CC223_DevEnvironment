<?php
include 'sidebar.php';
include "config.php"; // Database connection

// Get borrower_id if provided (e.g., from add_borrower.php redirect)
$borrower_id = isset($_GET["borrower_id"]) ? intval($_GET["borrower_id"]) : null;
// Fetch all borrowers for dropdown
$borrowers_result = $conn->query("SELECT id, first_name, last_name FROM borrower");
// Fetch all books for dropdown
$books_result = $conn->query("SELECT id, title FROM books");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $borrower_id = $_POST["borrower_id"];
    $book_id = $_POST["book_id"];
    $borrow_date = $_POST["borrow_date"];
    $copies = intval($_POST["num_copy"]);
    $due_date = date("Y-m-d", strtotime($borrow_date . " +5 days")); 
    $status = "Borrowed";
    $fine = 0;

    $check_copies_stmt = $conn->prepare("SELECT available_copy FROM books WHERE id = ?");
    $check_copies_stmt->bind_param("i", $book_id);
    $check_copies_stmt->execute();
    $result = $check_copies_stmt->get_result();

    if ($result->num_rows > 0) {
        $book = $result->fetch_assoc();
        $available_copies = $book["available_copy"];

        if ($available_copies >= $copies && $copies > 0) {
            $stmt = $conn->prepare("INSERT INTO transactions (book_id, num_copy, borrower_id, borrow_date, due_date, status, fine) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iiisssi", $book_id, $copies, $borrower_id, $borrow_date, $due_date, $status, $fine);

            if ($stmt->execute()) {
                $new_copies = $available_copies - $copies;
                $update_stmt = $conn->prepare("UPDATE books SET available_copy = ? WHERE id = ?");
                $update_stmt->bind_param("ii", $new_copies, $book_id);
                $update_stmt->execute();
                $update_stmt->close();

                header("Location: transaction.php");
                exit();
            }

            $stmt->close();
        } else {
            $error_message = "Insufficient copies available. Only $available_copies left.";
        }
    }

    $check_copies_stmt->close();
    $conn->close();
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
        .form-label {
            font-weight: 500;
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
        }
    </style>
</head>
<body>
    <div class="main-content py-4" id="main-content">
        <div class="header w-100 p-3 d-flex justify-content-between align-items-center">
            <h2 class="display-5 fw-bold mb-0 title">
                <i class="bi bi-journal-plus"></i> Add New Transaction
            </h2>
            <div>
                <a href="is_existing.php" class="btn go-back-btn">
                    <i class="bi bi-arrow-left"></i> Go Back
                </a>
            </div>
        </div><br>
        
        <div class="form-container">
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form method="POST" novalidate>
                <div class="mb-3">
                    <label for="borrower_id" class="form-label">Borrower:</label>
                    <select class="form-select" id="borrower_id" name="borrower_id" required>
                        <option value="">Select Borrower</option>
                        <?php while ($row = $borrowers_result->fetch_assoc()): ?>
                            <option value="<?= $row['id'] ?>" <?= ($row['id'] == $borrower_id) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($row['first_name'] . " " . $row['last_name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <div class="invalid-feedback">Please select a borrower.</div>
                </div>

                <div class="mb-3">
                    <label for="book_id" class="form-label">Book:</label>
                    <select class="form-select" id="book_id" name="book_id" required>
                        <option value="">Select Book</option>
                        <?php while ($row = $books_result->fetch_assoc()): ?>
                            <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['title']) ?></option>
                        <?php endwhile; ?>
                    </select>
                    <div class="invalid-feedback">Please select a book.</div>
                </div>

                <div class="mb-3">
                    <label for="borrow_date" class="form-label">Borrow Date:</label>
                    <input type="date" class="form-control" id="borrow_date" name="borrow_date" required>
                    <div class="invalid-feedback">Please select a borrow date.</div>
                </div>

                <div class="mb-3">
                    <label for="num_copy" class="form-label">Number of Copies:</label>
                    <input type="number" class="form-control" id="num_copy" name="num_copy" min="1" required>
                    <div class="invalid-feedback">Please enter a valid number of copies.</div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn submit-btn px-4">
                        <i class="bi bi-save"></i> Add Transaction
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Set default borrow date to today
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('borrow_date').value = today;
        });

        // Client-side form validation
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