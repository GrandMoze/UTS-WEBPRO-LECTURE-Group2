<?php
   $koneksi = new mysqli("localhost", "root", "", "ujian tengah semester lecture");

   if ($koneksi->connect_error) {
       die("Koneksi gagal: " . $koneksi->connect_error);
   }
   ?>