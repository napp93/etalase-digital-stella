<?php
require_once 'config/kuis_db.php';
require_once 'middleware/auth_check.php';

check_login();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_project = trim($_POST['nama_project']);
    $deskripsi    = trim($_POST['deskripsi']);
    $link_project = trim($_POST['link_project']);
    $user_id      = $_SESSION['user_id'];
    $gambar_nama  = '';

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($ext, $allowed_ext)) {
            $gambar_nama = uniqid('project_', true) . '.' . $ext;
            if (!is_dir('uploads')) {
                mkdir('uploads', 0777, true);
            }
            move_uploaded_file($_FILES['gambar']['tmp_name'], 'uploads/' . $gambar_nama);
        } else {
            $error = "Format gambar tidak didukung (gunakan JPG, PNG, GIF, WebP).";
        }
    } else {
        $error = "Gambar karya wajib diunggah.";
    }

    if (empty($error)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO projects (user_id, nama_project, deskripsi, gambar, link_project, created_at) VALUES (:user_id, :nama, :desk, :img, :link, NOW())");
            $stmt->execute([
                ':user_id' => $user_id,
                ':nama'    => $nama_project,
                ':desk'    => $deskripsi,
                ':img'     => $gambar_nama,
                ':link'    => $link_project
            ]);
            header('Location: index.php');
            exit();
        } catch (PDOException $e) {
            $error = "Gagal menyimpan karya: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Karya - Etalase Digital Stella</title>
    <!-- Hubungkan CSS Base & CSS Form -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/form.css">
</head>
<body>
<div class="form-container">
    <a href="index.php" style="text-decoration: none; color: #007bff; display: inline-block; margin-bottom: 15px;">&larr; Kembali ke Etalase</a>
    <h2>Tambah Karya Digital</h2>
    <?php if ($error): ?>
        <p style="color: red; margin-bottom: 15px;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="nama_project">Nama Karya</label>
            <input type="text" id="nama_project" name="nama_project" required>
        </div>
        <div class="form-group">
            <label for="deskripsi">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" required></textarea>
        </div>
        <div class="form-group">
            <label for="link_project">Link Project</label>
            <input type="url" id="link_project" name="link_project" placeholder="https://..." required>
        </div>
        <div class="form-group">
            <label for="gambar">Upload Gambar Karya</label>
            <input type="file" id="gambar" name="gambar" accept="image/*" required>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Karya</button>
    </form>
</div>
</body>
</html>
