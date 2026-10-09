<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function check_login() {
    if (!isset($_SESSION['user_id'])) {
        $login_path = file_exists('auth/login.php') ? 'auth/login.php' : '../auth/login.php';
        header("Location: $login_path");
        exit();
    }
}

function check_role($roles = []) {
    check_login();
    if (!in_array($_SESSION['role'], $roles)) {
        die("Akses Ditolak: Anda tidak memiliki hak akses ke halaman ini.");
    }
}
?>
