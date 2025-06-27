<?php
/**
 * Randevu işleme dosyası
 */
require_once 'config/init.php';

// Oturum başlatma
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Sadece POST isteklerini işle
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
    exit;
}

// Form verilerini al ve temizle
$trainer_id = isset($_POST['trainer_id']) ? (int)$_POST['trainer_id'] : 0;
$name = htmlspecialchars(trim($_POST['name'] ?? ''));
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$phone = htmlspecialchars(trim($_POST['phone'] ?? ''));
$booking_date = htmlspecialchars(trim($_POST['booking_date'] ?? ''));
$booking_time = htmlspecialchars(trim($_POST['booking_time'] ?? ''));
$package = htmlspecialchars(trim($_POST['package'] ?? ''));
$notes = htmlspecialchars(trim($_POST['notes'] ?? ''));
$agree = isset($_POST['agree']);

// Hata bayrağı
$error = false;

// Form doğrulama
if (empty($trainer_id)) {
    $_SESSION['flash_message'] = 'Eğitmen bilgisi eksik.';
    $_SESSION['flash_type'] = 'error';
    $error = true;
} elseif (empty($name)) {
    $_SESSION['flash_message'] = 'Lütfen isim ve soyisim bilgilerinizi girin.';
    $_SESSION['flash_type'] = 'error';
    $error = true;
} elseif (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['flash_message'] = 'Lütfen geçerli bir e-posta adresi girin.';
    $_SESSION['flash_type'] = 'error';
    $error = true;
} elseif (empty($phone)) {
    $_SESSION['flash_message'] = 'Lütfen telefon numaranızı girin.';
    $_SESSION['flash_type'] = 'error';
    $error = true;
} elseif (empty($booking_date)) {
    $_SESSION['flash_message'] = 'Lütfen randevu tarihi seçin.';
    $_SESSION['flash_type'] = 'error';
    $error = true;
} elseif (empty($booking_time)) {
    $_SESSION['flash_message'] = 'Lütfen randevu saati seçin.';
    $_SESSION['flash_type'] = 'error';
    $error = true;
} elseif (empty($package)) {
    $_SESSION['flash_message'] = 'Lütfen seans paketi seçin.';
    $_SESSION['flash_type'] = 'error';
    $error = true;
} elseif (!$agree) {
    $_SESSION['flash_message'] = 'Lütfen randevu şartlarını ve iptal politikasını kabul edin.';
    $_SESSION['flash_type'] = 'error';
    $error = true;
}

// Tarihin bugün veya daha sonra olduğunu kontrol et
$today = date('Y-m-d');
if (strtotime($booking_date) < strtotime($today)) {
    $_SESSION['flash_message'] = 'Geçmiş bir tarih için randevu oluşturamazsınız.';
    $_SESSION['flash_type'] = 'error';
    $error = true;
}

// Paket bilgisine göre fiyat ve ders sayısı belirleme
$price = 0;
$sessions = 1;

if ($package === 'single') {
    $price = 200;
    $sessions = 1;
} elseif ($package === 'five_sessions') {
    $price = 900;
    $sessions = 5;
} else {
    $_SESSION['flash_message'] = 'Geçersiz paket seçimi.';
    $_SESSION['flash_type'] = 'error';
    $error = true;
}

// Hata yoksa veritabanına kaydet
if (!$error) {
    try {
        // Veritabanına bağlan
        $db = connectDB();
        
        // İlk olarak müşteri mevcut mu kontrol et, değilse oluştur
        $stmt = $db->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $user_id = 0;
        
        if ($user) {
            $user_id = $user['id'];
        } else {
            // Yeni kullanıcı oluştur
            $password = generateToken(8); // Rastgele şifre
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $created_at = date('Y-m-d H:i:s');
            
            $stmt = $db->prepare("INSERT INTO users (first_name, last_name, email, phone, password, role, is_active, created_at) 
                                VALUES (:name, '', :email, :phone, :password, 'member', 1, :created_at)");
            
            // İsim ve soyisimi ayır (varsayılan olarak boşluğa göre)
            $name_parts = explode(' ', $name, 2);
            $first_name = $name_parts[0];
            $last_name = isset($name_parts[1]) ? $name_parts[1] : '';
            
            $stmt->bindParam(':name', $first_name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':created_at', $created_at);
            
            if ($stmt->execute()) {
                $user_id = $db->lastInsertId();
                
                // Burada kullanıcıya e-posta gönderilebilir
                // sendWelcomeEmail($email, $first_name, $password);
            }
        }
        
        // Randevu kaydını oluştur
        if ($user_id > 0) {
            // Tablo trainer_bookings mevcut mu kontrol et, yoksa oluştur
            $tableCheck = $db->query("SHOW TABLES LIKE 'trainer_bookings'");
            if ($tableCheck->rowCount() == 0) {
                // Tablo yok, oluştur
                $db->exec("CREATE TABLE trainer_bookings (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    trainer_id INT NOT NULL,
                    booking_date DATE NOT NULL,
                    booking_time VARCHAR(10) NOT NULL,
                    package VARCHAR(20) NOT NULL,
                    sessions INT NOT NULL,
                    price DECIMAL(10,2) NOT NULL,
                    notes TEXT,
                    status ENUM('pending', 'confirmed', 'completed', 'cancelled', 'active') NOT NULL DEFAULT 'pending',
                    created_at DATETIME NOT NULL,
                    updated_at DATETIME,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                    FOREIGN KEY (trainer_id) REFERENCES users(id) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            }
            
            $created_at = date('Y-m-d H:i:s');
            
            $stmt = $db->prepare("INSERT INTO trainer_bookings (user_id, trainer_id, booking_date, booking_time, package, sessions, price, notes, status, created_at) 
                                VALUES (:user_id, :trainer_id, :booking_date, :booking_time, :package, :sessions, :price, :notes, 'pending', :created_at)");
                                
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':trainer_id', $trainer_id);
            $stmt->bindParam(':booking_date', $booking_date);
            $stmt->bindParam(':booking_time', $booking_time);
            $stmt->bindParam(':package', $package);
            $stmt->bindParam(':sessions', $sessions);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':notes', $notes);
            $stmt->bindParam(':created_at', $created_at);
            
            if ($stmt->execute()) {
                $booking_id = $db->lastInsertId();
                
                // Başarı mesajı
                $_SESSION['flash_message'] = 'Randevunuz başarıyla oluşturuldu. Onay için sizinle iletişime geçilecektir.';
                $_SESSION['flash_type'] = 'success';
                
                // İsteğe bağlı: Eğitmene ve müşteriye e-posta bildirimi gönderme
                // sendBookingConfirmationEmail($email, $name, $booking_date, $booking_time);
                // sendBookingNotificationToTrainer($trainer_id, $name, $booking_date, $booking_time);
                
                // Eğitmen detay sayfasına yönlendir
                redirect('trainers.php?action=show&id=' . $trainer_id);
                exit;
            } else {
                // Hata mesajı
                $_SESSION['flash_message'] = 'Randevu oluşturulurken bir hata oluştu. Lütfen daha sonra tekrar deneyiniz.';
                $_SESSION['flash_type'] = 'error';
            }
        } else {
            // Kullanıcı oluşturulamadı
            $_SESSION['flash_message'] = 'Kullanıcı profili oluşturulurken bir hata oluştu. Lütfen daha sonra tekrar deneyiniz.';
            $_SESSION['flash_type'] = 'error';
        }
    } catch (PDOException $e) {
        // Veritabanı hatası
        $_SESSION['flash_message'] = 'Sistem hatası: ' . $e->getMessage();
        $_SESSION['flash_type'] = 'error';
        error_log('Randevu oluşturma hatası: ' . $e->getMessage());
    }
}

// Eğitmen randevu sayfasına geri dön
redirect('contact.php?trainer_id=' . $trainer_id);
exit;
?> 