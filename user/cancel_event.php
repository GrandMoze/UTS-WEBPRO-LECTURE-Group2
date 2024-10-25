<?php
include '../config/koneksi.php';

session_start(); // Pastikan session dimulai
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventId = $_POST['event_id'];
    $userId = $_SESSION['user_id']; // Pastikan session user_id tersedia

    // Logika untuk membatalkan pendaftaran acara
    $query = "DELETE FROM registrations WHERE event_id = ? AND user_id = ?";
    $stmt = $koneksi->prepare($query);

    if ($stmt) {
        $stmt->bind_param("ii", $eventId, $userId);
        if ($stmt->execute()) {
            echo "Pendaftaran dibatalkan!";
        } else {
            http_response_code(500);
            echo "Terjadi kesalahan saat membatalkan pendaftaran.";
        }
        $stmt->close();
    } else {
        http_response_code(500);
        echo "Terjadi kesalahan dalam persiapan pernyataan.";
    }

    $koneksi->close();
}
?>