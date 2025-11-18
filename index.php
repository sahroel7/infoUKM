<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Cek jika pengguna belum login, redirect ke halaman login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'koneksi.php';
require_once 'proses.php';

$db = new Database();
$conn = $db->getConnection();
$postingan = new infoUKM($conn);

$editData = null;
if (isset($_GET['edit'])) {
    $editData = $postingan->getById($_GET['edit']);
}


$dataukm = $postingan->getAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="style.css">
    <title>Info UKM & Organisasi</title>
</head>
<body>
<div class="container">
    <div class="header-nav">
        <span>Selamat datang, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong> (<?= htmlspecialchars($_SESSION['sebagai']) ?>)</span>
        <a href="logout.php" class="logout-link">Logout</a>
    </div>
    <h1><b>Info UKM & Organisasi</b></h1>
    <?php if ($_SESSION['sebagai'] == 'admin'): ?>
        <?php if ($editData): ?>
    <h2>Edit Postingan</h2>
    <!-- form edit -->
    <!-- tambahkan disini -->
    <form action="proses.php" method="POST" enctype="multipart/form-data" class="form-post">
        <input type="hidden" name="id" value="<?=$editData['id'] ?>">
        <table>
            <tr>
                <td>Judul</td>
                <td><input type="text" name="judul" required value="<?= htmlspecialchars($editData['judul']) ?>"></td>
            </tr>
            <tr>
                <td>Kategori</td>
                <td><select name="kategori" required>
                    <option value="">--Pilih--</option>
                    <option value="Organisasi" <?= ($editData['kategori'] == 'Organisasi') ? 'selected' : '' ?>>Organisasi</option>
                    <option value="UKM" <?= ($editData['kategori'] == 'UKM') ? 'selected' : '' ?>>UKM</option>
                </select></td>
            </tr>
            <tr>
                <td>Tanggal Mulai</td>
                <td><input type="date" name="tanggal_mulai" required value="<?= htmlspecialchars($editData['tanggal_mulai']) ?>"></td>
            </tr>
            <tr>
                <td>Tanggal Selesai</td>
                <td><input type="date" name="tanggal_selesai" required value="<?= htmlspecialchars($editData['tanggal_selesai']) ?>"></td>
            </tr>
            <tr>
                <td>Nama Kontak</td>
                <td><input type="text" name="nama_kontak" value="<?= htmlspecialchars($editData['nama_kontak']) ?>"></td>
            </tr>
            <tr>
                <td>No. HP Kontak</td>
                <td><input type="text" name="hp_kontak" value="<?= htmlspecialchars($editData['hp_kontak']) ?>"></td>
            </tr>
            <tr>
                <td>Link Pendaftaran</td>
                <td><input type="text" name="link_pendaftaran" value="<?= htmlspecialchars($editData['link_pendaftaran']) ?>"></td>
            </tr>

            <tr>
                <td></td> <!-- Empty cell for alignment -->
                <td><input type="submit" name="update" value="Update"> <a href="index.php">Batal</a></td>
            </tr>
        </table>
    </form>
    <?php else: ?>

    <h2>Buat Postingan Baru</h2>
<!-- form tambah postingan -->
<!-- tambahkan disini -->
    <form action="proses.php" method="POST" enctype="multipart/form-data" class="form-post">
        <table>
            <tr>
                <td>Judul</td>
                <td><input type="text" name="judul" required></td>
            </tr>
            <tr>
                <td>Kategori</td>
                <td><select name="kategori" required>
                    <option value="">--Pilih--</option>
                    <option value="Organisasi">Organisasi</option>
                    <option value="UKM">UKM</option>
                </select></td>
            </tr>
            <tr>
                <td>Tanggal Mulai</td>
                <td><input type="date" name="tanggal_mulai" required></td>
            </tr>
            <tr>
                <td>Tanggal Selesai</td>
                <td><input type="date" name="tanggal_selesai" required></td>
            </tr>
            <tr>
                <td>Nama Kontak</td>
                <td><input type="text" name="nama_kontak" required></td>
            </tr>
            <tr>
                <td>No. HP Kontak</td>
                <td><input type="text" name="hp_kontak" required></td>
            </tr>
            <tr>
                <td>Link Pendaftaran</td>
                <td><input type="text" name="link_pendaftaran" required></td>
            </tr>
            <tr>
                <td></td>
                <td><input type="submit" name="submit" value="Publikasikan"></td>
            </tr>
        </table>
    </form>
    <?php endif; ?>
    <?php endif; ?>

<!-- menampilkan data postingan -->
<!-- tambahkan disini -->
    <h2>Daftar Postingan</h2>
    <table class="data-table">
    <tr>
        <th>Judul</th>
        <th>Kategori</th>
        <th>Tanggal Mulai</th>
        <th>Tanggal Selesai</th>
        <th>Nama Kontak</th>
        <th>No. HP Kontak</th>
        <th>Link Pendaftaran</th>
        <?php if ($_SESSION['sebagai'] == 'admin'): ?>
        <th>Aksi</th>
        <?php endif; ?>
    </tr>
    <?php if (!empty($dataukm)): ?>
        <?php $no = 1; foreach ($dataukm as $row): ?>
        <tr>
            <!-- tambahkan disini -->
            <td><?= htmlspecialchars($row['judul']) ?></td>
            <td><?= htmlspecialchars($row['kategori']) ?></td>
            <td><?= htmlspecialchars($row['tanggal_mulai']) ?></td>
            <td><?= htmlspecialchars($row['tanggal_selesai']) ?></td> 
            <td><?= htmlspecialchars($row['nama_kontak']) ?></td>
            <td><?= htmlspecialchars($row['hp_kontak']) ?></td>
            <td>
                <?php if (!empty($row['link_pendaftaran'])): ?>
                    <a href="<?= htmlspecialchars($row['link_pendaftaran']) ?>" target="_blank" class="register-link">Klik Disini</a>
                <?php endif; ?>
            </td>
            <?php if ($_SESSION['sebagai'] == 'admin'): ?>
            <td class="action-links">
                <a href="index.php?edit=<?= $row['id'] ?>" class="update-link">Update</a>
                <a href="proses.php?delete=<?= $row['id'] ?>" class="delete-link" onclick="return confirm('Yakin Hapus data ini?')">Delete</a>
            </td>
            <?php endif; ?>
        </tr>
        <?php endforeach; ?>
            <?php else: ?>
                <?php $colspan = ($_SESSION['sebagai'] == 'admin') ? 8 : 7; ?> <!-- tambahkan disini -->
                <tr><td colspan="<?= $colspan ?>" style="text-align: center;">Belum ada data.</td></tr>
            <?php endif; ?>
    </table>

</div>
</body>
</html>
    
