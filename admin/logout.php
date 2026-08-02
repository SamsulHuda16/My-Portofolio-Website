<?php
/**
 * Skrip Logout Admin Panel
 * Menghancurkan session dan mengosongkan kredensial login
 */
session_start();

// Kosongkan seluruh variabel session
$_SESSION = array();

// Hapus cookie session jika ada
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Hancurkan session
session_destroy();

// Redirect ke halaman login
header("Location: ../login.php?logout=success");
exit;
