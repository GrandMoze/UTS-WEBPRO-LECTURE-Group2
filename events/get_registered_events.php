<?php
include '../config/koneksi.php';
session_start();

function getRegisteredEvents($userId) {
    global $koneksi;
    $query = "SELECT events.id, events.name, events.date, events.location, events.image 
              FROM registrations 
              JOIN events ON registrations.event_id = events.id 
              WHERE registrations.user_id = ?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $events = [];
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
    return $events;
}

$userId = $_SESSION['user_id'];
$registeredEvents = getRegisteredEvents($userId);

if (!empty($registeredEvents)) {
    echo "<ul class='list-group'>";
    foreach ($registeredEvents as $event) {
        echo "<li class='list-group-item'>";
        echo "<img src='../admin/" . (isset($event['image']) ? $event['image'] : 'default.png') . "' alt='Gambar Acara' class='w-full h-48 object-cover mb-2'>";
        echo "<h5>" . (isset($event['name']) ? $event['name'] : 'Nama acara tidak tersedia') . "</h5>";
        echo "<p>Tanggal: " . (isset($event['date']) ? $event['date'] : 'Tanggal tidak tersedia') . "</p>";
        echo "<p>Lokasi: " . (isset($event['location']) ? $event['location'] : 'Lokasi tidak tersedia') . "</p>";
        
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