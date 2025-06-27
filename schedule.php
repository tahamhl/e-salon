<?php
// schedule.php dosyası program.php'ye yönlendirme yapıyor
require_once 'config/config.php';
require_once 'utilities/functions.php';

session_start();

// program.php'ye yönlendir
header('Location: program.php');
exit;
?> 