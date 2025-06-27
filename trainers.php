<?php
require_once 'config/config.php';
require_once 'utilities/functions.php';
require_once 'controllers/MenuController.php';

session_start();

// Sayfa başlığını ayarla
$pageTitle = 'Eğitmenler';

// Menü kontrolcüsünü başlat
$controller = new MenuController();

// İşleme göre yönlendirme yap
if (isset($_GET['action']) && $_GET['action'] == 'show' && isset($_GET['id'])) {
    // Eğitmen detayını göster
    $controller->showTrainerDetails($_GET['id']);
} else {
    // Tüm eğitmenleri göster
    $controller->showTrainers();
}
?> 