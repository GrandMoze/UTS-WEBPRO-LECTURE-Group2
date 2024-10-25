<?php
include '../config/koneksi.php';

function getRegisteredEvents($user_id) {
    global $koneksi;
    $query = "SELECT events.id, events.name, events.date, events.location, events.image, registrations.id AS registration_id
              FROM registrations
              JOIN events ON registrations.event_id = events.id
              WHERE registrations.user_id = ?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $registeredEvents = [];
    while ($event = $result->fetch_assoc()) {
        $registeredEvents[] = $event;
    }
    return $registeredEvents;
}
?>