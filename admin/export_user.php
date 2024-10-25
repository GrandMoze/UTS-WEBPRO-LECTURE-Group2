<?php
   session_start();
   require 'vendor/autoload.php'; // Pastikan path ini sesuai dengan lokasi autoload.php
   include '../config/koneksi.php';

   use PhpOffice\PhpSpreadsheet\Spreadsheet;
   use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

   // Periksa apakah session role sudah diatur
   if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
       header("Location: ../index.php");
       exit();
   }

   // Periksa apakah tombol export ditekan
   if (isset($_POST['export'])) {
       $spreadsheet = new Spreadsheet();
       $sheet = $spreadsheet->getActiveSheet();

       // Tulis header kolom
       $sheet->setCellValue('A1', 'ID');
       $sheet->setCellValue('B1', 'Name');
       $sheet->setCellValue('C1', 'Email');
       $sheet->setCellValue('D1', 'Registered Events');

       // Ambil data pengguna dan event yang terdaftar
       $query = "SELECT users.id, users.name, users.email, GROUP_CONCAT(events.name SEPARATOR ', ') AS registered_events
                 FROM users
                 LEFT JOIN registrations ON users.id = registrations.user_id
                 LEFT JOIN events ON registrations.event_id = events.id
                 WHERE users.role = 'user' 
                 GROUP BY users.id";
       $result = $koneksi->query($query);

       // Tulis data ke spreadsheet
       $rowNumber = 2;
       while ($row = $result->fetch_assoc()) {
           $sheet->setCellValue('A' . $rowNumber, $row['id']);
           $sheet->setCellValue('B' . $rowNumber, $row['name']);
           $sheet->setCellValue('C' . $rowNumber, $row['email']);
           $sheet->setCellValue('D' . $rowNumber, $row['registered_events']);
           $rowNumber++;
       }

       // Set header untuk file Excel
       header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
       header('Content-Disposition: attachment; filename="users.xlsx"');

       $writer = new Xlsx($spreadsheet);
       $writer->save('php://output');
       exit();
   }
   ?>