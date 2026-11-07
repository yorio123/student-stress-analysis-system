<?php
session_start();
include __DIR__ . "/../koneksi/koneksi.php";

if (!$conn) {
    die("Koneksi database gagal.");
}

// Pastikan request berasal dari form
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: ../login_page.php");
    exit;
}

// Ambil data dari form
$username = trim($_POST['username']);
$password = trim($_POST['password']);

// Cek username
$query = mysqli_query($conn, "SELECT * FROM admin WHERE username='$username'");

if (mysqli_num_rows($query) == 1) {

    $admin = mysqli_fetch_assoc($query);

    // Jika password masih plaintext
    if ($password == $admin['password']) {

        $_SESSION['admin'] = $admin['username'];
        $_SESSION['email'] = $admin['email'];
        $_SESSION['nama'] = $admin['nama'];
        header("Location: ../dashboard.php");
        exit;

    } else {

        header("Location: ../login_page.php?error=1");
        exit;

    }

} else {

    header("Location: ../login_page.php?error=1");
    exit;

}
?>