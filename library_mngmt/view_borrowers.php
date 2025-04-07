<?php
include 'sidebar.php';
include "config.php"; // Database connection

$result = $conn->query("SELECT * FROM borrower");
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
        .borrower-table {
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .table-responsive {
            overflow-x: auto;
        }
        .action-btns .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        .header {
            background: #342519;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);
        }
        .borrower-title {
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
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .table-responsive {
            flex: 1;
            overflow: auto;
        }
        .table {
            border-collapse: collapse;
            width: 100%;
            table-layout: fixed;
            shadow: 0 0 15px rgba(0,0,0,0.1);
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
        .fixed-table {
            min-width: 1000px;
        }
        .col-id { width: 5%; }
        .col-user { width: 30%; }
        .col-contact-num { width: 20%; }
        .col-email { width: 20%; }
        .col-reg-date { width: 15% }
        .col-action { width: 10%; }
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 15px;
            }
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
            <h1 class="display-5 fw-bold mb-0 borrower-title">
                <i class="bi bi-people-fill"></i> User Information
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
                <a href="add_borrower.php" class="btn add-btn bi bi-plus-circle"> Add New User</a>
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
                <input type="text" id="searchBar" class="form-control" placeholder="Search a User..." onkeyup="searchBorrowers()">
            </div>
        </div>
            
        <script>
            function searchBorrowers() {
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
                    <col class="col-id"> <col class="col-user">
                    <col class="col-contact-num"> <col class="col-email">
                    <col class="col-reg-date"> <col class="col-action">
                </colgroup>
                <thead class="table-light">
                    <tr>
                        <th>ID</th> <th>Full Name</th>
                        <th>Contact Number</th> <th>Email Address</th>
                        <th>Date Registered</th> <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $fullName = "{$row['first_name']} {$row['last_name']}";
                            echo "<tr>
                                    <td class='text-center'>{$row['id']}</td>
                                    <td>{$fullName}</td>
                                    <td class='text-center'>{$row['contact_num']}</td>
                                    <td class='text-center'>{$row['email']}</td>
                                    <td class='text-center'>{$row['register_date']}</td>
                                    <td class='text-center action-btns'>
                                        <a href='edit_borrower.php?id={$row['id']}' class='btn btn-sm btn-outline-primary me-1' title='Edit'>
                                            <i class='bi bi-pencil'></i>
                                        </a>
                                        <a href='delete_borrower.php?id={$row['id']}' onclick='return confirm(\"Are you sure you want to delete this user?\");' class='btn btn-sm btn-outline-danger' title='Delete'>
                                            <i class='bi bi-trash'></i>
                                        </a>
                                    </td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center text-muted py-4'>No borrowers found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
