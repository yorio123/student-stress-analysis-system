<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login_page.php");
    exit;
}

include __DIR__ . "/../koneksi/koneksi.php";

if (!isset($_GET['id'])) {
    die("ID tidak ditemukan");
}

$id = (int) $_GET['id'];

/* =========================================================
   HAPUS DATA DARI SEMUA TABEL TERKAIT
========================================================= */

// urutan penting: child table dulu, baru parent

mysqli_query($conn, "DELETE FROM hasil_fuzzy WHERE data_id='$id'");
mysqli_query($conn, "DELETE FROM hasil_perhitungan WHERE data_id='$id'");

mysqli_query($conn, "DELETE FROM perceived WHERE data_id='$id'");
mysqli_query($conn, "DELETE FROM time_management WHERE data_id='$id'");
mysqli_query($conn, "DELETE FROM pcv WHERE data_id='$id'");
mysqli_query($conn, "DELETE FROM financial WHERE data_id='$id'");
mysqli_query($conn, "DELETE FROM sleep WHERE data_id='$id'");

// terakhir hapus data utama
mysqli_query($conn, "DELETE FROM data WHERE data_id='$id'");

/* =========================================================
   REDIRECT
========================================================= */

header("Location: ../data.php?hapus=sukses");
exit;
?>