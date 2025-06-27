<?php
require_once 'config/config.php';
require_once 'utilities/functions.php';
require_once 'controllers/DashboardController.php';

// Session başlat
session_start();

// Oturum kontrolü
if (!isset($_SESSION['user_id']) && basename($_SERVER['PHP_SELF']) != 'login.php' && basename($_SERVER['PHP_SELF']) != 'register.php') {
    header('Location: login.php');
    exit;
}

// Sayfa başlığını ayarla
$pageTitle = 'Anasayfa';

// Başlık ve üst kısmı dahil et
include_once 'views/partials/header.php';

// Dashboard verilerini al
$controller = new DashboardController();
$dashboardData = $controller->getDashboardData();

// Kullanıcı rolüne göre doğru view'i göster
if ($_SESSION['user_role'] === 'admin') {
    include_once 'views/dashboard/admin_dashboard.php';
} elseif ($_SESSION['user_role'] === 'staff') {
    include_once 'views/dashboard/staff_dashboard.php';
} else {
    include_once 'views/dashboard/member_dashboard.php';
}

// Alt kısmı dahil et
include_once 'views/partials/footer.php';
?> 