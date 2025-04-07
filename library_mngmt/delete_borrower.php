<?php
include "config.php";

if (isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);

    // Check if the borrower has any existing transactions
    $checkQuery = $conn->query("SELECT COUNT(*) AS count FROM transactions WHERE borrower_id = $id");
    $checkResult = $checkQuery->fetch_assoc();

    if ($checkResult['count'] > 0) {
        // If the borrower has transactions, prevent deletion and pass the message
        header("Location: view_borrowers.php?error=This borrower cannot be deleted because they have existing transactions.");
        exit();
    } else {
        // If the borrower has never borrowed a book, allow deletion
        $conn->query("DELETE FROM borrower WHERE id=$id");
        header("Location: view_borrowers.php?success=Borrower deleted successfully.");
        exit();
    }
} else {
    header("Location: view_borrowers.php");
    exit();
}
?>