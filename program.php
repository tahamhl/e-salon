<?php
require_once 'config/config.php';
require_once 'utilities/functions.php';
require_once 'controllers/MenuController.php';

session_start();

// Sayfa başlığını ayarla
$pageTitle = 'Program';

// Menü kontrolcüsünü başlat
$controller = new MenuController();

// Program sayfasını göster
$controller->showProgram();
?> 