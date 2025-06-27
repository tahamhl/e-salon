<?php
require_once 'config/config.php';
require_once 'utilities/functions.php';
require_once 'controllers/MenuController.php';
require_once 'controllers/PackageController.php';

session_start();

// Sayfa başlığını ayarla - varsayılan olarak
$pageTitle = 'Paketler';

// Belirli bir işlem belirtilmişse
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $controller = new PackageController();
    
    // İlgili işleme yönlendir
    switch($action) {
        case 'show':
            if (isset($_GET['id'])) {
                $controller->show($_GET['id']);
            } else {
                redirect('packages.php');
            }
            break;
            
        case 'purchase':
            if (isset($_GET['id'])) {
                $controller->purchase($_GET['id']);
            } else {
                redirect('packages.php');
            }
            break;
            
        case 'complete_purchase':
            if (isset($_GET['id']) && isset($_POST['payment_type'])) {
                $controller->completePurchase($_GET['id'], $_POST);
            } else {
                redirect('packages.php');
            }
            break;
            
        // Admin işlemleri
        case 'manage':
            $pageTitle = 'Paket Yönetimi';
            $controller->manage();
            break;
            
        case 'create':
            $pageTitle = 'Yeni Paket Ekle';
            $controller->create();
            break;
            
        case 'store':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->store($_POST);
            } else {
                redirect('packages.php?action=create');
            }
            break;
            
        case 'edit':
            if (isset($_GET['id'])) {
                $pageTitle = 'Paket Düzenle';
                $controller->edit($_GET['id']);
            } else {
                redirect('packages.php?action=manage');
            }
            break;
            
        case 'update':
            if (isset($_GET['id']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->update($_GET['id'], $_POST);
            } else {
                redirect('packages.php?action=manage');
            }
            break;
            
        case 'delete':
            if (isset($_GET['id'])) {
                $controller->delete($_GET['id']);
            } else {
                redirect('packages.php?action=manage');
            }
            break;
            
        default:
            $menuController = new MenuController();
            $menuController->showPackages();
            break;
    }
} else {
    // Varsayılan olarak tüm paketleri göster
    $menuController = new MenuController();
    $menuController->showPackages();
}
?> 