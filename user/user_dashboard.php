<?php
include '../config/koneksi.php';

session_start();
if ($_SESSION['role'] != 'user') {
    header("Location: ../auth/login.php");
    exit();
}

include '../events/event_list.php';
include 'view_registered_events.php';

// Logika pencarian
$searchQuery = '';
if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
    $events = array_filter($events, function($event) use ($searchQuery) {
        return stripos($event['name'], $searchQuery) !== false;
    });
}

// Query untuk mendapatkan jumlah pendaftar per acara
$registrationsCount = [];
$query = "SELECT event_id, COUNT(*) as count FROM registrations GROUP BY event_id";
$result = $koneksi->query($query);
while ($row = $result->fetch_assoc()) {
    $registrationsCount[$row['event_id']] = $row['count'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengguna</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/user.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        #profileDropdown {
    display: none;
    position: absolute;
    top: 100%; /* Tempatkan tepat di bawah ikon */
    right: 0; /* Sejajarkan ke kanan */
    background-color: white;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 5px;
    padding: 10px;
    z-index: 1000;
}
        /* Global Styles */
        html, body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(to right, #e0eafc, #cfdef3);
        }

        /* Container Styles */
        .container {
            max-width: 1000px;
            margin-top: 20px;
            margin-bottom: 20px;
            margin: 0 auto;
            padding: 20px;
            background: #f0f4f8;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            flex: 1;
        }

        /* Header Styles */
        .header {
            position: sticky;
            top: 0;
            width: 100%;
            z-index: 1000;
            padding: 10px 0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s, box-shadow 0.3s;
        }

        .header:hover {
            background-color: rgba(248, 249, 250, 0.9); /* Transparan saat hover */
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        /* Icon Styles */
        .icon-size {
            width: 24px;
            height: 24px;
            fill: currentColor;
        }

        /* Card Styles */
        .card {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            width: 100%;
            margin: 10px 0;
            background: #fff;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.3);
        }

        .card img {
            height: 150px;
            object-fit: cover;
            transition: transform 0.3s;
            border-bottom: 2px solid #2980b9;
        }

        .card:hover img {
            transform: scale(1.05);
        }

        .card-body {
            padding: 12px;
            background-color: #fff;
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2980b9;
            margin-bottom: 12px;
            text-align: center;
        }

        .card-text {
            font-size: 1rem;
            color: #2c3e50;
            margin-bottom: 10px;
            text-align: center;
        }

        .card-footer {
            background-color: #f4f4f9;
            padding: 12px;
        }

        /* Button Styles */
        .btn-primary {
            background-color: #3498db;
            border: none;
            transition: background-color 0.3s, transform 0.3s;
            font-weight: bold;
        }

        .btn-primary:hover {
            background-color: #2980b9;
            transform: scale(1.1);
        }

        /* Typography */
        h2 {
            font-size: 2rem;
            color: #2980b9;
            margin-bottom: 20px;
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: bold;
            color: #8e44ad;
        }

        .text-center {
            text-align: center;
        }

        /* Decorative Element */
        .decorative-line {
            width: 100%;
            height: 4px;
            background: linear-gradient(to right, #2980b9, #8e44ad);
            margin: 20px 0;
        }

        /* WhatsApp Button Styles */
        .whatsapp-float {
            position: fixed;
            width: 160px;
            height: 60px;
            bottom: 20px;
            right: 20px;
            background: linear-gradient(to right, #25d366, #128c7e);
            color: white;
            border-radius: 50px;
            text-align: center;
            box-shadow: 2px 2px 3px #999;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-weight: bold;
            z-index: 1000;
            transition: transform 0.3s;
        }

        .whatsapp-float:hover {
            transform: scale(1.1);
        }

        .whatsapp-icon {
            width: 30px;
            height: 30px;
            margin-right: 10px;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .whatsapp-float {
                width: 140px;
                height: 50px;
                bottom: 15px;
                right: 15px;
            }
            .whatsapp-icon {
                width: 25px;
                height: 25px;
            }
        }

        @media (min-width: 768px) {
            .grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        /* Notification Styles */
        #notification {
            position: fixed;
            top: 0;
            right: 0;
            margin: 20px;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: none;
        }
        @media (max-width: 768px) {
            .grid {
                grid-template-columns: 1fr; /* Satu kolom untuk ponsel */
            }
            .card {
                margin: 10px auto; /* Pusatkan kartu */
            }
            .card-title, .card-text {
                font-size: 1rem; /* Kurangi ukuran font */
            }
            .header, .container {
                padding: 10px; /* Kurangi padding */
            }
        }
        footer {
    background-color: #333;
    color: white;
    text-align: center;
    padding: 10px 0;
    position: relative;
    bottom: 0;
    width: 100%;
}
.modal-content {
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    overflow: hidden;
}

.modal-header {
    background-color: #2980b9;
    color: white;
    padding: 15px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.modal-title {
    font-size: 1.5rem;
    font-weight: bold;
}

.modal-body img {
    width: 100%;
    height: auto;
    border-bottom: 2px solid #2980b9;
    margin-bottom: 15px;
}

.modal-body p {
    margin: 10px 0;
    font-size: 1rem;
    color: #2c3e50;
    padding: 10px;
}
#eventDetailDescription {
    font-size: 1rem;
    color: #2c3e50;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: #f9f9f9;
    word-wrap: break-word; /* Memastikan teks berpindah baris */
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    padding: 10px;
}

.btn-secondary {
    background-color: #7f8c8d;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    transition: background-color 0.3s;
}

.btn-secondary:hover {
    background-color: #95a5a6;
}
    </style>
</head>
<link rel="icon" href="../admin/GAMBAR/Untitled image.png" type="image/x-icon">
<body>
<header class="header sticky top-0 bg-white shadow-md flex flex-col sm:flex-row items-center justify-between px-4 md:px-8 py-2">
    <div class="flex items-center justify-center sm:justify-start w-full sm:w-auto">
        <a style="text-decoration: none;" href="" class="flex items-center">
        <img src="../admin/GAMBAR/Untitled image.png" alt="Logo" class="h-12 w-auto mr-2">
        <span style="font-family: 'Bernard MT Condensed', sans-serif;" class="text-2xl font-bold text-green-500">TiketEvent.com</span>
        </a>
    </div>
    <div class="flex items-center justify-center sm:justify-end w-full sm:w-auto mt-2 sm:mt-0">
        <form action="" method="get" class="flex w-full sm:w-auto">
            <input type="text" name="search" placeholder="Cari acara..." value="<?php echo htmlspecialchars($searchQuery); ?>" class="form-control mr-2 w-full sm:w-auto">
            <button type="submit" class="btn btn-primary">Cari</button>
        </form>
        <a href="#" data-toggle="modal" data-target="#cartModal" class="ml-4">
            <svg class="h-8 p-1 hover:text-green-500 duration-200" aria-hidden="true" focusable="false" data-prefix="far" data-icon="shopping-cart" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M551.991 64H144.28l-8.726-44.608C133.35 8.128 123.478 0 112 0H12C5.373 0 0 5.373 0 12v24c0 6.627 5.373 12 12 12h80.24l69.594 355.701C150.796 415.201 144 430.802 144 448c0 35.346 28.654 64 64 64s64-28.654 64-64a63.681 63.681 0 0 0-8.583-32h145.167a63.681 63.681 0 0 0-8.583 32c0 35.346 28.654 64 64 64 35.346 0 64-28.654 64-64 0-18.136-7.556-34.496-19.676-46.142l1.035-4.757c3.254-14.96-8.142-29.101-23.452-29.101H203.76l-9.39-48h312.405c11.29 0 21.054-7.869 23.452-18.902l45.216-208C578.695 78.139 567.299 64 551.991 64zM208 472c-13.234 0-24-10.766-24-24s10.766-24 24-24 24 10.766 24 24-10.766 24-24 24zm256 0c-13.234 0-24-10.766-24-24s10.766-24 24-24 24 10.766 24 24-10.766 24-24 24zm23.438-200H184.98l-31.31-160h368.548l-34.78 160z"></path></svg>
        </a>
        <a href="#" id="profileIcon" class="ml-4">
    <svg class="icon-size hover:text-green-500 duration-200" aria-hidden="true" focusable="false" data-prefix="far" data-icon="user" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
        <path fill="currentColor" d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm89.6 32h-11.7c-22.2 10.3-46.7 16-72.9 16s-50.7-5.7-72.9-16h-11.7C60.2 288 0 348.2 0 422.4V464c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48v-41.6c0-74.2-60.2-134.4-134.4-134.4z"></path>
    </svg>
</a>
<div id="profileDropdown" style="display: none; position: absolute; background-color: white; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); border-radius: 5px; padding: 10px; z-index: 1000;">
    <a href="../user/profile.php" class="dropdown-item">Profil Saya</a>
    <a href="../auth/logout.php" class="dropdown-item">Logout</a>
</div>
    </div>
</header>
<div class="container mx-auto mt-20 px-4">
    <main>
        <div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="cartModalLabel">Acara yang Telah Anda Daftar</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <?php
                        $registeredEvents = getRegisteredEvents($_SESSION['user_id']);
                        if (!empty($registeredEvents)) {
                            echo "<ul class='list-group'>";
                            foreach ($registeredEvents as $event) {
                                echo "<li class='list-group-item'>";
                                echo "<img src='../admin/" . (isset($event['image']) ? $event['image'] : 'default.png') . "' alt='Gambar Acara' class='w-full h-48 object-cover mb-2'>";
                                echo "<h5>" . (isset($event['name']) ? $event['name'] : 'Nama acara tidak tersedia') . "</h5>";
                                echo "<p>Tanggal: " . (isset($event['date']) ? $event['date'] : 'Tanggal tidak tersedia') . "</p>";
                                echo "<p>Lokasi: " . (isset($event['location']) ? $event['location'] : 'Lokasi tidak tersedia') . "</p>";
                                
                                // Pastikan 'id' ada sebelum digunakan
                                if (isset($event['id'])) {
                                    echo "<button class='btn btn-danger' onclick='cancelEvent(" . $event['id'] . ")'>Cancel</button>";
                                } else {
                                    echo "<p class='text-danger'>ID acara tidak tersedia.</p>"; 
                                }
                                
                                echo "</li>";
                            }
                            echo "</ul>";
                        } else {
                            echo "<p class='text-center'>Anda belum mendaftar ke acara apapun.</p>";
                        }
                        ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <section>
        <h2 class="text-center">Daftar Acara yang Tersedia</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php if (!empty($events)): ?>
                <?php foreach ($events as $event): ?>
                    <div class="card">
                        <img src="<?php echo '../admin/' . (isset($event['image']) ? $event['image'] : 'default.png'); ?>" 
                            alt="<?php echo (isset($event['name']) ? $event['name'] : 'Gambar tidak tersedia'); ?>" 
                            class="w-full h-48 object-cover event-image" data-event-id="<?php echo $event['id']; ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo (isset($event['name']) ? $event['name'] : 'Nama acara tidak tersedia'); ?></h5>
                            <p class="card-text" id="countdown<?php echo $event['id']; ?>"></p>
                            <p class="card-text"><?php echo (isset($event['location']) ? $event['location'] : 'Lokasi tidak tersedia'); ?></p>
                        </div>
                        <div class="card-footer text-center">
                            <form onsubmit="registerEvent(event, <?php echo $event['id']; ?>)">
                                <input type="hidden" name="event_id" value="<?php echo (isset($event['id']) ? $event['id'] : ''); ?>">
                                <button type="submit" class="btn btn-primary">Daftar</button>
                            </form>
                        </div>
                    </div>

                    <script>
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
                                document.getElementById("countdown<?php echo $event['id']; ?>").innerHTML = "EXPIRED";
                            }
                        }, 1000);
                    </script>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center">Tidak ada acara yang tersedia saat ini.</p>
            <?php endif; ?>
        </div>
        </section>

        <!-- Modal untuk detail acara -->
        <div class="modal fade" id="eventDetailModal" tabindex="-1" aria-labelledby="eventDetailModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="eventDetailModalLabel">Detail Acara</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <img id="eventDetailImage" src="" alt="Gambar Acara" class="w-full h-48 object-cover mb-2">
                        <p style="font-weight: bold; text-align: center;" id="eventDetailName"></p>
                        <p id="eventDetailDate"></p>
                        <p id="eventDetailLocation"></p>
                        <p><strong>Deskripsi Acara:</strong></p>
                        <p id="eventDetailDescription"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

    </main>
</div>
<footer class="text-white text-center mt-20 w-full">
    <div id="sponsorCarousel" class="carousel slide mx-auto max-w-screen-lg" data-ride="carousel" data-interval="2000">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="../admin/GAMBAR/gambar1.png" class="w-full h-40 object-cover" alt="Sponsor 1">
            </div>
            <div class="carousel-item">
                <img src="../admin/GAMBAR/gambar2.png" class="w-full h-40 object-cover" alt="Sponsor 2">
            </div>
            <div class="carousel-item">
                <img src="../admin/GAMBAR/gambar3.png" class="w-full h-40 object-cover" alt="Sponsor 3">
            </div>
        </div>
    </div>
</footer>

<a style="text-decoration: none;" href="https://wa.me/6285893930323" class="whatsapp-float" target="_blank">
    <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WhatsApp" class="whatsapp-icon">
    <span>Contact Us</span>
</a>

<div id="notification">Pendaftaran berhasil!</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
// ... existing code ...

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var profileIcon = document.getElementById('profileIcon');
        if (profileIcon) {
            profileIcon.addEventListener('click', function(event) {
                event.preventDefault();
                var profileDropdown = document.getElementById('profileDropdown');
                if (profileDropdown.style.display === 'none' || profileDropdown.style.display === '') {
                    profileDropdown.style.display = 'block';
                } else {
                    profileDropdown.style.display = 'none';
                }
            });
        }

        // Tambahkan event listener untuk gambar acara
        var eventImages = document.querySelectorAll('.event-image');
        eventImages.forEach(function(image) {
            image.addEventListener('click', function() {
                var eventId = this.getAttribute('data-event-id');
                var event = <?php echo json_encode($events); ?>.find(e => e.id == eventId);

                if (event) {
                    document.getElementById('eventDetailImage').src = '../admin/' + (event.image || 'default.png');
                    document.getElementById('eventDetailName').innerText = event.name || 'Nama acara tidak tersedia';
                    document.getElementById('eventDetailDate').innerText = 'Tanggal: ' + (event.date || 'Tanggal tidak tersedia');
                    document.getElementById('eventDetailLocation').innerText = 'Lokasi: ' + (event.location || 'Lokasi tidak tersedia');
                    document.getElementById('eventDetailDescription').innerText = event.description || 'Deskripsi tidak tersedia';

                    $('#eventDetailModal').modal('show');
                }
            });
        });
    });

    // ... existing code ...
</script>
<script>
    function registerEvent(event, eventId) {
        event.preventDefault();

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "../events/register_event.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.status === 'exists') {
                        Swal.fire({
                            icon: 'info',
                            title: 'Maaf',
                            text: 'Anda sudah terdaftar untuk acara ini. Silakan pilih acara lainnya.',
                        });
                    } else if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Selamat',
                            text: 'Anda telah berhasil mendaftar di acara ini!',
                        }).then(() => {
                            updateRegisteredEvents(); // Panggil fungsi untuk memperbarui daftar acara
                        });
                    } else if (response.status === 'full') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Penuh',
                            text: 'Acara sudah penuh.',
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan',
                            text: response.message || 'Terjadi kesalahan saat mendaftar.',
                        });
                    }
                } catch (e) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Kesalahan',
                        text: 'Respons server tidak valid.',
                    });
                }
            }
        };
        xhr.send("event_id=" + eventId);
    }
    function updateRegisteredEvents() {
    // Lakukan permintaan AJAX untuk mendapatkan daftar acara yang terdaftar
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "../events/get_registered_events.php", true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                // Perbarui tampilan modal dengan data baru
                document.querySelector('.modal-body').innerHTML = xhr.responseText;
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan',
                    text: 'Gagal memperbarui daftar acara.',
                });
            }
        }
    };
    xhr.send();
}

    function cancelEvent(eventId) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "../user/cancel_event.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Pendaftaran dibatalkan!',
                    }).then(() => {
                        location.reload(); // Refresh daftar acara terdaftar
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Kesalahan',
                        text: 'Terjadi kesalahan saat membatalkan pendaftaran.',
                    });
                }
            }
        };
        xhr.send("event_id=" + eventId);
    }

    function showNotification(message) {
        var notification = document.getElementById('notification');
        notification.innerText = message;
        notification.style.display = 'block';
        setTimeout(function() {
            notification.style.display = 'none';
        }, 3000); // Notifikasi akan hilang setelah 3 detik
    }
</script>
</body>
</html>