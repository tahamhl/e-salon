<?php
/**
 * Uygulama başlatma dosyası
 */

// Hata ayıklaması açık
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Zaman dilimi
date_default_timezone_set('Europe/Istanbul');

// Konfigürasyon dosyasını dahil et
require_once 'config.php';

// Yardımcı fonksiyonları dahil et
require_once 'utilities/helpers.php';

// Veritabanı bağlantısı
require_once 'utilities/Database.php';

// Oturum başlat
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?> 