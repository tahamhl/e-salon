<?php
// Oturum başlat
session_start();

// Veritabanı bağlantısı
require_once 'config/config.php';

// Form kontrolü
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $_SESSION['flash_message'] = 'Geçersiz istek yöntemi.';
    $_SESSION['flash_type'] = 'error';
    header('Location: index.php');
    exit;
}

// Form verilerini al
$trainerId = isset($_POST['trainer_id']) ? intval($_POST['trainer_id']) : 0;
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$serviceType = $_POST['service_type'] ?? '';
$date = $_POST['date'] ?? '';
$time = $_POST['time'] ?? '';
$notes = $_POST['notes'] ?? '';

// Veri doğrulama
$errors = [];

if (empty($trainerId)) {
    $errors[] = 'Eğitmen bilgisi eksik.';
}

if (empty($name)) {
    $errors[] = 'İsim alanı gereklidir.';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Geçerli bir e-posta adresi giriniz.';
}

if (empty($phone)) {
    $errors[] = 'Telefon alanı gereklidir.';
}

if (empty($serviceType)) {
    $errors[] = 'Hizmet türü seçmelisiniz.';
}

if (empty($date)) {
    $errors[] = 'Randevu tarihi seçmelisiniz.';
} elseif (strtotime($date) < strtotime(date('Y-m-d'))) {
    $errors[] = 'Geçmiş bir tarih seçilemez.';
}

if (empty($time)) {
    $errors[] = 'Randevu saati seçmelisiniz.';
}

// Hata varsa form sayfasına geri dön
if (!empty($errors)) {
    $_SESSION['flash_message'] = implode('<br>', $errors);
    $_SESSION['flash_type'] = 'error';
    $_SESSION['form_data'] = $_POST; // Form verilerini geri doldurabilmek için sakla
    header('Location: contact.php?trainer_id=' . $trainerId);
    exit;
}

try {
    // Veritabanı bağlantısı
    $db = connectDB();
    
    // Eğitmenin varlığını kontrol et
    $stmt = $db->prepare("SELECT id, name FROM trainers WHERE id = :id");
    $stmt->bindParam(':id', $trainerId);
    $stmt->execute();
    $trainer = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$trainer) {
        $_SESSION['flash_message'] = 'Belirtilen eğitmen bulunamadı.';
        $_SESSION['flash_type'] = 'error';
        header('Location: trainers.php');
        exit;
    }
    
    // Randevunun daha önce alınıp alınmadığını kontrol et
    $stmt = $db->prepare("
        SELECT id FROM trainer_bookings 
        WHERE trainer_id = :trainer_id 
        AND booking_date = :booking_date 
        AND booking_time = :booking_time 
        AND status = 'active'
    ");
    $stmt->bindParam(':trainer_id', $trainerId);
    $stmt->bindParam(':booking_date', $date);
    $stmt->bindParam(':booking_time', $time);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $_SESSION['flash_message'] = 'Seçtiğiniz tarih ve saatte randevu mevcut. Lütfen başka bir zaman dilimi seçiniz.';
        $_SESSION['flash_type'] = 'error';
        $_SESSION['form_data'] = $_POST;
        header('Location: contact.php?trainer_id=' . $trainerId);
        exit;
    }
    
    // Oturum bilgisini kontrol et
    $userId = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;
    
    // Randevu kaydını oluştur
    $stmt = $db->prepare("
        INSERT INTO trainer_bookings (
            trainer_id, user_id, client_name, client_email, client_phone, 
            service_type, booking_date, booking_time, notes, status, created_at
        ) VALUES (
            :trainer_id, :user_id, :client_name, :client_email, :client_phone,
            :service_type, :booking_date, :booking_time, :notes, 'pending', NOW()
        )
    ");
    
    $stmt->bindParam(':trainer_id', $trainerId);
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':client_name', $name);
    $stmt->bindParam(':client_email', $email);
    $stmt->bindParam(':client_phone', $phone);
    $stmt->bindParam(':service_type', $serviceType);
    $stmt->bindParam(':booking_date', $date);
    $stmt->bindParam(':booking_time', $time);
    $stmt->bindParam(':notes', $notes);
    $stmt->execute();
    
    $bookingId = $db->lastInsertId();
    
    // E-posta bildirimi gönder (örnek)
    $to = $email;
    $subject = "E-Salon - Randevu Talebi Alındı";
    
    $message = "
    <html>
    <head>
        <title>Randevu Talebiniz Alındı</title>
    </head>
    <body>
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;'>
            <h2 style='color: #333;'>Randevu Talebiniz Alındı</h2>
            <p>Sayın {$name},</p>
            <p>E-Salon'dan randevu talebinizi aldık. Randevu detayları aşağıdadır:</p>
            
            <div style='background-color: #f9f9f9; padding: 15px; border-radius: 4px; margin: 15px 0;'>
                <p><strong>Eğitmen:</strong> {$trainer['name']}</p>
                <p><strong>Hizmet:</strong> {$serviceType}</p>
                <p><strong>Tarih:</strong> {$date}</p>
                <p><strong>Saat:</strong> {$time}</p>
            </div>
            
            <p>Talebiniz onaylandığında size bilgi verilecektir. Sorularınız için bize ulaşabilirsiniz.</p>
            
            <p>Saygılarımızla,<br>E-Salon Ekibi</p>
        </div>
    </body>
    </html>
    ";
    
    // Always set content-type when sending HTML email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: E-Salon <noreply@e-salon.com>" . "\r\n";
    
    // E-posta göndermeyi dene
    // Not: Bu kısım bir e-posta sunucusu gerektirir
    // mail($to, $subject, $message, $headers);
    
    // Başarı mesajı
    $_SESSION['flash_message'] = 'Randevu talebiniz alınmıştır. Onay durumunu profilinizden takip edebilirsiniz.';
    $_SESSION['flash_type'] = 'success';
    
    // Eğitimci listesine veya anasayfaya yönlendir
    header('Location: trainers.php');
    exit;
    
} catch (PDOException $e) {
    $_SESSION['flash_message'] = 'Sistem hatası oluştu. Lütfen daha sonra tekrar deneyiniz.';
    $_SESSION['flash_type'] = 'error';
    error_log('Randevu kaydetme hatası: ' . $e->getMessage());
    header('Location: contact.php?trainer_id=' . $trainerId);
    exit;
} 