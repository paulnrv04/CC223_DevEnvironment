<?php
include "config.php"; // Database connection
session_start();

$error_message = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    // Fetch admin details
    $stmt = $conn->prepare("SELECT id, username, password FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        if ($password === $row["password"]) { 
            $_SESSION["id"] = $row["id"];
            $_SESSION["username"] = $row["username"];
            header("Location: index.php"); // Redirect after login
            exit();
        } else {
            $error_message = "Invalid username or password.";
        }
    } else {
        $error_message = "Invalid username or password.";
    }

    $stmt->close();
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
        }
        .main-content {
            margin-left: 270px; 
            padding: 20px;
            width: calc(100% - 270px);
            transition: margin-left 0.3s ease, width 0.3s ease;
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
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
    <div class="header w-100 p-3 d-flex justify-content-between align-items-center">
                <h2 class="display-5 fw-bold mb-0 title">
                    <i class="bi bi-book"></i> Library Management System
                </h2>
            </div><br>

            <h2 class="mb-4"><i class="bi bi-person"></i> Admin Login</h2>
            
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Username:</label>
                    <input type="text" class="form-control" name="username" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password:</label>
                    <input type="password" class="form-control" name="password" required>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="submit" class="btn submit-btn px-4">
                        <i class="bi bi-box-arrow-in-right"></i> Log In
                    </button>
                </div>
            </form>
    </div>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
