<?php
include '../config/koneksi.php';

$showResetForm = false;
$errorMessage = '';

// Proses lupa password
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['lupa_password'])) {
    $name = $_POST['username'];
    $email = $_POST['email'];

    $query = "SELECT * FROM users WHERE name = ? AND email = ?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("ss", $name, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $showResetForm = true;
    } else {
        $errorMessage = "Nama atau email tidak ditemukan!";
    }
}

// Proses reset password
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset_password'])) {
    $username = $_POST['username'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);

    $query = "UPDATE users SET password = ? WHERE name = ?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("ss", $new_password, $username);

    if ($stmt->execute()) {
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
    <title>Lupa Password - Penjualan Tiket</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea, #764ba2);
            font-family: 'Roboto', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            position: relative;
        }
        .forgot-password-container {
            background-color: rgba(0, 0, 0, 0.8);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.5);
            text-align: center;
            color: #fff;
            max-width: 400px;
            width: 100%;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .forgot-password-container:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
        }
        h1 {
            font-size: 2.5em;
            margin-bottom: 30px;
            font-weight: bold;
        }
        .form-control {
            background-color: #444;
            border: none;
            color: #fff;
            border-radius: 5px;
            padding-left: 40px;
            position: relative;
        }
        .form-control::placeholder {
            color: #bbb;
        }
        .form-group {
            position: relative;
            margin-bottom: 25px;
        }
        .form-group i {
            position: absolute;
            left: 10px;
            top: 10px;
            color: #bbb;
        }
        .btn-primary {
            background-color: #5a67d8;
            border: none;
            border-radius: 25px;
            padding: 12px 25px;
            transition: background-color 0.3s, transform 0.3s;
        }
        .btn-primary:hover {
            background-color: #434190;
            transform: translateY(-3px);
        }
        .link {
            color: #fff;
            position: absolute;
            top: 20px;
            left: 20px;
            font-weight: bold;
            padding: 10px 15px;
            background-color: #5a67d8;
            border-radius: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            transition: background-color 0.3s, box-shadow 0.3s;
        }
        .link:hover {
            background-color: #434190;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.5);
        }
        .alert {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8d7da;
            color: #721c24;
            border-radius: 5px;
            border: 1px solid #f5c6cb;
        }
        @media (max-width: 576px) {
            .forgot-password-container {
                padding: 30px;
            }
            .link {
                top: 10px;
                left: 10px;
            }
        }
    </style>
</head>
<body>
    <a style="text-decoration: none;" href="../index.php" class="link">Kembali ke Login</a>
    <div class="forgot-password-container">
        <h1>Lupa Password</h1>
        <?php if ($errorMessage): ?>
            <div class="alert"><?php echo $errorMessage; ?></div>
        <?php endif; ?>
        <?php if (!$showResetForm): ?>
            <form method="post">
                <div class="form-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" class="form-control" placeholder="Nama" required>
                </div>
                <div class="form-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                </div>
                <button type="submit" name="lupa_password" class="btn btn-primary btn-block">Kirim Instruksi Reset</button>
            </form>
        <?php else: ?>
            <form method="post">
                <input type="hidden" name="username" value="<?php echo htmlspecialchars($name); ?>">
                <div class="form-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="new_password" class="form-control" placeholder="Password Baru" required>
                </div>
                <button type="submit" name="reset_password" class="btn btn-primary btn-block">Ubah Password</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>