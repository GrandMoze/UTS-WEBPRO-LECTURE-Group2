<?php
include '../config/koneksi.php';

session_start();
if ($_SESSION['role'] != 'user') {
    header("Location: ../auth/login.php");
    exit();
}

// Ambil data pengguna dari database
$userId = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Proses update profil
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $phone_number = $_POST['phone_number']; // Tambahkan ini

    // Proses upload foto (opsional)
    $target_file = $user['profile_picture'];
    if (!empty($_FILES["profile_picture"]["name"])) {
        $target_dir = "../admin/uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
        move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file);
    }

    $query = "UPDATE users SET name = ?, phone_number = ?, profile_picture = ? WHERE id = ?"; // Tambahkan phone_number
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("sssi", $name, $phone_number, $target_file, $userId); // Tambahkan phone_number
    $stmt->execute();

    // Redirect setelah update berhasil
    header("Location: profile.php");
    exit();
}
?>