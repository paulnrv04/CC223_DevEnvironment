<?php 
include 'sidebar.php';
include "config.php";

if (!isset($_GET["id"])) {
    die("Transaction ID not provided.");
}

$transaction_id = intval($_GET["id"]);
$transaction_result = $conn->query("SELECT * FROM transactions WHERE id = $transaction_id");
$transaction = $transaction_result->fetch_assoc();

// Fetch borrower name and book title and price
$borrower_result = $conn->query("SELECT first_name, last_name FROM borrower WHERE id = " . $transaction["borrower_id"]);
$borrower = $borrower_result->fetch_assoc();
$borrower_name = $borrower["first_name"] . " " . $borrower["last_name"];
$book_result = $conn->query("SELECT title, price FROM books WHERE id = " . $transaction["book_id"]);
$book = $book_result->fetch_assoc();
$book_title = $book["title"]; $book_price = $book["price"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $return_date = $_POST["return_date"];
    $borrow_date = $transaction["borrow_date"];
    $due_date = $transaction["due_date"];

    $return_timestamp = strtotime($return_date); $due_timestamp = strtotime($due_date);
    
    if ($return_timestamp <= $due_timestamp) {
        $status = "Returned"; $fine = 0;
    } else {
        $status = "Overdue";
        $days_late = ceil(($return_timestamp - $due_timestamp) / (60 * 60 * 24));
        $fine = round($days_late * ($book_price * 0.05), 2); // 5% of book price per day
    }

    $stmt = $conn->prepare("UPDATE transactions SET return_date = ?, status = ?, fine = ? WHERE id = ?");
    $stmt->bind_param("ssdi", $return_date, $status, $fine, $transaction_id);

    if ($stmt->execute()) {
        $book_id = $transaction["book_id"]; $copies = $transaction["num_copy"];
        // Return the num_copy back to available_copy
        $update_query = $conn->prepare("UPDATE books SET available_copy = available_copy + ? WHERE id = ?");
        $update_query->bind_param("ii", $copies, $book_id);
        $update_query->execute();
        $update_query->close();

        header("Location: transaction.php");
        exit();
    } else {
        echo "<p style='color: red;'>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Transaction</title>
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
        .info-item {
            margin-bottom: 1rem;
        }
        .info-label {
            font-weight: 500;
            display: block;
            margin-bottom: 0.25rem;
        }
        .info-value {
            padding: 0.375rem 0.75rem;
            background-color: #f8f9fa;
            border-radius: 0.25rem;
            display: block;
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
            .info-item {
                margin-bottom: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="main-content py-4" id="main-content">
        <div class="header w-100 p-3 d-flex justify-content-between align-items-center">
            <h2 class="display-5 fw-bold mb-0 title">
                <i class="bi bi-person"></i> Update User Transaction
            </h2>
            <div>
                <a href="transaction.php" class="btn go-back-btn">
                    <i class="bi bi-arrow-left"></i> Go Back
                </a>
            </div>
        </div><br>
        
        <div class="form-container">
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger mb-4"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="transaction-info mb-4">
                    <div class="info-item">
                        <span class="info-label">Borrower:</span>
                        <span class="info-value"><?= htmlspecialchars($borrower_name) ?></span>
                    </div>
                    
                    <div class="info-item">
                        <span class="info-label">Book:</span>
                        <span class="info-value"><?= htmlspecialchars($book_title) ?></span>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 info-item">
                            <span class="info-label">Borrow Date:</span>
                            <span class="info-value"><?= htmlspecialchars($transaction["borrow_date"]) ?></span>
                        </div>
                        <div class="col-md-6 info-item">
                            <span class="info-label">Due Date:</span>
                            <span class="info-value"><?= htmlspecialchars($transaction["due_date"]) ?></span>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 info-item">
                            <span class="info-label">Book Price:</span>
                            <span class="info-value">₱<?= number_format($book_price, 2) ?></span>
                        </div>
                        <div class="col-md-6 info-item">
                            <label for="return_date" class="form-label">Return Date:</label>
                            <input type="date" class="form-control" id="return_date" name="return_date" required 
                                   value="<?= isset($transaction['return_date']) ? htmlspecialchars($transaction['return_date']) : date('Y-m-d') ?>">
                            <small class="text-muted">Late returns will incur a 5% daily fine based on book price (₱<?= number_format($book_price * 0.05, 2) ?> per day)</small>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn submit-btn px-4">
                        <i class="bi bi-save"></i> Update Transaction
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Set default return date to today if not already set
        document.addEventListener('DOMContentLoaded', function() {
            const returnDateInput = document.getElementById('return_date');
            if (!returnDateInput.value) {
                const today = new Date().toISOString().split('T')[0];
                returnDateInput.value = today;
            }
            
            // Add event listener to show estimated fine when date changes
            returnDateInput.addEventListener('change', function() {
                const dueDate = new Date("<?= $transaction['due_date'] ?>");
                const returnDate = new Date(this.value);
                
                if (returnDate > dueDate) {
                    const daysLate = Math.ceil((returnDate - dueDate) / (1000 * 60 * 60 * 24));
                    const dailyFine = <?= $book_price * 0.05 ?>;
                    const totalFine = daysLate * dailyFine;
                }
            });
        });
    </script>
</body>
</html>