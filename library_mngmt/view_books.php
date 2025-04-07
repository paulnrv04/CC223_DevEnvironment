<?php
include 'sidebar.php';
include "config.php"; // Database connection

$result = $conn->query("SELECT * FROM books"); // Fetch all books
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
        .book-table {
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .availability-available {
            color: #28a745;
            font-weight: 500;
        }
        .availability-unavailable {
            color: #dc3545;
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
        .library-title {
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
        .table-container {
            flex: 1;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .table {
            border-collapse: collapse;
            width: 100%;
            table-layout: fixed;
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
        .table-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .table-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .fixed-table {
            min-width: 1200px;
        }
        .col-id { width: 5%; }
        .col-title { width: 28%; }
        .col-author { width: 17%; }
        .col-year { width: 10%; }
        .col-category { width: 10%; }
        .col-copies { width: 10%; }
        .col-availability { width: 10%; }
        .col-actions { width: 10%; }
        
        /* Mobile adjustments that don't change original layout */
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
        }
    </style>
</head>
<body>
    <div class="main-content py-4" id="main-content">
        <div class="header w-100 p-3 d-flex justify-content-between align-items-center">
            <h1 class="display-5 fw-bold mb-0 library-title">
                <i class="bi bi-book"></i> Library Books
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
                    <a href="add_book.php" class="btn add-btn bi bi-plus-circle"> Add New Book</a>
                </div>

                <?php
                    $error_message = isset($_GET['error']) ? $_GET['error'] : '';
                    $success_message = isset($_GET['success']) ? $_GET['success'] : '';
                ?>

                <?php if ($error_message): ?>
                    <div class="alert alert-danger text-center mb-0" role="alert">
                        <strong>Error:</strong> <?php echo $error_message; ?>
                    </div>
                <?php elseif ($success_message): ?>
                    <div class="alert alert-success text-center mb-0" role="alert">
                        <strong>Success:</strong> <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>

                <div class="input-group" style="max-width: 250px;">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" id="searchBar" class="form-control" placeholder="Search a Book..." onkeyup="searchBooks()">
                </div>
            </div>
            
            <script>
                function searchBooks() {
                    let input = document.getElementById("searchBar").value.toLowerCase();
                    let rows = document.querySelectorAll(".table tbody tr");

                    rows.forEach(row => {
                        let text = row.innerText.toLowerCase();
                        row.style.display = text.includes(input) ? "" : "none";
                    });
                }
            </script>

            <div class="table-container">
                    <table class="table table-hover mb-0 fixed-table">
                        <colgroup>
                            <col class="col-id"> <col class="col-title">
                            <col class="col-author"> <col class="col-year">
                            <col class="col-category"> <col class="col-copies">
                            <col class="col-availability"> <col class="col-actions">
                        </colgroup>
                        <thead class="table-light">
                            <tr>
                                <th>ID</th> <th>Book Title</th>
                                <th>Author</th> <th>Publish Year</th>
                                <th>Category</th> <th>Copies</th>
                                <th>Availability</th> <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $availabilityClass = ($row['availability'] == 'Available') ? 'availability-available' : 'availability-unavailable';
                                    echo "<tr>
                                            <td class='text-center'>{$row['id']}</td>
                                            <td title='{$row['title']}'>{$row['title']}</td>
                                            <td title='{$row['author']}'>{$row['author']}</td>
                                            <td class='text-center'>{$row['publish_year']}</td>
                                            <td class='text-center' title='{$row['category']}'>{$row['category']}</td>
                                            <td class='text-center'>{$row['available_copy']}</td>
                                            <td class='text-center {$availabilityClass}'>{$row['availability']}</td>
                                            <td class='text-center action-btns'>
                                                <a href='edit_book.php?id={$row['id']}' class='btn btn-sm btn-outline-primary me-1' title='Edit'>
                                                    <i class='bi bi-pencil'></i>
                                                </a>
                                                <a href='delete_book.php?id={$row['id']}' onclick='return confirm(\"Are you sure you want to delete this book?\");' class='btn btn-sm btn-outline-danger' title='Delete'>
                                                    <i class='bi bi-trash'></i>
                                                </a>
                                            </td>
                                        </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8' class='text-center text-muted py-4'>No books found in the library</td></tr>";
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