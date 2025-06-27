<?php
// Veritabanı bağlantı ayarları
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'e_salon');

// Uygulama sabit değerleri
define('APP_NAME', 'E-Salon');
define('APP_VERSION', '1.0.0');
define('BASE_URL', 'http://localhost/e-salon');

// Hata ayıklaması
define('DEBUG', true);

// Zaman dilimi ayarı
date_default_timezone_set('Europe/Istanbul');

// Veritabanı bağlantısı
function connectDB() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        return new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        if (DEBUG) {
            die("Veritabanı bağlantı hatası: " . $e->getMessage());
        } else {
            die("Veritabanı bağlantısı sağlanamadı.");
        }
    }
}
?> 