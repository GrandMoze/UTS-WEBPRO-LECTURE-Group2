<?php
session_start();
include '../config/koneksi.php';

// Periksa apakah session role sudah diatur
if (!isset($_SESSION['role'])) {
    header("Location: ../index.php");
    exit();
}

// Periksa nilai session role
if ($_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// ... existing code ...

// Fungsi untuk menambahkan event baru
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_event'])) {
    $event_name = $_POST['event_name'];
    $event_date = $_POST['event_date'];
    $event_location = $_POST['event_location'];
    $description = $_POST['description']; 
    $max_registrations = $_POST['max_registrations'];
    
    // Proses upload gambar
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    $target_file = $target_dir . time() . '_' . basename($_FILES["event_image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Periksa apakah file adalah gambar
    if (isset($_FILES["event_image"]) && $_FILES["event_image"]["error"] == 0) {
        $check = getimagesize($_FILES["event_image"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'File bukan gambar.',
                    confirmButtonText: 'OK'
                });
            </script>";
            $uploadOk = 0;
        }

        // Periksa jika file sudah ada
        if (file_exists($target_file)) {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Maaf, file sudah ada.',
                    confirmButtonText: 'OK'
                });
            </script>";
            $uploadOk = 0;
        }

        // Periksa ukuran file
        if ($_FILES["event_image"]["size"] > 50000000) {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Maaf, ukuran file terlalu besar.',
                    confirmButtonText: 'OK'
                });
            </script>";
            $uploadOk = 0;
        }

        // Hanya izinkan format tertentu
        if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Maaf, hanya file JPG, JPEG, PNG & GIF yang diperbolehkan.',
                    confirmButtonText: 'OK'
                });
            </script>";
            $uploadOk = 0;
        }

        // Periksa jika $uploadOk adalah 0 karena error
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["event_image"]["tmp_name"], $target_file)) {
                $query = "INSERT INTO events (name, date, location, description, image, max_registrations) VALUES (?, ?, ?, ?, ?, ?)"; 
                $stmt = $koneksi->prepare($query);
                $stmt->bind_param("sssssi", $event_name, $event_date, $event_location, $description, $target_file, $max_registrations); 
                
                if ($stmt->execute()) {
                    echo "<script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Event berhasil ditambahkan!',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'admin_dashboard.php';
                            }
                        });
                    </script>";
                    // Redirect setelah sukses
                    header("Location: admin_dashboard.php");
                    exit();
                } else {
                    echo "<script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error: " . $stmt->error . "',
                            confirmButtonText: 'OK'
                        });
                    </script>";
                }
                $stmt->close();
            } else {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Maaf, terjadi kesalahan saat mengupload file.',
                        confirmButtonText: 'OK'
                    });
                </script>";
            }
        }
    }
}

// ... existing code ...
// Fungsi untuk mengedit event
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_event'])) {
    $event_id = $_POST['event_id'];
    $event_name = $_POST['event_name'];
    $event_date = $_POST['event_date'];
    $event_location = $_POST['event_location'];
    $description = $_POST['description']; 
    $max_registrations = $_POST['max_registrations'];

    $query = "UPDATE events SET name=?, date=?, location=?, description=?, max_registrations=? WHERE id=?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("ssssii", $event_name, $event_date, $event_location, $description, $max_registrations, $event_id); 

    if ($stmt->execute()) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Event berhasil diperbarui!',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'admin_dashboard.php';
                }
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error: " . $stmt->error . "',
                confirmButtonText: 'OK'
            });
        </script>";
    }
    $stmt->close();
}

// Fungsi untuk menghapus event
if (isset($_GET['delete_event'])) {
    $event_id = $_GET['delete_event'];
    $query = "DELETE FROM events WHERE id=?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("i", $event_id);

    if ($stmt->execute()) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Event berhasil dihapus!',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'admin_dashboard.php';
                }
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error: " . $stmt->error . "',
                confirmButtonText: 'OK'
            });
        </script>";
    }
    $stmt->close();
}

// Ambil data event dan jumlah pendaftar
$events = $koneksi->query("SELECT e.*, COUNT(r.id) as registrations_count FROM events e LEFT JOIN registrations r ON e.id = r.event_id GROUP BY e.id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">

<div class="flex flex-col md:flex-row">
    <div class="bg-gray-800 text-white w-full md:w-64 h-screen p-4">
        <h2 class="text-2xl font-bold mb-4">Admin Dashboard</h2>
        <nav>
            <a href="#" class="block py-2 px-4 hover:bg-gray-700 rounded">Events Management</a>
            <a href="user_management.php" class="block py-2 px-4 hover:bg-gray-700 rounded">User Management</a>
            <a href="../auth/logout.php" class="block py-2 px-4 hover:bg-gray-700 rounded">Logout</a>
        </nav>
    </div>

    <div class="flex-1 p-6">
        <h2 class="text-3xl font-bold mb-4">Admin</h2>
        <button class="bg-blue-500 text-white px-4 py-2 rounded mb-4" onclick="toggleModal('createEventModal')">
            Buat Event Baru
        </button>

        <!-- Form Pencarian -->
        <form method="GET" action="" class="flex mb-4">
            <input type="text" name="search" class="flex-grow p-2 border border-gray-300 rounded-l" placeholder="Cari Nama Event">
            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-r">Cari</button>
        </form>

        <!-- Daftar Event dalam bentuk card -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php while ($event = $events->fetch_assoc()): ?>
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <img src="<?php echo htmlspecialchars($event['image']); ?>" class="w-full h-48 object-cover cursor-pointer" alt="Gambar Event" onclick="showDescriptionModal('<?php echo $event['id']; ?>')">
                <div class="p-4">
                    <h5 class="text-xl font-bold"><?php echo htmlspecialchars($event['name']); ?></h5>
                    <p class="text-gray-700 mt-2"><?php echo htmlspecialchars($event['location']); ?></p>
                    <p class="text-gray-500"><i class="fas fa-users"></i> Jumlah Pendaftar: <?php echo isset($event['registrations_count']) ? $event['registrations_count'] : '0'; ?></p>
                    <p class="text-gray-500">Batas Pendaftar: <?php echo htmlspecialchars($event['max_registrations']); ?></p>
                    <p class="text-gray-500" id="countdown<?php echo $event['id']; ?>"></p>
                    <div class="flex justify-between items-center mt-4">
                        <button class="bg-yellow-500 text-white px-4 py-2 rounded" onclick="toggleModal('editEventModal<?php echo $event['id']; ?>')">Edit</button>
                        <a href="?delete_event=<?php echo $event['id']; ?>" class="bg-red-500 text-white px-4 py-2 rounded" onclick="return confirm('Apakah Anda yakin ingin menghapus event ini?')">Hapus</a>
                    </div>
                </div>
            </div>

<!-- Modal untuk pembuatan event baru -->
<div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center" id="createEventModal" style="display: none;">
    <div class="relative mx-auto p-5 border w-96 shadow-lg rounded-md bg-white transform transition-all duration-300 ease-in-out">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Buat Event Baru</h3>
            <div class="mt-2">
                <form method="POST" action="" enctype="multipart/form-data" class="form-group">
                    <div class="mb-4">
                        <label for="event_name" class="block text-sm font-medium text-gray-700">Nama Event:</label>
                        <input type="text" id="event_name" name="event_name" class="mt-1 p-2 border border-gray-300 rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div class="mb-4">
                        <label for="event_date" class="block text-sm font-medium text-gray-700">Tanggal Event:</label>
                        <input type="date" id="event_date" name="event_date" class="mt-1 p-2 border border-gray-300 rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div class="mb-4">
                        <label for="event_location" class="block text-sm font-medium text-gray-700">Lokasi Event:</label>
                        <textarea id="event_location" name="event_location" class="mt-1 p-2 border border-gray-300 rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-500" required></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="event_image" class="block text-sm font-medium text-gray-700">Gambar Event:</label>
                        <input type="file" id="event_image" name="event_image" class="mt-1 p-2 border border-gray-300 rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div class="mb-4">
                        <label for="max_registrations" class="block text-sm font-medium text-gray-700">Batas Pendaftar:</label>
                        <input type="number" id="max_registrations" name="max_registrations" class="mt-1 p-2 border border-gray-300 rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi Event:</label>
                        <textarea id="description" name="description" class="mt-1 p-2 border border-gray-300 rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-500" required></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" name="create_event" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition duration-200">Buat Event</button>
                        <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded ml-2 hover:bg-gray-600 transition duration-200" onclick="toggleModal('createEventModal')">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

     <!-- Modal untuk deskripsi lengkap -->
<div class="fixed inset-0 bg-gray-800 bg-opacity-75 overflow-y-auto h-full w-full flex items-center justify-center" id="descriptionModal<?php echo $event['id']; ?>" style="display: none;">
    <div class="relative mx-auto p-6 border w-96 shadow-lg rounded-md bg-white transform transition-all duration-300 ease-in-out">
        <div class="text-center">
            <h3 class="text-2xl font-semibold text-gray-900 mb-4"><?php echo htmlspecialchars($event['name']); ?></h3>
            <img src="<?php echo htmlspecialchars($event['image']); ?>" class="w-full h-48 object-cover mb-4 rounded-md" alt="Gambar Event">
            <p class="text-gray-700 mb-4 text-justify" style="word-break: break-word;"><?php echo htmlspecialchars($event['description']); ?></p>
            <div class="flex justify-end">
                <button type="button" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition duration-200" onclick="toggleModal('descriptionModal<?php echo $event['id']; ?>')">Tutup</button>
            </div>
        </div>
    </div>
</div>
            <!-- Modal untuk mengedit event -->
            <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center" id="editEventModal<?php echo $event['id']; ?>" style="display: none;">
                <div class="relative mx-auto p-5 border w-96 shadow-lg rounded-md bg-white transform transition-all duration-300 ease-in-out">
                    <div class="mt-3 text-center">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Edit Event</h3>
                        <div class="mt-2">
                            <form method="POST" action="" enctype="multipart/form-data" class="form-group">
                                <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                                <div class="mb-4">
                                    <label for="event_name" class="block text-sm font-medium text-gray-700">Nama Event:</label>
                                    <input type="text" id="event_name" name="event_name" class="mt-1 p-2 border border-gray-300 rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo htmlspecialchars($event['name']); ?>" required>
                                </div>
                                <div class="mb-4">
                                    <label for="event_date" class="block text-sm font-medium text-gray-700">Tanggal Event:</label>
                                    <input type="date" id="event_date" name="event_date" class="mt-1 p-2 border border-gray-300 rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo htmlspecialchars($event['date']); ?>" required>
                                </div>
                                <div class="mb-4">
                                    <label for="event_location" class="block text-sm font-medium text-gray-700">Lokasi Event:</label>
                                    <textarea id="event_location" name="event_location" class="mt-1 p-2 border border-gray-300 rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-500" required><?php echo htmlspecialchars($event['location']); ?></textarea>
                                </div>
                                <div class="mb-4">
                                    <label for="event_image" class="block text-sm font-medium text-gray-700">Gambar Event:</label>
                                    <input type="file" id="event_image" name="event_image" class="mt-1 p-2 border border-gray-300 rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div class="mb-4">
                                    <label for="max_registrations" class="block text-sm font-medium text-gray-700">Batas Pendaftar:</label>
                                    <input type="number" id="max_registrations" name="max_registrations" class="mt-1 p-2 border border-gray-300 rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo htmlspecialchars($event['max_registrations']); ?>" required>
                                </div>
                                <div class="mb-4">
                                    <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi Event:</label>
                                    <textarea id="description" name="description" class="mt-1 p-2 border border-gray-300 rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-500" required><?php echo htmlspecialchars($event['description']); ?></textarea>
                                </div>
                                <div class="flex justify-end">
                                    <button type="submit" name="edit_event" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition duration-200">Simpan Perubahan</button>
                                    <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded ml-2 hover:bg-gray-600 transition duration-200" onclick="toggleModal('editEventModal<?php echo $event['id']; ?>')">Batal</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>           
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    function toggleModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = modal.style.display === 'none' ? 'block' : 'none';
        } else {
            console.error('Modal with ID ' + modalId + ' not found.');
        }
    }

    function showDescriptionModal(eventId) {
        toggleModal('descriptionModal' + eventId);
    }
</script>
<script>
    $(document).ready(function() {
        // Contoh penggunaan toastr untuk notifikasi
        function showToast(type, message) {
            toastr[type](message);
        }

        // Panggil fungsi showToast dengan tipe dan pesan yang sesuai
        // showToast('success', 'Event berhasil ditambahkan!');
        // showToast('error', 'Terjadi kesalahan saat menambahkan event.');

        // Waktu mundur
        <?php
        $events->data_seek(0); // Reset pointer hasil query
        while ($event = $events->fetch_assoc()): ?>
        var countDownDate<?php echo $event['id']; ?> = new Date("<?php echo $event['date']; ?>").getTime();

        var x<?php echo $event['id']; ?> = setInterval(function() {
            var now = new Date().getTime();
            var distance = countDownDate<?php echo $event['id']; ?> - now;

            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById("countdown<?php echo $event['id']; ?>").innerHTML = days + "d " + hours + "h "
            + minutes + "m " + seconds + "s ";

            if (distance < 0) {
                clearInterval(x<?php echo $event['id']; ?>);
                document.getElementById("countdown<?php echo $event['id']; ?>").innerHTML = "Event telah berlangsung";
            }
        }, 1000);
        <?php endwhile; ?>
    });
</script>

</body>
</html>