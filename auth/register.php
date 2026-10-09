<?php
session_start();
require_once '../config/kuis_db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama']);
    $email    = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (nama, email, password, role) VALUES (:nama, :email, :password, 'siswa')");
        $stmt->execute([':nama' => $nama, ':email' => $email, ':password' => $password]);
        header('Location: login.php');
        exit();
    } catch (PDOException $e) {
        $error = "Email sudah terdaftar atau terjadi kesalahan.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Registrasi - Etalase Digital Stella</title>
    <!-- Hubungkan CSS Base & CSS Auth -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
        <h2>Registrasi Siswa</h2>
        <?php if ($error): ?>
            <div class="alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-control">
                <label for="nama">Nama Lengkap</label>
                <input type="text" id="nama" name="nama" placeholder="Nama Lengkap" required>
            </div>
            <div class="form-control">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Email" required>
            </div>
            <div class="form-control">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Daftar</button>
        </form>
        <div class="auth-footer">
            <p>Sudah punya akun? <a href="login.php">Login</a></p>
        </div>
    </div>
</div>
</body>
</html>
