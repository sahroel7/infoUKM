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
if (isset($_POST['login'])) {
    $db = new Database();
    $conn = $db->getConnection();

    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id_user, username, password, sebagai FROM tabel_user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['sebagai'] = $user['sebagai'];
            header("Location: index.php");
            exit();
        } else {
            $error = "Username atau password salah!";
        }

    } else {
        $error = "Username atau password salah!";
    }
    $stmt->close();
    $db->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Info UKM</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <form action="login.php" method="POST" class="form-auth">
        <h1>Login</h1>
        <?php if ($error): ?>
            <p class="form-error"><?= $error ?></p>
        <?php endif; ?>
        <input type="text" name="username" placeholder="Username" required><br><br>
        <input type="password" name="password" placeholder="Password" required><br><br>
        <input type="submit" name="login" value="Login">
        <p class="form-footer">Belum punya akun? <a href="registrasi.php">Daftar di sini</a></p>
    </form>
</div>
</body>
</html>