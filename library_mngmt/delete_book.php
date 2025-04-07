<?php
include "config.php";

if (isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);

    // Check if the book has any existing transactions
    $checkQuery = $conn->query("SELECT COUNT(*) AS count FROM transactions WHERE book_id = $id");
    $checkResult = $checkQuery->fetch_assoc();

    if ($checkResult['count'] > 0) {
        // If the book has transaction, prevent deletion and pass the message
        header("Location: view_books.php?error=This book cannot be deleted because they have existing transactions.");
        exit();
    } else {
        // If the book was never borrowed, allow deletion
        $conn->query("DELETE FROM book WHERE id=$id");
        header("Location: view_books.php?success=Book deleted successfully.");
        exit();
    }
} else {
    header("Location: view_books.php");
    exit();
}
?>