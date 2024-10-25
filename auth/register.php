<?php
include '../config/koneksi.php';

// Proses registrasi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $phone_number = $_POST['phone_number']; // Tambahkan ini

    // Proses upload foto (opsional)
    $target_file = "";
    if (!empty($_FILES["profile_picture"]["name"])) {
        $target_dir = "../admin/uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
        move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file);
    }

    $role = 'user'; // Set default role
    $query = "INSERT INTO users (name, email, password, phone_number, profile_picture, role) VALUES (?, ?, ?, ?, ?, ?)"; // Tambahkan phone_number
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("ssssss", $name, $email, $password, $phone_number, $target_file, $role); // Tambahkan phone_number
    $stmt->execute();

    // Redirect setelah registrasi berhasil
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Arial', sans-serif;
        }
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
        }
        .card-header {
            background: #007bff;
            color: white;
            text-align: center;
            padding: 20px;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            transition: background-color 0.3s, transform 0.3s;
            border-radius: 50px;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }
        .form-control {
            border-radius: 50px;
            margin-bottom: 15px;
        }
        .form-control-file {
            border-radius: 50px;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ced4da;
            width: 100%;
            box-sizing: border-box;
        }
        .form-control-file:focus {
            box-shadow: none;
            border-color: #007bff;
        }
        .links {
            text-align: center;
            margin-top: 10px;
        }
        .links a {
            color: #007bff;
            text-decoration: none;
            margin: 0 10px;
        }
        .links a:hover {
            text-decoration: underline;
        }
    </style>
    <title>Registrasi</title>
</head>
<body>
<div class="card">
    <div class="card-header">
        <h2>Registrasi</h2>
    </div>
    <div class="card-body p-4">
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <input type="text" class="form-control" name="name" placeholder="Nama" required>
            </div>
            <div class="form-group">
                <input type="email" class="form-control" name="email" placeholder="Email" required>
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Password" required>
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="phone_number" placeholder="Nomor Telepon" required> <!-- Tambahkan ini -->
            </div>
            <div class="form-group">
                <input type="file" class="form-control-file" name="profile_picture">
            </div>
            <button type="submit" name="register" class="btn btn-primary btn-block">Registrasi</button>
            <div class="links">
                <a href="../index.php">Kembali</a>
            </div>
        </form>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>