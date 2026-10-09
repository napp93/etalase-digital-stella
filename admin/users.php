<?php
require_once '../config/kuis_db.php';
require_once '../middleware/auth_check.php';

// Memastikan hanya Super Admin yang bisa mengakses halaman ini
check_role(['super_admin']);

$message = '';
// Tambah Admin Baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_admin'])) {
    $nama  = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $pass  = password_hash($_POST['password'], PASSWORD_BCRYPT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (nama, email, password, role) VALUES (:nama, :email, :pass, 'admin')");
        $stmt->execute([':nama' => $nama, ':email' => $email, ':pass' => $pass]);
        $message = "Admin baru berhasil ditambahkan.";
    } catch (PDOException $e) {
        $message = "Gagal menambahkan admin (Email mungkin sudah terdaftar).";
    }
}

// Hapus User
if (isset($_GET['hapus'])) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id AND role != 'super_admin'");
    $stmt->execute([':id' => $_GET['hapus']]);
    header('Location: users.php');
    exit();
}

$users = $pdo->query("SELECT * FROM users ORDER BY role ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola User - Super Admin</title>
    <!-- Hubungkan CSS Base, Form, dan Admin -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/form.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<div class="container" style="max-width: 1000px; margin: 30px auto;">
    <a href="../index.php" class="btn btn-secondary" style="margin-bottom: 20px;">&larr; Kembali ke Etalase</a>
    <h2 style="margin-bottom: 20px;">Manajemen User (Super Admin Only)</h2>

    <?php if ($message): ?>
        <p style="padding: 10px; background: #e2e3e5; border-radius: 5px; margin-bottom: 20px;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <div class="form-container" style="margin: 0 0 30px 0; max-width: 100%;">
        <h3>Tambah Admin Baru</h3>
        <form method="POST" style="margin-top: 15px;">
            <div class="form-group">
                <label for="nama">Nama Admin</label>
                <input type="text" id="nama" name="nama" placeholder="Nama Admin" required>
            </div>
            <div class="form-group">
                <label for="email">Email Admin</label>
                <input type="email" id="email" name="email" placeholder="Email Admin" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" name="tambah_admin" class="btn btn-primary">Tambah Admin</button>
        </form>
    </div>

    <div class="table-container">
        <h3>Daftar Pengguna</h3>
        <table class="custom-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $u): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td><?= htmlspecialchars($u['nama']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><span class="badge-role badge-<?= htmlspecialchars($u['role']) ?>"><?= htmlspecialchars($u['role']) ?></span></td>
                    <td>
                        <?php if($u['role'] !== 'super_admin'): ?>
                            <a href="users.php?hapus=<?= $u['id'] ?>" class="btn btn-danger" style="padding: 4px 10px; font-size: 12px;" onclick="return confirm('Hapus user ini?')">Hapus</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
