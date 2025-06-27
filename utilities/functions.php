<?php
/**
 * Uygulama genelinde kullanılacak yardımcı fonksiyonlar
 */

/**
 * Verilen URL'e yönlendirme yapar
 * 
 * @param string $url Yönlendirilecek URL
 * @return void
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Başarı mesajını session'a kaydeder
 * 
 * @param string $message Başarı mesajı
 * @return void
 */
function setSuccess($message) {
    $_SESSION['success'] = $message;
}

/**
 * Hata mesajını session'a kaydeder
 * 
 * @param string $message Hata mesajı
 * @return void
 */
function setError($message) {
    $_SESSION['error'] = $message;
}

/**
 * Uyarı mesajını session'a kaydeder
 * 
 * @param string $message Uyarı mesajı
 * @return void
 */
function setWarning($message) {
    $_SESSION['warning'] = $message;
}

/**
 * Bilgi mesajını session'a kaydeder
 * 
 * @param string $message Bilgi mesajı
 * @return void
 */
function setInfo($message) {
    $_SESSION['info'] = $message;
}

/**
 * Session içindeki başarı mesajını getirir ve temizler
 * 
 * @return string|null Başarı mesajı
 */
function getSuccess() {
    $message = $_SESSION['success'] ?? null;
    unset($_SESSION['success']);
    return $message;
}

/**
 * Session içindeki hata mesajını getirir ve temizler
 * 
 * @return string|null Hata mesajı
 */
function getError() {
    $message = $_SESSION['error'] ?? null;
    unset($_SESSION['error']);
    return $message;
}

/**
 * Session içindeki uyarı mesajını getirir ve temizler
 * 
 * @return string|null Uyarı mesajı
 */
function getWarning() {
    $message = $_SESSION['warning'] ?? null;
    unset($_SESSION['warning']);
    return $message;
}

/**
 * Session içindeki bilgi mesajını getirir ve temizler
 * 
 * @return string|null Bilgi mesajı
 */
function getInfo() {
    $message = $_SESSION['info'] ?? null;
    unset($_SESSION['info']);
    return $message;
}

/**
 * Şifre hashleme
 * 
 * @param string $password Hashlenecek şifre
 * @return string Hash edilmiş şifre
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Şifre doğrulama
 * 
 * @param string $password Kontrol edilecek şifre
 * @param string $hash Hash edilmiş şifre
 * @return bool Eşleşiyorsa true, değilse false
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * XSS ve SQL injection saldırılarına karşı veriyi temizler
 * 
 * @param string $data Temizlenecek veri
 * @return string Temizlenmiş veri
 */
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Para formatı
 * 
 * @param float $amount Para miktarı
 * @return string Formatlanmış para
 */
function formatMoney($amount) {
    return number_format($amount, 2, ',', '.') . ' ₺';
}

/**
 * Tarih formatı
 * 
 * @param string $date Tarih
 * @param string $format Format (opsiyonel)
 * @return string Formatlanmış tarih
 */
function formatDate($date, $format = 'd.m.Y') {
    return date($format, strtotime($date));
}

/**
 * Rastgele şifre oluşturur
 * 
 * @param int $length Şifre uzunluğu
 * @return string Rastgele şifre
 */
function generateRandomPassword($length = 8) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_=+';
    $password = '';
    
    for($i = 0; $i < $length; $i++) {
        $password .= $chars[rand(0, strlen($chars) - 1)];
    }
    
    return $password;
}

/**
 * Kullanıcı rolünün belirli bir yetkiye sahip olup olmadığını kontrol eder
 * 
 * @param string|array $roles İzin verilen roller
 * @return bool Yetkili ise true, değilse false
 */
function hasRole($roles) {
    if (!isset($_SESSION['user_role'])) {
        return false;
    }
    
    if (!is_array($roles)) {
        $roles = [$roles];
    }
    
    return in_array($_SESSION['user_role'], $roles);
}

/**
 * Oturum açmış bir kullanıcı olup olmadığını kontrol eder
 * 
 * @return bool Oturum açılmışsa true, değilse false
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Belirli bir tarihin, şu andan itibaren geçerli olup olmadığını kontrol eder
 * 
 * @param string $date Kontrol edilecek tarih
 * @return bool Geçerliyse true, değilse false
 */
function isDateValid($date) {
    return strtotime($date) >= strtotime('today');
}

/**
 * İki tarih arasındaki gün farkını hesaplar
 * 
 * @param string $date1 Başlangıç tarihi
 * @param string $date2 Bitiş tarihi
 * @return int Gün farkı
 */
function dateDiff($date1, $date2) {
    $start = new DateTime($date1);
    $end = new DateTime($date2);
    $interval = $start->diff($end);
    return $interval->days;
}

/**
 * Kullanıcı adını oluşturur (Ad ve soyadın ilk harfleri)
 * 
 * @param string $firstName İsim
 * @param string $lastName Soyisim
 * @return string Kullanıcı adı
 */
function generateUsername($firstName, $lastName) {
    $firstName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $firstName));
    $lastName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $lastName));
    
    return $firstName . substr($lastName, 0, 1);
}

/**
 * Veritabanı bağlantısını oluşturur ve geri döndürür
 * 
 * @return PDO Veritabanı bağlantısı
 */
function getDb() {
    return connectDB();
}

/**
 * Kullanıcının admin rolüne sahip olup olmadığını kontrol eder
 * 
 * @return bool Admin ise true, değilse false
 */
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Kullanıcının personel (staff) rolüne sahip olup olmadığını kontrol eder
 * 
 * @return bool Personel ise true, değilse false
 */
function isStaff() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'staff';
}
?> 