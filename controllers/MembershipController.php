<?php
/**
 * Üyelik işlemlerini yöneten sınıf
 */
class MembershipController {
    private $db;
    
    /**
     * Sınıf başlatıcı
     */
    public function __construct() {
        $this->db = connectDB();
    }
    
    /**
     * Kullanıcının üyeliklerini listele
     * 
     * @param int $userId Kullanıcı ID
     * @param bool $activeOnly Yalnızca aktif üyelikleri listele
     * @return array Üyelik listesi
     */
    public function getUserMemberships($userId, $activeOnly = false) {
        $query = "SELECT m.*, p.name as package_name, p.description as package_description, p.duration, p.price 
                  FROM memberships m 
                  JOIN packages p ON m.package_id = p.id 
                  WHERE m.user_id = :user_id";
                  
        if ($activeOnly) {
            $query .= " AND m.end_date >= CURDATE() AND m.status = 'active'";
        }
        
        $query .= " ORDER BY m.created_at DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Aktif üyelikleri listele
     * 
     * @return void
     */
    public function index() {
        // Yalnızca admin ve personel erişebilir
        if ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff') {
            setError('Bu sayfaya erişim izniniz yok.');
            redirect('index.php');
        }
        
        // Üyelik durumu filtresini al
        $status = isset($_GET['status']) ? $_GET['status'] : 'active';
        
        // Filtre ve arama değerleriyle üyelikleri getir
        $query = "SELECT m.*, 
                  u.name as user_name, 
                  u.email as user_email, 
                  p.name as package_name,
                  p.price as package_price,
                  p.duration as package_duration
                  FROM memberships m 
                  JOIN users u ON m.user_id = u.id 
                  JOIN packages p ON m.package_id = p.id";
                  
        $whereClause = [];
        $queryParams = [];
        
        // Durum filtresine göre koşul ekle
        if ($status == 'active') {
            $whereClause[] = "m.end_date >= CURDATE() AND m.status = 'active'";
        } elseif ($status == 'expired') {
            $whereClause[] = "m.end_date < CURDATE() OR m.status = 'expired'";
        } elseif ($status == 'all') {
            // Tüm üyelikler, ek koşul yok
        } else {
            $whereClause[] = "m.status = :status";
            $queryParams[':status'] = $status;
        }
        
        // Arama filtresi
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $search = '%' . $_GET['search'] . '%';
            $whereClause[] = "(u.name LIKE :search OR u.email LIKE :search OR p.name LIKE :search)";
            $queryParams[':search'] = $search;
        }
        
        // Where koşulunu ekle
        if (!empty($whereClause)) {
            $query .= " WHERE " . implode(' AND ', $whereClause);
        }
        
        // Sıralama
        $query .= " ORDER BY m.created_at DESC";
        
        $stmt = $this->db->prepare($query);
        
        // Parametreleri bağla
        foreach ($queryParams as $param => $value) {
            $stmt->bindValue($param, $value);
        }
        
        $stmt->execute();
        $memberships = $stmt->fetchAll();
        
        // Görünümü yükle
        $pageTitle = "Üyelik Listesi";
        include_once 'views/memberships/index.php';
    }
    
    /**
     * Belirli bir üyeliği göster
     * 
     * @param int $id Üyelik ID
     * @return void
     */
    public function show($id) {
        // Yalnızca admin ve personel, veya üyenin kendisi erişebilir
        $stmt = $this->db->prepare("SELECT m.*, 
                                   u.id as user_id, u.name as user_name, u.email as user_email, u.phone as user_phone,
                                   p.name as package_name, p.description as package_description, p.duration, p.price 
                                   FROM memberships m 
                                   JOIN users u ON m.user_id = u.id 
                                   JOIN packages p ON m.package_id = p.id 
                                   WHERE m.id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $membership = $stmt->fetch();
        
        if (!$membership) {
            setError('Üyelik bulunamadı.');
            redirect('memberships.php');
        }
        
        // Erişim kontrolü
        if ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff' && $_SESSION['user_id'] != $membership['user_id']) {
            setError('Bu sayfaya erişim izniniz yok.');
            redirect('index.php');
        }

        // Üyeliğe ait ödemeleri getir
        $stmt = $this->db->prepare("SELECT * FROM payments WHERE membership_id = :membership_id ORDER BY payment_date DESC");
        $stmt->bindParam(':membership_id', $id);
        $stmt->execute();
        $payments = $stmt->fetchAll();
        
        // Görünümü yükle
        $pageTitle = "Üyelik Detayı";
        include_once 'views/memberships/show.php';
    }
    
    /**
     * Yeni üyelik formu göster
     * 
     * @return void
     */
    public function create() {
        // Yalnızca admin ve personel erişebilir
        if ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff') {
            setError('Bu sayfaya erişim izniniz yok.');
            redirect('index.php');
        }
        
        // Kullanıcı listesini getir
        $stmt = $this->db->prepare("SELECT id, name, email FROM users WHERE role = 'member' ORDER BY name ASC");
        $stmt->execute();
        $users = $stmt->fetchAll();
        
        // Paket listesini getir
        $stmt = $this->db->prepare("SELECT id, name, duration, price FROM packages WHERE is_active = 1 ORDER BY price ASC");
        $stmt->execute();
        $packages = $stmt->fetchAll();
        
        $pageTitle = "Yeni Üyelik Oluştur";
        include_once 'views/memberships/create.php';
    }
    
    /**
     * Yeni üyelik oluştur
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
        
        // Gerekli alanları kontrol et
        if (empty($data['user_id']) || empty($data['package_id']) || empty($data['payment_type'])) {
            setError('Lütfen tüm alanları doldurun.');
            redirect('memberships.php?action=create');
            return;
        }
        
        // Kullanıcı ve paket varlığını kontrol et
        $stmt = $this->db->prepare("SELECT id FROM users WHERE id = :id AND role = 'member'");
        $stmt->bindParam(':id', $data['user_id']);
        $stmt->execute();
        
        if (!$stmt->fetch()) {
            setError('Geçersiz kullanıcı seçildi.');
            redirect('memberships.php?action=create');
            return;
        }
        
        $stmt = $this->db->prepare("SELECT id, price, duration FROM packages WHERE id = :id AND is_active = 1");
        $stmt->bindParam(':id', $data['package_id']);
        $stmt->execute();
        
        $package = $stmt->fetch();
        if (!$package) {
            setError('Geçersiz paket seçildi.');
            redirect('memberships.php?action=create');
            return;
        }
        
        // Başlangıç ve bitiş tarihlerini hesapla
        $startDate = date('Y-m-d');
        
        // Eğer başlangıç tarihi belirtilmişse
        if (!empty($data['start_date'])) {
            $startDate = $data['start_date'];
        }
        
        // Kullanıcının aktif üyeliği varsa ve uzatma seçilmişse
        if (isset($data['extend']) && $data['extend'] == 1) {
            $stmt = $this->db->prepare("SELECT end_date FROM memberships 
                                        WHERE user_id = :user_id AND end_date >= CURDATE() AND status = 'active'
                                        ORDER BY end_date DESC LIMIT 1");
            $stmt->bindParam(':user_id', $data['user_id']);
            $stmt->execute();
            $currentMembership = $stmt->fetch();
            
            if ($currentMembership) {
                $startDate = date('Y-m-d', strtotime($currentMembership['end_date'] . ' +1 day'));
            }
        }
        
        // Bitiş tarihini hesapla
        $endDate = date('Y-m-d', strtotime($startDate . ' +' . $package['duration'] . ' days'));
        
        // Verileri temizle
        $userId = (int)$data['user_id'];
        $packageId = (int)$data['package_id'];
        $paymentType = sanitize($data['payment_type']);
        $amount = $package['price'];
        
        // İndirim uygulanacaksa
        $discountAmount = 0;
        if (!empty($data['discount_amount']) && is_numeric($data['discount_amount'])) {
            $discountAmount = (float)$data['discount_amount'];
            if ($discountAmount > $amount) {
                $discountAmount = $amount;
            }
            $amount -= $discountAmount;
        }
        
        // Veritabanı işlemlerini başlat
        $this->db->beginTransaction();
        
        try {
            // Üyeliği oluştur
            $stmt = $this->db->prepare("INSERT INTO memberships (user_id, package_id, start_date, end_date, status, discount_amount, created_by, created_at) 
                                       VALUES (:user_id, :package_id, :start_date, :end_date, 'active', :discount_amount, :created_by, NOW())");
            
            $createdBy = $_SESSION['user_id'];
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':package_id', $packageId);
            $stmt->bindParam(':start_date', $startDate);
            $stmt->bindParam(':end_date', $endDate);
            $stmt->bindParam(':discount_amount', $discountAmount);
            $stmt->bindParam(':created_by', $createdBy);
            
            if (!$stmt->execute()) {
                throw new Exception('Üyelik oluşturulurken bir hata oluştu.');
            }
            
            $membershipId = $this->db->lastInsertId();
            
            // Ödeme kaydı oluştur
            $stmt = $this->db->prepare("INSERT INTO payments (user_id, membership_id, amount, payment_type, payment_date, created_by, created_at) 
                                       VALUES (:user_id, :membership_id, :amount, :payment_type, NOW(), :created_by, NOW())");
            
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':membership_id', $membershipId);
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':payment_type', $paymentType);
            $stmt->bindParam(':created_by', $createdBy);
            
            if (!$stmt->execute()) {
                throw new Exception('Ödeme kaydı oluşturulurken bir hata oluştu.');
            }
            
            // İşlemleri tamamla
            $this->db->commit();
            
            setSuccess('Üyelik başarıyla oluşturuldu.');
            redirect('memberships.php');
        } catch (Exception $e) {
            // Hata durumunda işlemleri geri al
            $this->db->rollBack();
            
            setError($e->getMessage());
            redirect('memberships.php?action=create');
        }
    }
    
    /**
     * Üyelik durumunu güncelle
     * 
     * @param int $id Üyelik ID
     * @param string $status Yeni durum
     * @return void
     */
    public function updateStatus($id, $status) {
        // Yalnızca admin ve personel erişebilir
        if ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff') {
            setError('Bu işlemi yapmaya yetkiniz yok.');
            redirect('index.php');
        }
        
        // Durum doğrulaması
        $validStatuses = ['active', 'suspended', 'expired', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            setError('Geçersiz üyelik durumu.');
            redirect('memberships.php');
            return;
        }
        
        // Üyeliği kontrol et
        $stmt = $this->db->prepare("SELECT * FROM memberships WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $membership = $stmt->fetch();
        if (!$membership) {
            setError('Üyelik bulunamadı.');
            redirect('memberships.php');
            return;
        }
        
        // Durumu güncelle
        $stmt = $this->db->prepare("UPDATE memberships SET status = :status, updated_at = NOW() WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':status', $status);
        
        if ($stmt->execute()) {
            setSuccess('Üyelik durumu başarıyla güncellendi.');
        } else {
            setError('Üyelik durumu güncellenirken bir hata oluştu.');
        }
        
        redirect('memberships.php?action=show&id=' . $id);
    }
    
    /**
     * Üyelik süresini uzat
     * 
     * @param int $id Üyelik ID
     * @param array $data Form verileri
     * @return void
     */
    public function extend($id, $data) {
        // Yalnızca admin ve personel erişebilir
        if ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff') {
            setError('Bu işlemi yapmaya yetkiniz yok.');
            redirect('index.php');
        }
        
        // Üyeliği kontrol et
        $stmt = $this->db->prepare("SELECT m.*, p.price, p.duration 
                                   FROM memberships m 
                                   JOIN packages p ON m.package_id = p.id 
                                   WHERE m.id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $membership = $stmt->fetch();
        if (!$membership) {
            setError('Üyelik bulunamadı.');
            redirect('memberships.php');
            return;
        }
        
        // Gerekli alanları kontrol et
        if (empty($data['duration']) || empty($data['payment_type'])) {
            setError('Lütfen tüm alanları doldurun.');
            redirect('memberships.php?action=show&id=' . $id);
            return;
        }
        
        // Uzatma süresini ve ücreti hesapla
        $duration = (int)$data['duration'];
        $price = $membership['price'];
        
        if ($duration <= 0) {
            setError('Geçersiz uzatma süresi.');
            redirect('memberships.php?action=show&id=' . $id);
            return;
        }
        
        // Günlük ücret hesapla
        $dailyRate = $price / $membership['duration'];
        $amount = $dailyRate * $duration;
        
        // İndirim uygulanacaksa
        $discountAmount = 0;
        if (!empty($data['discount_amount']) && is_numeric($data['discount_amount'])) {
            $discountAmount = (float)$data['discount_amount'];
            if ($discountAmount > $amount) {
                $discountAmount = $amount;
            }
            $amount -= $discountAmount;
        }
        
        // Yeni bitiş tarihini hesapla
        $newEndDate = date('Y-m-d', strtotime($membership['end_date'] . ' +' . $duration . ' days'));
        
        // Veritabanı işlemlerini başlat
        $this->db->beginTransaction();
        
        try {
            // Üyeliği güncelle
            $stmt = $this->db->prepare("UPDATE memberships 
                                       SET end_date = :end_date, 
                                           status = 'active', 
                                           updated_at = NOW() 
                                       WHERE id = :id");
            
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':end_date', $newEndDate);
            
            if (!$stmt->execute()) {
                throw new Exception('Üyelik güncellenirken bir hata oluştu.');
            }
            
            // Ödeme kaydı oluştur
            $stmt = $this->db->prepare("INSERT INTO payments (user_id, membership_id, amount, payment_type, payment_date, discount_amount, created_by, created_at) 
                                       VALUES (:user_id, :membership_id, :amount, :payment_type, NOW(), :discount_amount, :created_by, NOW())");
            
            $userId = $membership['user_id'];
            $paymentType = sanitize($data['payment_type']);
            $createdBy = $_SESSION['user_id'];
            
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':membership_id', $id);
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':payment_type', $paymentType);
            $stmt->bindParam(':discount_amount', $discountAmount);
            $stmt->bindParam(':created_by', $createdBy);
            
            if (!$stmt->execute()) {
                throw new Exception('Ödeme kaydı oluşturulurken bir hata oluştu.');
            }
            
            // İşlemleri tamamla
            $this->db->commit();
            
            setSuccess('Üyelik süresi başarıyla uzatıldı.');
            redirect('memberships.php?action=show&id=' . $id);
        } catch (Exception $e) {
            // Hata durumunda işlemleri geri al
            $this->db->rollBack();
            
            setError($e->getMessage());
            redirect('memberships.php?action=show&id=' . $id);
        }
    }
    
    /**
     * Kullanıcının aktif üyeliğini kontrol et
     * 
     * @param int $userId Kullanıcı ID
     * @return array|bool Aktif üyelik bilgileri veya false
     */
    public function getActiveMembership($userId) {
        $stmt = $this->db->prepare("SELECT m.*, p.name as package_name 
                                   FROM memberships m 
                                   JOIN packages p ON m.package_id = p.id 
                                   WHERE m.user_id = :user_id 
                                     AND m.end_date >= CURDATE() 
                                     AND m.status = 'active' 
                                   ORDER BY m.end_date DESC 
                                   LIMIT 1");
        
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        $membership = $stmt->fetch();
        
        return $membership ? $membership : false;
    }

    /**
     * Aktif olan üyeliklerin sayısını döndürür
     * @return int Aktif üyelik sayısı
     */
    public function getActiveMemberships() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM memberships WHERE status = 'active' AND end_date >= CURDATE()");
        return $stmt->fetchColumn();
    }
}
?> 