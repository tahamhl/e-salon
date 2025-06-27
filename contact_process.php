<?php
// Oturum başlat
session_start();

// Yapılandırma dosyasını içe aktar
require_once 'config/config.php';

// Veritabanı bağlantısı
$db = connectDB();

// POST verilerini kontrol et
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Gelen verileri temizle ve doğrula
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_STRING);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
    
    // Validasyon
    $errors = [];
    
    if (empty($name)) {
        $errors[] = 'Ad Soyad alanı gereklidir.';
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Geçerli bir e-posta adresi girilmelidir.';
    }
    
    if (empty($phone)) {
        $errors[] = 'Telefon alanı gereklidir.';
    }
    
    if (empty($subject)) {
        $errors[] = 'Konu alanı gereklidir.';
    }
    
    if (empty($message)) {
        $errors[] = 'Mesaj alanı gereklidir.';
    }
    
    // Hata yoksa mesajı veritabanına kaydet
    if (empty($errors)) {
        try {
            $query = "
                INSERT INTO contact_messages (name, email, phone, subject, message, created_at, status)
                VALUES (:name, :email, :phone, :subject, :message, NOW(), 'new')
            ";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':subject', $subject);
            $stmt->bindParam(':message', $message);
            
            $result = $stmt->execute();
            
            if ($result) {
                // Yöneticilere e-posta bildirimi gönder (gerçek implementasyonda)
                // sendEmailNotification($name, $email, $subject, $message);
                
                // Başarı mesajı
                $_SESSION['flash_message'] = 'Mesajınız başarıyla gönderildi. En kısa sürede size dönüş yapacağız.';
                $_SESSION['flash_type'] = 'success';
                
                // İletişim sayfasına yönlendir
                header('Location: index.php?page=contact');
                exit;
            } else {
                throw new Exception('Mesaj kaydedilirken bir hata oluştu.');
            }
        } catch (Exception $e) {
            // Hata logla
            error_log('İletişim formu hatası: ' . $e->getMessage());
            
            // Hata mesajı
            $_SESSION['flash_message'] = 'Mesajınız gönderilirken bir hata oluştu. Lütfen daha sonra tekrar deneyin.';
            $_SESSION['flash_type'] = 'error';
            
            // İletişim sayfasına yönlendir
            header('Location: index.php?page=contact');
            exit;
        }
    } else {
        // Validasyon hatalarını göster
        $_SESSION['flash_message'] = implode('<br>', $errors);
        $_SESSION['flash_type'] = 'error';
        
        // İletişim sayfasına yönlendir
        header('Location: index.php?page=contact');
        exit;
    }
} else {
    // POST isteği değilse ana sayfaya yönlendir
    header('Location: index.php');
    exit;
} 