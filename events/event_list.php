<?php

include '../config/koneksi.php'; // Pastikan koneksi ke database

$query = "SELECT * FROM events";
$result = $koneksi->query($query);

if (!$result) {
    die("Query gagal: " . $koneksi->error);
}

$events = [];
while ($event = $result->fetch_assoc()) {
    $events[] = $event;
}
?>