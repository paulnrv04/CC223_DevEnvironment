<?php
include 'sidebar.php';
include "config.php";

$error_message = ""; $success_message = "";
$first_name = $last_name = $contact_num = $email = $register_date = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $contact_num = "09" . trim($_POST["contact_num"]);
    $email = trim($_POST["email"]);
    $register_date = trim($_POST["register_date"]);

    // Check if all fields are filled
    if (empty($first_name) || empty($last_name) || empty($_POST["contact_num"]) || empty($email) || empty($register_date)) {
        $error_message = "All fields are required!";
    } elseif (!preg_match("/^09[0-9]{9}$/", $contact_num)) { // Validate contact number
        $error_message = "Invalid contact number! Must start with 09 and be exactly 11 digits.";
    } else { // Check for duplicates
        $stmt = $conn->prepare("SELECT contact_num, email FROM borrower WHERE contact_num = ? OR email = ?");
        $stmt->bind_param("ss", $contact_num, $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $error_message = "The contact number or email is already in use!";
        } else {
            $stmt = $conn->prepare("INSERT INTO borrower (first_name, last_name, contact_num, email, register_date) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $first_name, $last_name, $contact_num, $email, $register_date);            
            
            if ($stmt->execute()) {
                $success_message = "Borrower added successfully!";
                
                // Check if 'redirect' parameter is set in the URL
                if (isset($_GET['redirect'])) {
                    $redirect_url = $_GET['redirect'];
                    header("Location: $redirect_url");  // Redirect to add_transaction.php
                    exit();
                } else {
                    header("Location: view_borrowers.php");  // Redirect to view_borrowers.php
                    exit();
                }
            } else {
                $error_message = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
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
                <i class="bi bi-people-fill"></i> Add New User
            </h2>
            <div>
                <a href="view_borrowers.php" class="btn go-back-btn">
                    <i class="bi bi-arrow-left"></i> Go Back
                </a>
            </div>
        </div><br>
        
        <div class="form-container">
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form method="POST" novalidate>
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label for="first_name" class="form-label">First Name:</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" placeholder="Enter First Name" required>
                        <div class="invalid-feedback">Please provide a first name.</div>
                    </div>
                    <div class="col-md-6">
                        <label for="last_name" class="form-label">Last Name:</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" placeholder="Enter Last Name" required>
                        <div class="invalid-feedback">Please provide a last name.</div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="contact_num" class="form-label">Contact Number:</label>
                    <div class="input-group">
                        <span class="input-group-text">09</span>
                        <input type="text" class="form-control" id="contact_num" name="contact_num" pattern="[0-9]{9}" placeholder="Enter remaining 9 digits" required maxlength="9" value="<?php echo isset($_POST['contact_num']) ? htmlspecialchars($_POST['contact_num']) : ''; ?>">
                    </div>
                    <div class="invalid-feedback">Please provide a valid contact number.</div>
                    <small class="text-muted">Must be exactly 11 digits (09 + 9 digits).</small>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="Enter Email Address" required>
                    <div class="invalid-feedback">Please provide a valid email address.</div>
                </div>

                <div class="mb-3">
                    <label for="register_date" class="form-label">Registration Date:</label>
                    <input type="date" class="form-control" id="register_date" name="register_date" required>
                    <div class="invalid-feedback">Please select a registration date.</div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn submit-btn px-4">
                        <i class="bi bi-save"></i> Register
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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