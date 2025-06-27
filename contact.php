<?php
/**
 * İletişim ve randevu sayfası
 */
require_once 'config/init.php';

// Oturum başlatma
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// POST isteğini kontrol et
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Form verilerini al ve güvenli hale getir
    $name = htmlspecialchars(trim($_POST['name'] ?? ''));
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $phone = htmlspecialchars(trim($_POST['phone'] ?? ''));
    $subject = htmlspecialchars(trim($_POST['subject'] ?? ''));
    $message = htmlspecialchars(trim($_POST['message'] ?? ''));
    
    // Hata bayrağı
    $error = false;
    
    // Doğrulama işlemleri
    if (empty($name)) {
        $_SESSION['flash_message'] = 'Lütfen isim ve soyisim alanını doldurunuz.';
        $_SESSION['flash_type'] = 'error';
        $error = true;
    } elseif (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['flash_message'] = 'Lütfen geçerli bir e-posta adresi giriniz.';
        $_SESSION['flash_type'] = 'error';
        $error = true;
    } elseif (empty($subject)) {
        $_SESSION['flash_message'] = 'Lütfen konu alanını doldurunuz.';
        $_SESSION['flash_type'] = 'error';
        $error = true;
    } elseif (empty($message)) {
        $_SESSION['flash_message'] = 'Lütfen mesaj alanını doldurunuz.';
        $_SESSION['flash_type'] = 'error';
        $error = true;
    }
    
    // Hata yoksa veritabanına kaydet
    if (!$error) {
        try {
            // Veritabanı bağlantısı
            $db = connectDB();
            
            // SQL sorgusu
            $sql = "INSERT INTO contact_messages (name, email, phone, subject, message, created_at) 
                    VALUES (:name, :email, :phone, :subject, :message, NOW())";
            
            // Sorguyu hazırla ve çalıştır
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':subject', $subject);
            $stmt->bindParam(':message', $message);
            
            if ($stmt->execute()) {
                // Başarı mesajı
                $_SESSION['flash_message'] = 'Mesajınız başarıyla gönderildi. En kısa sürede size dönüş yapacağız.';
                $_SESSION['flash_type'] = 'success';
                
                // İsteğe bağlı: Yöneticiye e-posta bildirimi gönderme
                // mail('admin@e-salon.com', 'Yeni İletişim Formu Mesajı', "İsim: $name\nE-posta: $email\nTelefon: $phone\nKonu: $subject\nMesaj: $message");
            } else {
                // Hata mesajı
                $_SESSION['flash_message'] = 'Mesajınız gönderilirken bir hata oluştu. Lütfen daha sonra tekrar deneyiniz.';
                $_SESSION['flash_type'] = 'error';
            }
        } catch (PDOException $e) {
            // Veritabanı hatası
            $_SESSION['flash_message'] = 'Sistem hatası. Lütfen daha sonra tekrar deneyiniz.';
            $_SESSION['flash_type'] = 'error';
            // Hata günlüğüne kaydet
            error_log('İletişim formu hatası: ' . $e->getMessage());
        }
    }
    
    // İletişim sayfasına yönlendir
    header('Location: contact.php');
    exit;
}

// Sayfa başlığı
$pageTitle = "İletişim";

// Eğitmen randevu mu yoksa genel iletişim sayfası mı?
if (isset($_GET['trainer_id'])) {
    $trainerId = (int)$_GET['trainer_id'];
    
    // Header'ı dahil et
    require_once 'views/partials/header.php';
    
    // Eğitmen randevu sayfasını göster
    require_once 'views/contact/trainer_booking.php';
    
    // Footer'ı dahil et
    require_once 'views/partials/footer.php';
} else {
    // Header'ı dahil et
    require_once 'views/partials/header.php';
    
    // Genel iletişim sayfasını göster
    require_once 'views/contact/index.php';
    
    // Footer'ı dahil et
    require_once 'views/partials/footer.php';
}
?> 