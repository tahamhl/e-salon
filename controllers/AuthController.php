<?php
/**
 * Kimlik doğrulama ve kullanıcı işlemlerini yöneten sınıf
 */
class AuthController {
    private $db;
    
    /**
     * Sınıf başlatıcı
     */
    public function __construct() {
        $this->db = connectDB();
    }
    
    /**
     * Kullanıcı girişi
     * 
     * @param array $data Form verileri
     * @return void
     */
    public function login($data) {
        // Gerekli alanların kontrolü
        if (empty($data['email']) || empty($data['password'])) {
            setError('Lütfen tüm alanları doldurun');
            return;
        }
        
        // Email formatı kontrolü
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            setError('Geçersiz email formatı');
            return;
        }
        
        // Veritabanından kullanıcı bilgilerini çek
        $email = sanitize($data['email']);
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $user = $stmt->fetch();
        
        // Kullanıcı var mı kontrol et
        if (!$user) {
            setError('Kullanıcı bulunamadı');
            return;
        }
        
        // Şifre doğrulama
        if (!verifyPassword($data['password'], $user['password'])) {
            setError('Şifre yanlış');
            return;
        }
        
        // Giriş başarılı, oturum değişkenlerini ayarla
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        
        // Ana sayfaya yönlendir
        redirect('index.php');
    }
    
    /**
     * Kullanıcı kaydı
     * 
     * @param array $data Form verileri
     * @return void
     */
    public function register($data) {
        // Gerekli alanların kontrolü
        if (empty($data['first_name']) || empty($data['last_name']) || 
            empty($data['email']) || empty($data['phone']) || 
            empty($data['password']) || empty($data['password_confirm'])) {
            setError('Lütfen tüm alanları doldurun');
            return;
        }
        
        // Email formatı kontrolü
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            setError('Geçersiz email formatı');
            return;
        }
        
        // Şifre eşleşme kontrolü
        if ($data['password'] !== $data['password_confirm']) {
            setError('Şifreler eşleşmiyor');
            return;
        }
        
        // Şifre uzunluk kontrolü
        if (strlen($data['password']) < 6) {
            setError('Şifre en az 6 karakter olmalıdır');
            return;
        }
        
        // Email kullanımda mı kontrolü
        $email = sanitize($data['email']);
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            setError('Bu e-posta adresi zaten kullanılıyor');
            return;
        }
        
        // Verileri temizle
        $firstName = sanitize($data['first_name']);
        $lastName = sanitize($data['last_name']);
        $phone = sanitize($data['phone']);
        $hashedPassword = hashPassword($data['password']);
        $role = 'member'; // Varsayılan üye rolü
        $created = date('Y-m-d H:i:s');
        
        // Kullanıcıyı veritabanına ekle
        $stmt = $this->db->prepare("INSERT INTO users (first_name, last_name, email, phone, password, role, created_at) 
                                    VALUES (:first_name, :last_name, :email, :phone, :password, :role, :created_at)");
        
        $stmt->bindParam(':first_name', $firstName);
        $stmt->bindParam(':last_name', $lastName);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':created_at', $created);
        
        if ($stmt->execute()) {
            // Başarılı kayıt, otomatik giriş yapılır
            $userId = $this->db->lastInsertId();
            
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_name'] = $firstName . ' ' . $lastName;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = $role;
            
            // Ana sayfaya yönlendir
            redirect('index.php');
        } else {
            setError('Kayıt sırasında bir hata oluştu');
        }
    }
    
    /**
     * Kullanıcı çıkışı
     * 
     * @return void
     */
    public function logout() {
        // Oturum değişkenlerini temizle
        session_unset();
        session_destroy();
        
        // Giriş sayfasına yönlendir
        redirect('login.php');
    }
}
?> 