<?php
require_once 'config/kuis_db.php';
require_once 'middleware/auth_check.php';

check_login();

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit();
}

// Fetch data project
$stmt = $pdo->prepare("SELECT * FROM projects WHERE id = :id");
$stmt->execute([':id' => $id]);
$project = $stmt->fetch();

if (!$project) {
    die("Data karya tidak ditemukan.");
}

// Pengecekan Otorisasi: Siswa hanya dapat mengedit miliknya sendiri
if ($_SESSION['role'] === 'siswa' && $project['user_id'] != $_SESSION['user_id']) {
    die("Akses Ditolak: Anda tidak dapat mengedit karya milik siswa lain.");
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_project = trim($_POST['nama_project']);
    $deskripsi    = trim($_POST['deskripsi']);
    $link_project = trim($_POST['link_project']);
    $gambar_nama  = $project['gambar'];

    // Jika user mengunggah gambar baru
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($ext, $allowed_ext)) {
            $new_name = uniqid('project_', true) . '.' . $ext;
            if (!is_dir('uploads')) {
                mkdir('uploads', 0777, true);
            }
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], 'uploads/' . $new_name)) {
                if (!empty($project['gambar']) && file_exists('uploads/' . $project['gambar'])) {
                    unlink('uploads/' . $project['gambar']); // Hapus gambar lama
                }
                $gambar_nama = $new_name;
            }
        } else {
            $error = "Format gambar tidak didukung.";
        }
    }

    if (empty($error)) {
        $stmt = $pdo->prepare("UPDATE projects SET nama_project = :nama, deskripsi = :desk, gambar = :img, link_project = :link WHERE id = :id");
        $stmt->execute([':nama' => $nama_project, ':desk' => $deskripsi, ':img' => $gambar_nama, ':link' => $link_project, ':id' => $id]);
        header('Location: index.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Karya - Etalase Digital Stella</title>
    <!-- Hubungkan CSS Base & CSS Form -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/form.css">
</head>
<body>
<div class="form-container">
    <a href="index.php" style="text-decoration: none; color: #007bff; display: inline-block; margin-bottom: 15px;">&larr; Kembali ke Etalase</a>
    <h2>Edit Karya Digital</h2>
    <?php if ($error): ?>
        <p style="color: red; margin-bottom: 15px;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="nama_project">Nama Karya</label>
            <input type="text" id="nama_project" name="nama_project" value="<?= htmlspecialchars($project['nama_project']) ?>" required>
        </div>
        <div class="form-group">
            <label for="deskripsi">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" required><?= htmlspecialchars($project['deskripsi']) ?></textarea>
        </div>
        <div class="form-group">
            <label for="link_project">Link Project</label>
            <input type="url" id="link_project" name="link_project" value="<?= htmlspecialchars($project['link_project']) ?>" required>
        </div>
        <div class="form-group">
            <label for="gambar">Ganti Gambar (Opsional)</label>
            <input type="file" id="gambar" name="gambar" accept="image/*">
            <?php if (!empty($project['gambar'])): ?>
                <p style="font-size: 12px; color: #6c757d; margin-top: 5px;">Gambar saat ini: <?= htmlspecialchars($project['gambar']) ?></p>
            <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </form>
</div>
</body>
</html>
