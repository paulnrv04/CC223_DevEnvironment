<?php
include 'sidebar.php';
include "config.php";

$id = $_GET['id'] ?? null; // Ensure id is set
if (!$id) {
    die("Invalid User ID");
}

// Fetch user details from the database
$result = $conn->prepare("SELECT * FROM borrower WHERE id = ?");
$result->bind_param("i", $id);
$result->execute();
$data = $result->get_result();

if ($data->num_rows == 0) {
    die("User not found");
}

$row = $data->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $conn->real_escape_string($_POST["first_name"]);
    $last_name = $conn->real_escape_string($_POST["last_name"]);
    $contact_num = $conn->real_escape_string($_POST["contact_num"]);
    $email = $conn->real_escape_string($_POST["email"]);

    if ( // Check if there are changes
        $first_name === $row['first_name'] && $last_name === $row['last_name'] &&
        $contact_num === $row['contact_num'] && $email === $row['email']
    ) {
        $message = "<p class='alert alert-info'>No changes were made.</p>";
    } else {
        $stmt = $conn->prepare("UPDATE borrower SET first_name=?, last_name=?, contact_num=?, email=? WHERE id=?");
        $stmt->bind_param("ssssi", $first_name, $last_name, $contact_num, $email, $id);

        if ($stmt->execute()) {
            header("Location: view_borrowers.php");
            exit();
        } else {
            $message = "<p class='alert alert-danger'>Error updating user: " . $stmt->error . "</p>";
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
        }
    </style>
</head>
<body>
    <div class="main-content py-4" id="main-content">
        <div class="header w-100 p-3 d-flex justify-content-between align-items-center">
            <h2 class="display-5 fw-bold mb-0 title">
                <i class="bi bi-person"></i> Update User Information
            </h2>
            <a href="view_borrowers.php" class="btn go-back-btn">
                <i class="bi bi-arrow-left"></i> Go Back
            </a>
        </div><br>

        <div class="form-container">
            <?php if (isset($message)) echo $message; ?>
            <form method="post">
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label for="first_name" class="form-label">First Name:</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" value="<?= htmlspecialchars($row['first_name']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="last_name" class="form-label">Last Name:</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" value="<?= htmlspecialchars($row['last_name']) ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="contact_num" class="form-label">Contact Number:</label>
                    <input type="text" class="form-control" id="contact_num" name="contact_num" value="<?= htmlspecialchars($row['contact_num']) ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($row['email']) ?>" required>
                </div>

                <div class="info-item">
                    <span class="info-label">Registration Date:</span>
                    <span class="info-value"><?= htmlspecialchars($row['register_date']) ?></span>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn submit-btn px-4">
                        <i class="bi bi-save"></i> Update User
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Set default return date to today
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('return_date').value = today;
        });
    </script>
</body>
</html>
