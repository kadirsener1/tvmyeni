<?php
session_start();
include "config.php"; // config.php'yi dahil et

$hata = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kullanici = $_POST['username'];
    $sifre = $_POST['password'];

    // Şifreli JSON dosyasından kullanıcı bilgilerini al
    $userCredentials = getUserCredentials();

    if ($userCredentials) {
        // Eğer kullanıcı adı ve şifre doğruysa
        if ($kullanici === $userCredentials['username'] && password_verify($sifre, $userCredentials['password'])) {
            $_SESSION['logged_in'] = true;
            header("Location: admin.php");
            exit();
        } else {
            $hata = "Kullanıcı adı veya şifre yanlış!";
        }
    } else {
        $hata = "Kullanıcı bilgileri bulunamadı!";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Admin Giriş</title>
  <style>
    body { font-family: sans-serif; background: #f5f5f5; display: flex; justify-content: center; align-items: center; height: 100vh; }
    form { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    input { display: block; margin: 10px 0; padding: 10px; width: 100%; }
    .error { color: red; }
  </style>
</head>
<body>
  <form method="post">
    <h2>🔐 Admin Panel Giriş</h2>
    <input type="text" name="username" placeholder="Kullanıcı Adı" required>
    <input type="password" name="password" placeholder="Şifre" required>
    <button type="submit">Giriş Yap</button>
    <?php if ($hata) echo "<p class='error'>$hata</p>"; ?>
  </form>
</body>
</html>
