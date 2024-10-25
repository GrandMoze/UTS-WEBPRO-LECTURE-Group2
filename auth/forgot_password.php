<?php
include '../config/koneksi.php';

// Proses reset password
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset_password'])) {
    $username = $_GET['username'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);

    $query = "UPDATE users SET password = ? WHERE name = ?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("ss", $new_password, $username);

    if ($stmt->execute()) {
        // Redirect ke halaman index setelah password berhasil diubah
        header("Location: index.php");
        exit();
    } else {
        echo "Terjadi kesalahan, silakan coba lagi.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f9;
            font-family: 'Roboto', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .reset-password-container {
            max-width: 400px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .btn-primary {
            background-color: #3498db;
            border: none;
            transition: background-color 0.3s;
        }
        .btn-primary:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="reset-password-container">
        <h1>Reset Password</h1>
        <form method="post">
            <div class="form-group">
                <input type="password" name="new_password" class="form-control" placeholder="Password Baru" required>
            </div>
            <button type="submit" name="reset_password" class="btn btn-primary btn-block">Ubah Password</button>
        </form>
    </div>
</body>
</html>