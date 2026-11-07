<?php
session_start();

/* Kosongkan session */
$_SESSION = [];

/* Hapus session cookie jika ada */
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 3600,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

/* Hancurkan session */
session_destroy();

/* Redirect */
header("Location: ../login_page.php");
exit;
?>