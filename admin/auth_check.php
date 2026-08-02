<?php
/**
 * Middleware Autentikasi Admin
 * File ini wajib di-require di setiap halaman dalam direktori admin/
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../login.php");
    exit;
}
