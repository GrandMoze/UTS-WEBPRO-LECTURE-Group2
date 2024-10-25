<?php
session_start();
include '../config/koneksi.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$query = "SELECT users.name AS user_name, events.name AS event_name FROM registrations 
          JOIN users ON registrations.user_id = users.id 
          JOIN events ON registrations.event_id = events.id";
$result = $koneksi->query($query);

while ($row = $result->fetch_assoc()) {
    echo "<p>User: " . $row['user_name'] . " - Event: " . $row['event_name'] . "</p>";
}
?>
