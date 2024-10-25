<?php
header('Content-Type: application/json');
session_start();
include '../config/koneksi.php';

// Nonaktifkan error reporting ke output
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/php-error.log');

$response = [];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Metode request tidak valid.');
    }

    if (!isset($_POST['event_id']) || !isset($_SESSION['user_id'])) {
        throw new Exception('Data tidak lengkap.');
    }

    $eventId = $_POST['event_id'];
    $userId = $_SESSION['user_id'];

    // Ambil batas maksimal pendaftaran dari tabel events
    $maxQuery = "SELECT max_registrations FROM events WHERE id = ?";
    $maxStmt = $koneksi->prepare($maxQuery);
    if (!$maxStmt) {
        throw new Exception('Gagal mempersiapkan statement max.');
    }

    $maxStmt->bind_param("i", $eventId);
    $maxStmt->execute();
    $maxResult = $maxStmt->get_result();
    $maxRow = $maxResult->fetch_assoc();
    $maxRegistrations = $maxRow['max_registrations'];

    // Cek jumlah pendaftar saat ini
    $countQuery = "SELECT COUNT(*) as count FROM registrations WHERE event_id = ?";
    $countStmt = $koneksi->prepare($countQuery);
    if (!$countStmt) {
        throw new Exception('Gagal mempersiapkan statement count.');
    }

    $countStmt->bind_param("i", $eventId);
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    $countRow = $countResult->fetch_assoc();

    if ($countRow['count'] >= $maxRegistrations) {
        $response['status'] = 'full';
        $response['message'] = 'Acara sudah penuh.';
    } else {
        // Cek apakah user sudah terdaftar di acara
        $checkQuery = "SELECT * FROM registrations WHERE user_id = ? AND event_id = ?";
        $checkStmt = $koneksi->prepare($checkQuery);
        if (!$checkStmt) {
            throw new Exception('Gagal mempersiapkan statement cek.');
        }

        $checkStmt->bind_param("ii", $userId, $eventId);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows > 0) {
            $response['status'] = 'exists';
        } else {
            // Daftarkan user ke acara
            $query = "INSERT INTO registrations (user_id, event_id) VALUES (?, ?)";
            $stmt = $koneksi->prepare($query);
            if (!$stmt) {
                throw new Exception('Gagal mempersiapkan statement.');
            }

            $stmt->bind_param("ii", $userId, $eventId);
            if ($stmt->execute()) {
                $response['status'] = 'success';
            } else {
                throw new Exception('Gagal mengeksekusi query.');
            }
            $stmt->close();
        }
        $checkStmt->close();
    }
    $countStmt->close();
    $maxStmt->close();
} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = $e->getMessage();
    error_log($e->getMessage()); // Log kesalahan
}

$koneksi->close();
echo json_encode($response);
?>