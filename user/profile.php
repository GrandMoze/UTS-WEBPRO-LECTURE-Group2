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
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
        }
        .header {
            background: linear-gradient(90deg, #ffccdd, #e91e63);
            padding: 20px;
            text-align: center;
            color: #fff;
            font-size: 24px;
            font-weight: bold;
        }
        .profile-container {
            display: flex;
            flex-direction: column;
            max-width: 800px;
            margin: 40px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s;
        }
        .profile-container:hover {
            transform: scale(1.02);
        }
        .profile-picture {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            margin: 20px auto;
            border: 3px solid #e91e63;
        }
        .profile-details {
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .profile-details p {
            margin: 10px 0;
            font-size: 18px;
            color: #333;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
        }
        .profile-details strong {
            display: inline-block;
            width: 150px;
        }
        .btn-group {
            margin-top: 20px;
            text-align: center;
        }
        .btn-link, .btn-primary {
            margin-right: 10px;
        }
        .btn-link {
            color: #e91e63;
            text-decoration: none;
            font-weight: bold;
        }
        .btn-link:hover {
            text-decoration: underline;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            transition: background-color 0.3s, transform 0.3s;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }
        @media (min-width: 768px) {
            .profile-container {
                flex-direction: row;
            }
            .profile-picture {
                margin: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="header">Profil Saya</div>
    <div class="profile-container">
        <img src="<?php echo !empty($user['profile_picture']) ? htmlspecialchars($user['profile_picture']) : '../admin/GAMBAR/blank-profile-picture-973460_1280.webp'; ?>" alt="Foto Profil" class="profile-picture">
        <div class="profile-details">
            <p><strong>Nama:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>No. Telp:</strong> <?php echo htmlspecialchars($user['phone_number']); ?></p>
            <div class="btn-group">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">Edit Profil</button>
                <a href="user_dashboard.php" class="btn-link">Kembali ke Dashboard</a>
            </div>
        </div>  
    </div>

    <!-- Modal -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Edit Profil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="edit_profile.php" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label"><i class="fas fa-user"></i> Nama</label>
                            <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="profile_picture" class="form-label"><i class="fas fa-image"></i> Foto Profil</label>
                            <input type="file" class="form-control" name="profile_picture">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label"><i class="fas fa-envelope"></i> Email</label>
                            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required autocomplete="email">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label"><i class="fas fa-lock"></i> Password Baru</label>
                            <input type="password" class="form-control" name="password" placeholder="Masukkan password baru" autocomplete="new-password">
                        </div>
                        <div class="mb-3">
                            <label for="phone_number" class="form-label"><i class="fas fa-phone"></i> No. Telp</label>
                            <input type="text" class="form-control" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" readonly>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('.btn-primary');
            buttons.forEach(button => {
                button.addEventListener('mouseover', function() {
                    this.style.transform = 'scale(1.1)';
                });
                button.addEventListener('mouseout', function() {
                    this.style.transform = 'scale(1)';
                });
            });
        });
    </script>
</body>
</html>