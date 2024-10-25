<?php
session_start();
include '../config/koneksi.php';

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // Update status to inactive
    $updateStatusQuery = "UPDATE users SET status = 'inactive' WHERE id = ?";
    $stmt = $koneksi->prepare($updateStatusQuery);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->close();

    session_destroy();
}

header("Location: ../index.php");
exit();
?>