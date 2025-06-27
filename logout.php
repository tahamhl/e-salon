<?php
require_once 'config/config.php';
require_once 'utilities/functions.php';
require_once 'controllers/AuthController.php';

session_start();

// Çıkış işlemini yap
$controller = new AuthController();
$controller->logout();
?> 