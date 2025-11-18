<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'koneksi.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db = new Database();
    $conn = $db->getConnection();

    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $sebagai = 'pengguna';

    if (empty($username) || empty($password)) {
        $error = "Username dan password tidak boleh kosong.";
    } elseif ($password !== $confirm_password) {
        $error = "Konfirmasi password tidak cocok.";
    } else {
        // Cek apakah username sudah ada
        $stmt = $conn->prepare("SELECT id_user FROM tabel_user WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username sudah digunakan. Silakan pilih yang lain.";
        } else {
            // Hash password sebelum disimpan
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $insert_stmt = $conn->prepare("INSERT INTO tabel_user (username, password, sebagai) VALUES (?, ?, ?)");
            $insert_stmt->bind_param("sss", $username, $hashed_password, $sebagai);

            if ($insert_stmt->execute()) {
                $success = "Registrasi berhasil! Silakan login";
            } else {
                $error = "Terjadi kesalahan. Gagal melakukan registrasi.";
            }
            $insert_stmt->close();
        }
        $stmt->close();
    }
    $db->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Info UKM</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <form action="registrasi.php" method="POST" class="form-auth">
        <h1>Registrasi Akun</h1>
        <?php if ($error): ?><p class="form-error"><?= $error ?></p><?php endif; ?>
        <?php if ($success): ?><p class="form-success"><?= $success ?></p><?php endif; ?>

        <?php if (!$success): // Sembunyikan form jika registrasi berhasil ?>
            <input type="text" name="username" placeholder="Username" required><br><br>
            <input type="password" name="password" placeholder="Password" required><br><br>
            <input type="password" name="confirm_password" placeholder="Konfirmasi Password" required><br><br>
            <input type="submit" value="Daftar">
        <?php endif; ?>
        <p class="form-footer">Sudah punya akun? <a href="login.php">Login di sini</a></p>
    </form>
</div>
</body>
</html>