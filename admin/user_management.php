<?php
session_start();
include '../config/koneksi.php';

// Periksa apakah session role sudah diatur
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// Fungsi untuk menghapus pengguna
if (isset($_GET['delete_user'])) {
    $userId = intval($_GET['delete_user']);
    $deleteQuery = "DELETE FROM users WHERE id = ?";
    $stmt = $koneksi->prepare($deleteQuery);
    $stmt->bind_param("i", $userId);
    if ($stmt->execute()) {
        echo "User deleted successfully.";
    } else {
        echo "Error deleting user.";
    }
    $stmt->close();
    header("Location: user_management.php");
    exit();
}

// Ambil data pengguna dan event yang terdaftar, hanya untuk pengguna dengan role 'user'
$query = "SELECT users.id, users.name, users.email, GROUP_CONCAT(events.name SEPARATOR ', ') AS registered_events
          FROM users
          LEFT JOIN registrations ON users.id = registrations.user_id
          LEFT JOIN events ON registrations.event_id = events.id
          WHERE users.role = 'user' 
          GROUP BY users.id";
$result = $koneksi->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(120deg, #f6d365, #fda085);
            animation: gradientAnimation 10s ease infinite;
        }
        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        h2 {
            text-align: center;
            margin-top: 20px;
            color: #fff;
            font-size: 2em;
        }
        .search-container {
            text-align: center;
            margin: 20px;
        }
        .search-input {
            padding: 10px;
            width: 80%;
            max-width: 400px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .table {
            width: 90%;
            margin: 0 auto;
            padding: 0;
            border-collapse: collapse;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s;
        }
        .table:hover {
            transform: scale(1.02);
        }
        .btn {
            padding: 10px 15px;
            color: #fff;
            background-color: #d9534f;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s;
            font-size: 0.9em;
        }
        .btn:hover {
            background-color: #c9302c;
        }
        .btn-back {
            display: inline-block;
            margin: 20px;
            background-color: #5bc0de;
        }
        .btn-back:hover {
            background-color: #31b0d5;
        }
        @media (max-width: 768px) {
            h2 {
                font-size: 1.5em;
            }
            .search-input {
                width: 90%;
                padding: 8px;
            }
            .btn {
                padding: 5px 10px;
                font-size: 0.8em;
            }
            .table th, .table td {
                padding: 10px;
                font-size: 0.8em;
            }
        }
        @media (max-width: 576px) {
            h2 {
                font-size: 1.2em;
            }
            .search-input {
                width: 95%;
                padding: 6px;
            }
            .btn {
                padding: 4px 8px;
                font-size: 0.7em;
            }
            .table th, .table td {
                padding: 8px;
                font-size: 0.7em;
            }
        }
        .export-form {
        text-align: center;
        margin: 20px 0;
    }
    .btn-export {
        padding: 10px 20px;
        color: #fff;
        background-color: #5cb85c;
        border: none;
        border-radius: 5px;
        text-decoration: none;
        transition: background-color 0.3s;
        font-size: 1em;
    }
    .btn-export:hover {
        background-color: #4cae4c;
    }
    </style>
</head>
<body>

<h2>User Management</h2>

<div class="search-container">
    <input type="text" id="searchInput" class="form-control search-input" placeholder="Search by name...">
</div>

<a href="admin_dashboard.php" class="btn btn-back">Back</a>

<div class="table-responsive">
    <table class="table table-bordered mt-3" id="userTable">
        <thead class="thead-light">
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Registered Events</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = $result->fetch_assoc()): ?>
            <tr>
                <td data-label="Name"><?php echo htmlspecialchars($user['name']); ?></td>
                <td data-label="Email"><?php echo htmlspecialchars($user['email']); ?></td>
                <td data-label="Registered Events"><?php echo htmlspecialchars($user['registered_events'] ?: 'None'); ?></td>
                <td data-label="Actions">
                    <a href="?delete_user=<?php echo $user['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <form action="export_user.php" method="post" class="export-form">
        <button type="submit" name="export" class="btn btn-export">Export Users</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        var input = this.value.toLowerCase();
        var rows = document.querySelectorAll('#userTable tbody tr');

        rows.forEach(function(row) {
            var name = row.querySelector('td[data-label="Name"]').textContent.toLowerCase();
            if (name.includes(input)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    document.querySelectorAll('#userTable tbody tr').forEach(function(row) {
        row.addEventListener('mouseover', function() {
            this.style.backgroundColor = '#f1f1f1';
        });
        row.addEventListener('mouseout', function() {
            this.style.backgroundColor = '';
        });
    });
</script>

</body>
</html>