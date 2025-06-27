<?php
/**
 * Helpers - Yardımcı fonksiyonlar
 * Uygulama genelinde kullanılabilecek yardımcı fonksiyonları içerir
 */

/**
 * Yönlendirme yapar
 * 
 * @param string $path Yönlendirilecek yol
 * @return void
 */
function redirect($path) {
    header("Location: {$path}");
    exit;
}

/**
 * Veriyi güvenli hale getirir
 * 
 * @param mixed $data Temizlenecek veri
 * @return mixed Temizlenmiş veri
 */
function sanitize($data) {
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = sanitize($value);
        }
        return $data;
    }
    
    // Özel karakterleri temizle
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    
    return $data;
}

/**
 * Oturum bilgilerine başarı mesajı ekler
 * 
 * @param string $message Mesaj
 * @return void
 */
function setSuccess($message) {
    $_SESSION['flash']['success'] = $message;
}

/**
 * Oturum bilgilerine hata mesajı ekler
 * 
 * @param string $message Mesaj
 * @return void
 */
function setError($message) {
    $_SESSION['flash']['error'] = $message;
}

/**
 * Oturum bilgilerine bilgi mesajı ekler
 * 
 * @param string $message Mesaj
 * @return void
 */
function setInfo($message) {
    $_SESSION['flash']['info'] = $message;
}

/**
 * Oturum bilgilerine uyarı mesajı ekler
 * 
 * @param string $message Mesaj
 * @return void
 */
function setWarning($message) {
    $_SESSION['flash']['warning'] = $message;
}

/**
 * Flash mesajlarını döndürür ve oturumdan temizler
 * 
 * @return array Flash mesajları
 */
function getFlashMessages() {
    $messages = isset($_SESSION['flash']) ? $_SESSION['flash'] : [];
    unset($_SESSION['flash']);
    return $messages;
}

/**
 * Tek flash mesajını döndürür ve oturumdan temizler
 * 
 * @param string $type Mesaj türü (success, error, info, warning)
 * @return string|null Mesaj veya null
 */
function getFlashMessage($type) {
    $message = isset($_SESSION['flash'][$type]) ? $_SESSION['flash'][$type] : null;
    unset($_SESSION['flash'][$type]);
    return $message;
}

/**
 * CSRF tokeni oluşturur veya kontrol eder
 * 
 * @param bool $verify Token kontrolü yapılacaksa true
 * @return string|bool Token üretilirse string, kontrol edilirse bool
 */
function csrf($verify = false) {
    if ($verify) {
        // Token kontrolü
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])) {
            return false;
        }
        
        if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            return false;
        }
        
        return true;
    } else {
        // Token üretme
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }
}

/**
 * CSRF token input alanı oluşturur
 * 
 * @return string HTML input
 */
function csrfField() {
    $token = csrf();
    return "<input type='hidden' name='csrf_token' value='{$token}'>";
}

/**
 * Para formatını düzenler
 * 
 * @param float $amount Miktar
 * @param string $currency Para birimi
 * @return string Formatlanmış para
 */
function formatMoney($amount, $currency = '₺') {
    return number_format($amount, 2, ',', '.') . ' ' . $currency;
}

/**
 * Tarihi formatlar
 * 
 * @param string $date Tarih
 * @param string $format Format
 * @return string Formatlanmış tarih
 */
function formatDate($date, $format = 'd.m.Y') {
    return date($format, strtotime($date));
}

/**
 * Tarihi ve saati formatlar
 * 
 * @param string $datetime Tarih ve saat
 * @param string $format Format
 * @return string Formatlanmış tarih ve saat
 */
function formatDateTime($datetime, $format = 'd.m.Y H:i') {
    return date($format, strtotime($datetime));
}

/**
 * Metni belirli bir uzunlukta kısaltır
 * 
 * @param string $text Metin
 * @param int $length Maksimum uzunluk
 * @param string $append Kısaltma sonuna eklenecek metin
 * @return string Kısaltılmış metin
 */
function truncate($text, $length = 100, $append = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    
    $text = substr($text, 0, $length);
    return rtrim($text) . $append;
}

/**
 * Aktif sayfayı kontrol eder
 * 
 * @param string $page Kontrol edilecek sayfa
 * @return bool Aktif sayfaysa true
 */
function isActivePage($page) {
    $currentPage = $_SERVER['REQUEST_URI'];
    
    if ($page === '/') {
        return $currentPage === '/';
    }
    
    return strpos($currentPage, $page) !== false;
}

/**
 * Aktif sayfa için CSS sınıfı döndürür
 * 
 * @param string $page Kontrol edilecek sayfa
 * @param string $class Eklenecek CSS sınıfı
 * @return string CSS sınıfı veya boş string
 */
function activeClass($page, $class = 'active') {
    return isActivePage($page) ? $class : '';
}

/**
 * Şifreyi güvenli bir şekilde hasher
 * 
 * @param string $password Şifre
 * @return string Hash
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Şifre doğrulama
 * 
 * @param string $password Şifre
 * @param string $hash Hash
 * @return bool Doğruysa true
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Rastgele şifre oluşturur
 * 
 * @param int $length Şifre uzunluğu
 * @return string Şifre
 */
function generatePassword($length = 8) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?';
    return substr(str_shuffle($chars), 0, $length);
}

/**
 * Rastgele token oluşturur
 * 
 * @param int $length Token uzunluğu
 * @return string Token
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Dosya boyutunu okunabilir formata dönüştürür
 * 
 * @param int $bytes Bayt cinsinden boyut
 * @param int $precision Hassasiyet
 * @return string Formatlanmış boyut
 */
function formatFileSize($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    
    $bytes /= pow(1024, $pow);
    
    return round($bytes, $precision) . ' ' . $units[$pow];
}

/**
 * Diziden belirli bir değere sahip elemanı bulur
 * 
 * @param array $array Dizi
 * @param string $key Anahtar
 * @param mixed $value Değer
 * @return mixed Bulunan eleman veya null
 */
function findInArray($array, $key, $value) {
    foreach ($array as $item) {
        if (isset($item[$key]) && $item[$key] == $value) {
            return $item;
        }
    }
    
    return null;
}

/**
 * İki tarih arasındaki farkı hesaplar
 * 
 * @param string $date1 İlk tarih
 * @param string $date2 İkinci tarih
 * @param string $format Sonuç formatı (days, hours, minutes, seconds)
 * @return int Fark
 */
function dateDiff($date1, $date2, $format = 'days') {
    $datetime1 = new DateTime($date1);
    $datetime2 = new DateTime($date2);
    $interval = $datetime1->diff($datetime2);
    
    switch ($format) {
        case 'years':
            return $interval->y;
        case 'months':
            return $interval->y * 12 + $interval->m;
        case 'days':
            return $interval->days;
        case 'hours':
            return $interval->days * 24 + $interval->h;
        case 'minutes':
            return ($interval->days * 24 + $interval->h) * 60 + $interval->i;
        case 'seconds':
            return (($interval->days * 24 + $interval->h) * 60 + $interval->i) * 60 + $interval->s;
        default:
            return $interval->days;
    }
}

/**
 * Bir tarihe belirli bir süre ekler
 * 
 * @param string $date Tarih
 * @param string $interval Eklenecek süre (örn: +1 day, +2 weeks)
 * @param string $format Sonuç formatı
 * @return string Yeni tarih
 */
function addToDate($date, $interval, $format = 'Y-m-d') {
    $datetime = new DateTime($date);
    $datetime->modify($interval);
    return $datetime->format($format);
}

/**
 * SEO dostu URL oluşturur
 * 
 * @param string $string Metin
 * @return string SEO dostu URL
 */
function slugify($string) {
    // Türkçe karakterleri değiştir
    $string = str_replace(
        ['ı', 'ğ', 'ü', 'ş', 'ö', 'ç', 'İ', 'Ğ', 'Ü', 'Ş', 'Ö', 'Ç'],
        ['i', 'g', 'u', 's', 'o', 'c', 'i', 'g', 'u', 's', 'o', 'c'],
        $string
    );
    
    // Küçük harfe çevir
    $string = strtolower($string);
    
    // Alfanümerik olmayan karakterleri tire ile değiştir
    $string = preg_replace('/[^a-z0-9]/', '-', $string);
    
    // Birden fazla tireyi tek tireye indir
    $string = preg_replace('/-+/', '-', $string);
    
    // Baştaki ve sondaki tireleri temizle
    return trim($string, '-');
}

/**
 * Verilen e-posta adresinin geçerli olup olmadığını kontrol eder
 * 
 * @param string $email E-posta adresi
 * @return bool Geçerliyse true
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Debug amaçlı veri çıktısı
 * 
 * @param mixed $data Çıktılanacak veri
 * @param bool $die Çıktı sonrası çıkış yapılacaksa true
 * @return void
 */
function debug($data, $die = true) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
    
    if ($die) {
        die();
    }
}

/**
 * IP adresini alır
 * 
 * @return string IP adresi
 */
function getIpAddress() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    
    return $ip;
}

/**
 * Kullanıcı arayüzünde kullanılacak JavaScript değişkenlerini oluşturur
 * 
 * @param array $vars Değişkenler
 * @return string JavaScript kodu
 */
function jsVars($vars) {
    $output = '<script>var appVars = ' . json_encode($vars) . ';</script>';
    return $output;
}

/**
 * Kullanıcı tarayıcı bilgilerini alır
 * 
 * @return array Tarayıcı bilgileri
 */
function getBrowser() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $browser = 'Bilinmeyen';
    $platform = 'Bilinmeyen';
    
    // Platform
    if (preg_match('/linux/i', $userAgent)) {
        $platform = 'Linux';
    } elseif (preg_match('/macintosh|mac os x/i', $userAgent)) {
        $platform = 'Mac';
    } elseif (preg_match('/windows|win32/i', $userAgent)) {
        $platform = 'Windows';
    }
    
    // Tarayıcı
    if (preg_match('/MSIE/i', $userAgent) && !preg_match('/Opera/i', $userAgent)) {
        $browser = 'Internet Explorer';
    } elseif (preg_match('/Firefox/i', $userAgent)) {
        $browser = 'Mozilla Firefox';
    } elseif (preg_match('/Chrome/i', $userAgent)) {
        $browser = 'Google Chrome';
    } elseif (preg_match('/Safari/i', $userAgent)) {
        $browser = 'Apple Safari';
    } elseif (preg_match('/Opera/i', $userAgent)) {
        $browser = 'Opera';
    } elseif (preg_match('/Netscape/i', $userAgent)) {
        $browser = 'Netscape';
    }
    
    return [
        'userAgent' => $userAgent,
        'browser' => $browser,
        'platform' => $platform
    ];
}
?> 