<?php
include 'sidebar.php';
include "config.php"; // Database connection

$sql = "SELECT transactions.id, borrower.first_name, borrower.last_name, books.title, 
        transactions.num_copy, transactions.borrow_date, transactions.due_date, 
        transactions.return_date, transactions.status, transactions.fine 
        FROM transactions
        JOIN borrower ON transactions.borrower_id = borrower.id
        JOIN books ON transactions.book_id = books.id
        ORDER BY transactions.id ASC";

$result = $conn->query($sql);
if (!$result) {
    die("Database Query Failed: " . $conn->error);
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
            margin: 0;
            padding: 0;
            overflow-x: hidden;
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
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 15px;
            }
        }
        .transaction-table {
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .status-overdue {
            color: #dc3545;
            font-weight: 500;
        }
        .status-returned {
            color: #28a745;
            font-weight: 500;
        }
        .status-borrowed {
            color: #fd7e14;
            font-weight: 500;
        }
        .action-btns .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        .header {
            background: #342519;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);
        }
        .transaction-title {
            color: #ede6d9 !important;
        }
        .add-btn {
            background-color: #684f36 !important;
            color: white !important;
        }
        .go-back-btn {
            background-color: #f5efeb !important;
            color: black !important;
        }
        /* Ensure the table always stretches fully */
        .table-container {
            width: 100%;
            overflow-x: auto;
        }
        .table-responsive {
            width: 100%;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed; /* Makes sure table columns stretch */
        }
        .table th, .table td {
            border: 1px solid #dee2e6;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .table thead th {
            background-color: #f5efeb !important;
            color: black !important;
            text-align: center;
            position: sticky;
            top: 0;
        }
        .table-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .col-id { width: 5%; }
        .col-borrower { width: 10%; }
        .col-book { width: 17%; }
        .col-num_copy { width: 10% }
        .col-borrow-date { width: 10%; }
        .col-due-date { width: 10%; }
        .col-return-date { width: 10%; }
        .col-status { width: 10%; }
        .col-fine { width: 10%; }
        .col-actions { width: 8%; }

        @media (max-width: 768px) {
            .action-btns {
                display: flex;
                flex-direction: column;
                gap: 5px;
            }
            .action-btns .btn {
                width: 100%;
            }
            .header h1 {
                font-size: 1.5rem;
            }
            .table-controls {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="main-content py-4" id="main-content">
        <div class="header w-100 p-3 d-flex justify-content-between align-items-center">
            <h1 class="display-5 fw-bold mb-0 transaction-title">
                <i class="bi bi-list-check"></i> Transaction History
            </h1>
            <div>
                <a href="index.php" class="btn go-back-btn">
                    <i class="bi bi-arrow-left"></i> Go Back
                </a>
            </div>
        </div><br>

        <div class="content-wrapper">
            <div class="table-controls">
                <div>
                    <a href="is_existing.php" class="btn add-btn bi bi-plus-circle"> Add Transaction</a>
                </div>
                <div class="input-group" style="max-width: 250px;">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" id="searchBar" class="form-control" placeholder="Search Transactions..." onkeyup="searchTransactions()">
                </div>
            </div>

            <script>
                function searchTransactions() {
                    let input = document.getElementById("searchBar").value.toLowerCase();
                    let rows = document.querySelectorAll(".table tbody tr");

                    rows.forEach(row => {
                        let text = row.innerText.toLowerCase();
                        row.style.display = text.includes(input) ? "" : "none";
                    });
                }
            </script>

            <div class="table-container">
                <table class="table table-hover mb-0">
                    <colgroup>
                        <col class="col-id"> <col class="col-borrower">
                        <col class="col-book"> <col class="col-num_copy">
                        <col class="col-borrow-date"> <col class="col-due-date"> 
                        <col class="col-return-date"> <col class="col-status"> 
                        <col class="col-fine"> <col class="col-actions">
                    </colgroup>
                    <thead class="table-light">
                        <tr>
                            <th>ID</th> <th>Borrower</th>
                            <th>Book</th> <th>Amount</th>
                            <th>Borrow Date</th> <th>Due Date</th>
                            <th>Return Date</th> <th>Status</th>
                            <th>Fine</th> <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $statusClass = ($row['status'] == 'Overdue') ? 'status-overdue' :
                                            (($row['status'] == 'Returned') ? 'status-returned' :
                                            (($row['status'] == 'Borrowed') ? 'status-borrowed' : ''));

                                echo "<tr>
                                        <td class='text-center'>" . htmlspecialchars($row['id']) . "</td>
                                        <td>" . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . "</td>
                                        <td>" . htmlspecialchars($row['title']) . "</td>
                                        <td class='text-center'>" . htmlspecialchars($row['num_copy']) . "</td>
                                        <td class='text-center'>" . htmlspecialchars($row['borrow_date']) . "</td>
                                        <td class='text-center'>" . htmlspecialchars($row['due_date']) . "</td>
                                        <td class='text-center'>" . (!empty($row['return_date']) ? htmlspecialchars($row['return_date']) : "<span class='text-muted'>—</span>") . "</td>
                                        <td class='text-center $statusClass'>" . htmlspecialchars($row['status']) . "</td>
                                        <td class='text-center'>" . ($row['fine'] !== null ? '₱' . number_format($row['fine'], 2) : '—') . "</td>
                                        <td class='text-center action-btns'>
                                            <a href='edit_transaction.php?id=" . urlencode($row['id']) . "' class='btn btn-sm btn-outline-primary me-1'>
                                                <i class='bi bi-pencil'></i>
                                            </a>
                                        </td>
                                    </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='9' class='text-center text-muted py-4'>No transactions found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>