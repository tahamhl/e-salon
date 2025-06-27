<?php
/**
 * Kullanıcı işlemlerini yöneten sınıf
 */
class UserController {
    private $db;
    
    /**
     * Sınıf başlatıcı
     */
    public function __construct() {
        $this->db = connectDB();
    }
    
    /**
     * Kullanıcı listesini göster
     * 
     * @return void
     */
    public function index() {
        // Yalnızca admin erişebilir
        if ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff') {
            setError('Bu sayfaya erişim izniniz yok.');
            redirect('index.php');
        }
        
        $users = [];
        $search = "";
        $role = "";
        
        // Filtreleme
        if (isset($_GET['search']) || isset($_GET['role'])) {
            $search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
            $role = isset($_GET['role']) ? sanitize($_GET['role']) : '';
            
            $query = "SELECT * FROM users WHERE 1=1";
            
            if (!empty($search)) {
                $search = "%$search%";
                $query .= " AND (first_name LIKE :search OR last_name LIKE :search OR email LIKE :search)";
            }
            
            if (!empty($role)) {
                $query .= " AND role = :role";
            }
            
            $query .= " ORDER BY created_at DESC";
            
            $stmt = $this->db->prepare($query);
            
            if (!empty($search)) {
                $stmt->bindParam(':search', $search);
            }
            
            if (!empty($role)) {
                $stmt->bindParam(':role', $role);
            }
        } else {
            // Tüm kullanıcıları getir
            $stmt = $this->db->prepare("SELECT * FROM users ORDER BY created_at DESC");
        }
        
        $stmt->execute();
        $users = $stmt->fetchAll();
        
        // Görünümü yükle
        $pageTitle = "Kullanıcı Yönetimi";
        include_once 'views/users/index.php';
    }
    
    /**
     * Yeni kullanıcı formunu göster
     * 
     * @return void
     */
    public function create() {
        // Yalnızca admin ve personel erişebilir
        if ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff') {
            setError('Bu sayfaya erişim izniniz yok.');
            redirect('index.php');
        }
        
        $pageTitle = "Yeni Kullanıcı";
        include_once 'views/users/create.php';
    }
    
    /**
     * Yeni kullanıcı oluştur
     * 
     * @param array $data Form verileri
     * @return void
     */
    public function store($data) {
        // Yalnızca admin ve personel erişebilir
        if ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff') {
            setError('Bu işlemi yapmaya yetkiniz yok.');
            redirect('index.php');
        }
        
        // Gerekli alanların kontrolü
        if (empty($data['first_name']) || empty($data['last_name']) || 
            empty($data['email']) || empty($data['phone']) || 
            empty($data['password']) || empty($data['password_confirm'])) {
            setError('Lütfen tüm alanları doldurun.');
            redirect('users.php?action=create');
            return;
        }
        
        // Email formatı kontrolü
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            setError('Geçersiz email formatı.');
            redirect('users.php?action=create');
            return;
        }
        
        // Şifre eşleşme kontrolü
        if ($data['password'] !== $data['password_confirm']) {
            setError('Şifreler eşleşmiyor.');
            redirect('users.php?action=create');
            return;
        }
        
        // Şifre uzunluk kontrolü
        if (strlen($data['password']) < 6) {
            setError('Şifre en az 6 karakter olmalıdır.');
            redirect('users.php?action=create');
            return;
        }
        
        // Email kullanımda mı kontrolü
        $email = sanitize($data['email']);
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            setError('Bu e-posta adresi zaten kullanılıyor.');
            redirect('users.php?action=create');
            return;
        }
        
        // Verileri temizle
        $firstName = sanitize($data['first_name']);
        $lastName = sanitize($data['last_name']);
        $phone = sanitize($data['phone']);
        $role = isset($data['role']) && $_SESSION['user_role'] === 'admin' ? sanitize($data['role']) : 'member';
        $hashedPassword = hashPassword($data['password']);
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
            setSuccess('Kullanıcı başarıyla oluşturuldu.');
            redirect('users.php');
        } else {
            setError('Kullanıcı oluşturulurken bir hata oluştu.');
            redirect('users.php?action=create');
        }
    }
    
    /**
     * Kullanıcı düzenleme formunu göster
     * 
     * @param int $id Kullanıcı ID
     * @return void
     */
    public function edit($id) {
        // Yalnızca admin her kullanıcıyı düzenleyebilir
        // Kullanıcı kendi profilini düzenleyebilir
        if ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_id'] != $id) {
            setError('Bu sayfaya erişim izniniz yok.');
            redirect('index.php');
        }
        
        // Kullanıcı bilgilerini getir
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $user = $stmt->fetch();
        
        if (!$user) {
            setError('Kullanıcı bulunamadı.');
            redirect('users.php');
        }
        
        $pageTitle = "Kullanıcı Düzenle";
        include_once 'views/users/edit.php';
    }
    
    /**
     * Kullanıcı bilgilerini güncelle
     * 
     * @param int $id Kullanıcı ID
     * @param array $data Form verileri
     * @return void
     */
    public function update($id, $data) {
        // Yalnızca admin her kullanıcıyı düzenleyebilir
        // Kullanıcı kendi profilini düzenleyebilir
        if ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_id'] != $id) {
            setError('Bu işlemi yapmaya yetkiniz yok.');
            redirect('index.php');
        }
        
        // Gerekli alanların kontrolü
        if (empty($data['first_name']) || empty($data['last_name']) || 
            empty($data['email']) || empty($data['phone'])) {
            setError('Lütfen tüm alanları doldurun.');
            redirect("users.php?action=edit&id=$id");
            return;
        }
        
        // Email formatı kontrolü
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            setError('Geçersiz email formatı.');
            redirect("users.php?action=edit&id=$id");
            return;
        }
        
        // Email kullanımda mı kontrolü (kendisi hariç)
        $email = sanitize($data['email']);
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = :email AND id != :id");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            setError('Bu e-posta adresi zaten kullanılıyor.');
            redirect("users.php?action=edit&id=$id");
            return;
        }
        
        // Verileri temizle
        $firstName = sanitize($data['first_name']);
        $lastName = sanitize($data['last_name']);
        $phone = sanitize($data['phone']);
        
        // Güncellenecek alanları hazırla
        $updateFields = "first_name = :first_name, last_name = :last_name, email = :email, phone = :phone";
        $params = [
            ':id' => $id,
            ':first_name' => $firstName,
            ':last_name' => $lastName,
            ':email' => $email,
            ':phone' => $phone
        ];
        
        // Rolü yalnızca admin değiştirebilir
        if (isset($data['role']) && $_SESSION['user_role'] === 'admin') {
            $role = sanitize($data['role']);
            $updateFields .= ", role = :role";
            $params[':role'] = $role;
        }
        
        // Şifre değişikliği isteğe bağlı
        if (!empty($data['password'])) {
            // Şifre kontrolü
            if (empty($data['password_confirm']) || $data['password'] !== $data['password_confirm']) {
                setError('Şifreler eşleşmiyor.');
                redirect("users.php?action=edit&id=$id");
                return;
            }
            
            // Şifre uzunluk kontrolü
            if (strlen($data['password']) < 6) {
                setError('Şifre en az 6 karakter olmalıdır.');
                redirect("users.php?action=edit&id=$id");
                return;
            }
            
            $hashedPassword = hashPassword($data['password']);
            $updateFields .= ", password = :password";
            $params[':password'] = $hashedPassword;
        }
        
        // Güncelleme tarihi
        $updated = date('Y-m-d H:i:s');
        $updateFields .= ", updated_at = :updated_at";
        $params[':updated_at'] = $updated;
        
        // Kullanıcıyı güncelle
        $stmt = $this->db->prepare("UPDATE users SET $updateFields WHERE id = :id");
        
        foreach ($params as $key => $value) {
            $stmt->bindParam($key, $value);
        }
        
        if ($stmt->execute()) {
            // Oturum verilerini güncelle, eğer kendi profiliyse
            if ($_SESSION['user_id'] == $id) {
                $_SESSION['user_name'] = $firstName . ' ' . $lastName;
                $_SESSION['user_email'] = $email;
                
                if (isset($role)) {
                    $_SESSION['user_role'] = $role;
                }
            }
            
            setSuccess('Kullanıcı bilgileri başarıyla güncellendi.');
            
            // Yönlendirme: Admin için kullanıcı listesine, normal kullanıcı için anasayfaya
            if ($_SESSION['user_role'] === 'admin' && $_SESSION['user_id'] != $id) {
                redirect('users.php');
            } else {
                redirect('index.php');
            }
        } else {
            setError('Kullanıcı güncellenirken bir hata oluştu.');
            redirect("users.php?action=edit&id=$id");
        }
    }
    
    /**
     * Kullanıcı detaylarını göster
     * 
     * @param int $id Kullanıcı ID
     * @return void
     */
    public function show($id) {
        // Yalnızca admin veya personel erişebilir
        // Kullanıcı kendi profilini görüntüleyebilir
        if ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff' && $_SESSION['user_id'] != $id) {
            setError('Bu sayfaya erişim izniniz yok.');
            redirect('index.php');
        }
        
        // Kullanıcı bilgilerini getir
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $user = $stmt->fetch();
        
        if (!$user) {
            setError('Kullanıcı bulunamadı.');
            redirect('users.php');
        }
        
        // Üyelik bilgilerini getir
        $stmtMembership = $this->db->prepare("
            SELECT m.*, p.name as package_name, p.description as package_description
            FROM memberships m
            JOIN packages p ON m.package_id = p.id
            WHERE m.user_id = :user_id
            ORDER BY m.end_date DESC
            LIMIT 1
        ");
        $stmtMembership->bindParam(':user_id', $id);
        $stmtMembership->execute();
        $membership = $stmtMembership->fetch();
        
        // Son 5 ziyaret
        $stmtVisits = $this->db->prepare("
            SELECT * FROM visits 
            WHERE user_id = :user_id 
            ORDER BY check_in_time DESC 
            LIMIT 5
        ");
        $stmtVisits->bindParam(':user_id', $id);
        $stmtVisits->execute();
        $visits = $stmtVisits->fetchAll();
        
        // Son 5 ödeme
        $stmtPayments = $this->db->prepare("
            SELECT * FROM payments 
            WHERE user_id = :user_id 
            ORDER BY payment_date DESC 
            LIMIT 5
        ");
        $stmtPayments->bindParam(':user_id', $id);
        $stmtPayments->execute();
        $payments = $stmtPayments->fetchAll();
        
        $pageTitle = "Kullanıcı Detayları";
        include_once 'views/users/show.php';
    }
    
    /**
     * Kullanıcı sil
     * 
     * @param int $id Kullanıcı ID
     * @return void
     */
    public function delete($id) {
        // Yalnızca admin silebilir
        if ($_SESSION['user_role'] !== 'admin') {
            setError('Bu işlemi yapmaya yetkiniz yok.');
            redirect('index.php');
        }
        
        // Kendi hesabını silmeyi engelle
        if ($_SESSION['user_id'] == $id) {
            setError('Kendi hesabınızı silemezsiniz.');
            redirect('users.php');
            return;
        }
        
        // Kullanıcıyı sil
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            setSuccess('Kullanıcı başarıyla silindi.');
        } else {
            setError('Kullanıcı silinirken bir hata oluştu.');
        }
        
        redirect('users.php');
    }
    
    /**
     * Profil sayfasını göster
     * 
     * @return void
     */
    public function profile() {
        $id = $_SESSION['user_id'];
        
        // Kullanıcı bilgilerini getir
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $user = $stmt->fetch();
        
        $pageTitle = "Profilim";
        include_once 'views/users/profile.php';
    }

    /**
     * Kullanıcı ID'sine göre kullanıcı bilgilerini getir
     * 
     * @param int $id Kullanıcı ID
     * @return array|false Kullanıcı bilgileri veya false
     */
    public function getUserById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Kullanıcı profilini güncelle
     * 
     * @param int $userId Kullanıcı ID
     * @param array $data Güncellenecek profil verileri
     * @return bool Güncelleme durumu
     */
    public function updateProfile($userId, $data) {
        // Temel bilgileri hazırla
        $name = sanitize($data['name'] ?? '');
        $surname = sanitize($data['surname'] ?? '');
        $email = sanitize($data['email'] ?? '');
        $phone = sanitize($data['phone'] ?? '');
        $address = sanitize($data['address'] ?? '');
        $birthDate = !empty($data['birth_date']) ? $data['birth_date'] : null;
        $gender = sanitize($data['gender'] ?? '');
        $emergencyContact = sanitize($data['emergency_contact'] ?? '');
        $emergencyPhone = sanitize($data['emergency_phone'] ?? '');
        $updated = date('Y-m-d H:i:s');
        
        // Email kullanımda mı kontrolü (kendisi hariç)
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = :email AND id != :id");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            setError('Bu e-posta adresi zaten kullanılıyor.');
            return false;
        }
        
        // Profili güncelle
        $stmt = $this->db->prepare("
            UPDATE users 
            SET name = :name, 
                surname = :surname, 
                email = :email, 
                phone = :phone, 
                address = :address, 
                birth_date = :birth_date, 
                gender = :gender, 
                emergency_contact = :emergency_contact, 
                emergency_phone = :emergency_phone, 
                updated_at = :updated_at 
            WHERE id = :id
        ");
        
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':surname', $surname);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':birth_date', $birthDate);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':emergency_contact', $emergencyContact);
        $stmt->bindParam(':emergency_phone', $emergencyPhone);
        $stmt->bindParam(':updated_at', $updated);
        $stmt->bindParam(':id', $userId);
        
        if ($stmt->execute()) {
            setSuccess('Profiliniz başarıyla güncellendi.');
            return true;
        } else {
            setError('Profil güncellenirken bir hata oluştu.');
            return false;
        }
    }

    /**
     * Kullanıcı şifresini değiştir
     * 
     * @param int $userId Kullanıcı ID
     * @param string $currentPassword Mevcut şifre
     * @param string $newPassword Yeni şifre
     * @return bool Değiştirme durumu
     */
    public function changePassword($userId, $currentPassword, $newPassword) {
        // Mevcut şifreyi kontrol et
        $stmt = $this->db->prepare("SELECT password FROM users WHERE id = :id");
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        
        $user = $stmt->fetch();
        
        if (!$user) {
            setError('Kullanıcı bulunamadı.');
            return false;
        }
        
        // Şifre doğrulaması
        if (!verifyPassword($currentPassword, $user['password'])) {
            setError('Mevcut şifre yanlış.');
            return false;
        }
        
        // Yeni şifreyi hashle
        $hashedPassword = hashPassword($newPassword);
        $updated = date('Y-m-d H:i:s');
        
        // Şifreyi güncelle
        $stmt = $this->db->prepare("UPDATE users SET password = :password, updated_at = :updated_at WHERE id = :id");
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':updated_at', $updated);
        $stmt->bindParam(':id', $userId);
        
        if ($stmt->execute()) {
            return true;
        } else {
            setError('Şifre değiştirilirken bir hata oluştu.');
            return false;
        }
    }

    /**
     * Toplam kullanıcı sayısını döndürür
     * @return int Toplam kullanıcı sayısı
     */
    public function getTotalUsers() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM users");
        return $stmt->fetchColumn();
    }
}
?> 