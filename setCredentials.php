<?php
include "config.php";

// Kullanıcı bilgilerini al
$username = "kadirsener1";
$password = password_hash("Elz2302ksa.", PASSWORD_BCRYPT); // Şifreyi hash'liyoruz

// Şifreli JSON dosyasına kaydet
saveUserCredentials($username, $password);

echo "Kullanıcı bilgileri başarıyla kaydedildi.";
?>
