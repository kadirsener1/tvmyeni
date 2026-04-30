<?php
// AES-256 ile şifreleme için anahtar
define("ENCRYPTION_KEY", "your-encryption-key-here"); // Anahtarı güçlü bir şekilde belirle
define("CONFIG_FILE", "config.json"); // Şifreli JSON dosyasının yolu

// Şifreli JSON verisini çözme fonksiyonu
function decrypt($data) {
    $iv = substr($data, 0, 16); // İlk 16 byte IV (Initialization Vector)
    $encryptedData = substr($data, 16); // Kalan veri şifreli kısmı

    return openssl_decrypt($encryptedData, 'aes-256-cbc', ENCRYPTION_KEY, 0, $iv);
}

// JSON verisini şifreleme fonksiyonu
function encrypt($data) {
    $iv = openssl_random_pseudo_bytes(16); // 16 byte IV oluşturuluyor
    $encryptedData = openssl_encrypt($data, 'aes-256-cbc', ENCRYPTION_KEY, 0, $iv);
    return $iv . $encryptedData; // IV ve şifreli veriyi birleştiriyoruz
}

// Şifreli config.json'dan kullanıcı bilgilerini al
function getUserCredentials() {
    if (file_exists(CONFIG_FILE)) {
        $encryptedData = file_get_contents(CONFIG_FILE);
        $decryptedData = decrypt($encryptedData);
        return json_decode($decryptedData, true);
    }
    return null;
}

// Kullanıcı bilgilerini şifreli bir şekilde kaydet
function saveUserCredentials($username, $password) {
    $userData = json_encode(['username' => $username, 'password' => $password]);
    $encryptedData = encrypt($userData);
    file_put_contents(CONFIG_FILE, $encryptedData);
}
?>
