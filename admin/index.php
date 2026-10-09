<?php
require_once '../config/kuis_db.php';
require_once '../middleware/auth_check.php';

check_login();

// Ambil semua karya beserta nama pembuat
$stmt = $pdo->query("SELECT projects.*, users.nama AS pembuat 
                     FROM projects 
                     JOIN users ON projects.user_id = users.id 
                     ORDER BY projects.created_at DESC");
$projects = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - Etalase Digital Stella</title>
    <!-- Hubungkan CSS Base & CSS Main & Admin -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<nav class="navbar">
    <h2>Etalase Digital Stella (Admin Panel)</h2>
    <div class="navbar-user">
        Halo, <b><?= htmlspecialchars($_SESSION['nama']) ?></b> (<?= htmlspecialchars($_SESSION['role']) ?>) | 
        <a href="../project_tambah.php" class="btn btn-primary">+ Tambah Karya</a>
        <?php if ($_SESSION['role'] === 'super_admin'): ?>
            <a href="users.php" class="btn btn-warning">Kelola Users</a>
        <?php endif; ?> 
        <a href="../auth/logout.php" class="btn btn-danger">Logout</a>
    </div>
</nav>

<div class="container">
    <div class="grid-projects">
        <?php if (empty($projects)): ?>
            <p style="grid-column: 1 / -1; text-align: center; color: #6c757d; margin-top: 30px;">Belum ada karya yang diunggah.</p>
        <?php else: ?>
            <?php foreach($projects as $p): ?>
                <div class="card-project">
                    <?php if (!empty($p['gambar'])): ?>
                        <img src="../uploads/<?= htmlspecialchars($p['gambar']) ?>" alt="Karya" class="card-img-top">
                    <?php else: ?>
                        <div style="height: 180px; background: #e9ecef; display: flex; align-items: center; justify-content: center; color: #adb5bd;">Tidak Ada Gambar</div>
                    <?php endif; ?>
                    <div class="card-body">
                        <h3 class="card-title"><?= htmlspecialchars($p['nama_project']) ?></h3>
                        <p class="card-author">Oleh: <b><?= htmlspecialchars($p['pembuat']) ?></b></p>
                        <p class="card-text"><?= htmlspecialchars($p['deskripsi']) ?></p>
                        <p><a href="<?= htmlspecialchars($p['link_project']) ?>" target="_blank" class="btn btn-primary" style="display: inline-block; margin-bottom: 10px;">Lihat Project Tautan &rarr;</a></p>
                        
                        <!-- Hak Akses Edit/Hapus: Owner / Admin / Super Admin -->
                        <?php if($_SESSION['role'] === 'super_admin' || $_SESSION['role'] === 'admin' || $_SESSION['user_id'] == $p['user_id']): ?>
                            <div class="card-actions">
                                <a href="../project_edit.php?id=<?= $p['id'] ?>" class="btn btn-warning">Edit</a>
                                <a href="../project_hapus.php?id=<?= $p['id'] ?>" onclick="return confirm('Hapus karya ini?')" class="btn btn-danger">Hapus</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
