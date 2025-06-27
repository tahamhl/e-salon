<?php
/**
 * Paket işlemlerini yöneten sınıf
 */
class PackageController {
    private $db;
    
    /**
     * Sınıf başlatıcı
     */
    public function __construct() {
        $this->db = connectDB();
    }
    
    /**
     * Paket listesini göster
     * 
     * @return void
     */
    public function index() {
        // Tüm paketleri getir
        $stmt = $this->db->prepare("SELECT * FROM packages ORDER BY price ASC");
        $stmt->execute();
        $packages = $stmt->fetchAll();
        
        // Görünümü yükle
        $pageTitle = "Paketler";
        include_once 'views/packages/index.php';
    }
    
    /**
     * Paket detayını göster
     * 
     * @param int $id Paket ID
     * @return void
     */
    public function show($id) {
        // Paket bilgilerini getir
        $stmt = $this->db->prepare("SELECT * FROM packages WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $package = $stmt->fetch();
        
        if (!$package) {
            setError('Paket bulunamadı.');
            redirect('packages.php');
        }
        
        // Paketi kullanan aktif üyelikleri say
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM memberships WHERE package_id = :id AND status = 'active'");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $activeUsage = $stmt->fetchColumn();
        
        // Görünümü yükle
        $pageTitle = $package['name'];
        include_once 'views/packages/show.php';
    }
    
    /**
     * Yeni paket formunu göster
     * 
     * @return void
     */
    public function create() {
        // Yalnızca admin erişebilir
        if ($_SESSION['user_role'] !== 'admin') {
            setError('Bu sayfaya erişim izniniz yok.');
            redirect('packages.php');
        }
        
        $pageTitle = "Yeni Paket Ekle";
        include_once 'views/packages/create.php';
    }
    
    /**
     * Yeni paket oluştur
     * 
     * @param array $data Form verileri
     * @return void
     */
    public function store($data) {
        // Yalnızca admin erişebilir
        if ($_SESSION['user_role'] !== 'admin') {
            setError('Bu işlemi yapmaya yetkiniz yok.');
            redirect('packages.php');
        }
        
        // Gerekli alanların kontrolü
        if (empty($data['name']) || empty($data['duration']) || empty($data['price'])) {
            setError('Lütfen tüm alanları doldurun.');
            redirect('packages.php?action=create');
            return;
        }
        
        // Fiyat ve süre sayısal olmalı
        if (!is_numeric($data['price']) || !is_numeric($data['duration'])) {
            setError('Fiyat ve süre sayısal değer olmalıdır.');
            redirect('packages.php?action=create');
            return;
        }
        
        // Verileri temizle
        $name = sanitize($data['name']);
        $description = !empty($data['description']) ? sanitize($data['description']) : null;
        $duration = (int)$data['duration'];
        $price = (float)$data['price'];
        $isActive = isset($data['is_active']) ? 1 : 0;
        $created = date('Y-m-d H:i:s');
        
        // Paketi veritabanına ekle
        $stmt = $this->db->prepare("INSERT INTO packages (name, description, duration, price, is_active, created_at) 
                                    VALUES (:name, :description, :duration, :price, :is_active, :created_at)");
        
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':duration', $duration);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':is_active', $isActive);
        $stmt->bindParam(':created_at', $created);
        
        if ($stmt->execute()) {
            setSuccess('Paket başarıyla oluşturuldu.');
            redirect('packages.php?action=manage');
        } else {
            setError('Paket oluşturulurken bir hata oluştu.');
            redirect('packages.php?action=create');
        }
    }
    
    /**
     * Paket düzenleme formunu göster
     * 
     * @param int $id Paket ID
     * @return void
     */
    public function edit($id) {
        // Yalnızca admin erişebilir
        if ($_SESSION['user_role'] !== 'admin') {
            setError('Bu sayfaya erişim izniniz yok.');
            redirect('packages.php');
        }
        
        // Paket bilgilerini getir
        $stmt = $this->db->prepare("SELECT * FROM packages WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $package = $stmt->fetch();
        
        if (!$package) {
            setError('Paket bulunamadı.');
            redirect('packages.php?action=manage');
        }
        
        $pageTitle = "Paket Düzenle";
        include_once 'views/packages/edit.php';
    }
    
    /**
     * Paket bilgilerini güncelle
     * 
     * @param int $id Paket ID
     * @param array $data Form verileri
     * @return void
     */
    public function update($id, $data) {
        // Yalnızca admin erişebilir
        if ($_SESSION['user_role'] !== 'admin') {
            setError('Bu işlemi yapmaya yetkiniz yok.');
            redirect('packages.php');
        }
        
        // Gerekli alanların kontrolü
        if (empty($data['name']) || empty($data['duration']) || empty($data['price'])) {
            setError('Lütfen tüm alanları doldurun.');
            redirect("packages.php?action=edit&id=$id");
            return;
        }
        
        // Fiyat ve süre sayısal olmalı
        if (!is_numeric($data['price']) || !is_numeric($data['duration'])) {
            setError('Fiyat ve süre sayısal değer olmalıdır.');
            redirect("packages.php?action=edit&id=$id");
            return;
        }
        
        // Verileri temizle
        $name = sanitize($data['name']);
        $description = !empty($data['description']) ? sanitize($data['description']) : null;
        $duration = (int)$data['duration'];
        $price = (float)$data['price'];
        $isActive = isset($data['is_active']) ? 1 : 0;
        $updated = date('Y-m-d H:i:s');
        
        // Paketi güncelle
        $stmt = $this->db->prepare("UPDATE packages SET 
                                     name = :name, 
                                     description = :description, 
                                     duration = :duration, 
                                     price = :price, 
                                     is_active = :is_active, 
                                     updated_at = :updated_at 
                                    WHERE id = :id");
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':duration', $duration);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':is_active', $isActive);
        $stmt->bindParam(':updated_at', $updated);
        
        if ($stmt->execute()) {
            setSuccess('Paket başarıyla güncellendi.');
            redirect('packages.php?action=manage');
        } else {
            setError('Paket güncellenirken bir hata oluştu.');
            redirect("packages.php?action=edit&id=$id");
        }
    }
    
    /**
     * Paket sil
     * 
     * @param int $id Paket ID
     * @return void
     */
    public function delete($id) {
        // Yalnızca admin erişebilir
        if ($_SESSION['user_role'] !== 'admin') {
            setError('Bu işlemi yapmaya yetkiniz yok.');
            redirect('packages.php');
        }
        
        // İlişkili üyelik var mı kontrol et
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM memberships WHERE package_id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch();
        
        if ($result['count'] > 0) {
            setError('Bu paketi kullanan üyelikler var. Paket silinemez.');
            redirect('packages.php?action=manage');
            return;
        }
        
        // Paketi sil
        $stmt = $this->db->prepare("DELETE FROM packages WHERE id = :id");
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            setSuccess('Paket başarıyla silindi.');
        } else {
            setError('Paket silinirken bir hata oluştu.');
        }
        
        redirect('packages.php?action=manage');
    }
    
    /**
     * Paket yönetim panelini göster
     * 
     * @return void
     */
    public function manage() {
        // Yalnızca admin erişebilir
        if ($_SESSION['user_role'] !== 'admin') {
            setError('Bu sayfaya erişim izniniz yok.');
            redirect('packages.php');
        }
        
        // Tüm paketleri getir
        $stmt = $this->db->prepare("SELECT * FROM packages ORDER BY is_active DESC, price ASC");
        $stmt->execute();
        $packages = $stmt->fetchAll();
        
        // Her paket için üyelik sayısını getir
        foreach ($packages as &$package) {
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM memberships WHERE package_id = :id");
            $stmt->bindParam(':id', $package['id']);
            $stmt->execute();
            $result = $stmt->fetch();
            $package['membership_count'] = $result['count'];
        }
        
        $pageTitle = "Paket Yönetimi";
        include_once 'views/packages/manage.php';
    }
    
    /**
     * Paket satın alma sayfasını göster
     * 
     * @param int $id Paket ID
     * @return void
     */
    public function purchase($id) {
        // Paket bilgilerini getir
        $stmt = $this->db->prepare("SELECT * FROM packages WHERE id = :id AND is_active = 1");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $package = $stmt->fetch();
        
        if (!$package) {
            setError('Paket bulunamadı veya aktif değil.');
            redirect('packages.php');
        }
        
        // Mevcut kullanıcının aktif üyeliği var mı kontrol et
        $userId = $_SESSION['user_id'];
        $stmt = $this->db->prepare("SELECT * FROM memberships WHERE user_id = :user_id AND end_date >= CURDATE() ORDER BY end_date DESC LIMIT 1");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $currentMembership = $stmt->fetch();
        
        $pageTitle = "Paket Satın Al";
        include_once 'views/packages/purchase.php';
    }
    
    /**
     * Paket satın alma işlemini tamamla
     * 
     * @param int $id Paket ID
     * @param array $data Form verileri
     * @return void
     */
    public function completePurchase($id, $data) {
        // Kullanıcının giriş yapmış olması gerekiyor
        if (!isset($_SESSION['user_id'])) {
            setError('Paket satın almak için giriş yapmalısınız.');
            redirect('login.php');
        }
        
        // Gerekli alanların kontrolü
        if (empty($data['payment_type'])) {
            setError('Lütfen ödeme tipini seçin.');
            redirect("packages.php?action=purchase&id=$id");
            return;
        }
        
        // Paket bilgilerini getir
        $stmt = $this->db->prepare("SELECT * FROM packages WHERE id = :id AND is_active = 1");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $package = $stmt->fetch();
        
        if (!$package) {
            setError('Paket bulunamadı veya aktif değil.');
            redirect('packages.php');
        }
        
        // Verileri temizle
        $userId = $_SESSION['user_id'];
        $paymentType = sanitize($data['payment_type']);
        $amount = $package['price'];
        $currentDate = date('Y-m-d');
        $startDate = $currentDate;
        
        // Kullanıcının aktif üyeliği varsa ve uzatma seçilmişse, mevcut üyelik bitiminden başlat
        if (isset($data['extend']) && $data['extend'] == 1) {
            $stmt = $this->db->prepare("SELECT end_date FROM memberships WHERE user_id = :user_id AND end_date >= CURDATE() ORDER BY end_date DESC LIMIT 1");
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            $currentMembership = $stmt->fetch();
            
            if ($currentMembership) {
                $startDate = date('Y-m-d', strtotime($currentMembership['end_date'] . ' +1 day'));
            }
        }
        
        // Bitiş tarihini hesapla
        $endDate = date('Y-m-d', strtotime($startDate . ' +' . $package['duration'] . ' days'));
        
        // Veritabanı işlemlerini başlat
        $this->db->beginTransaction();
        
        try {
            // Üyelik oluştur
            $stmt = $this->db->prepare("INSERT INTO memberships (user_id, package_id, start_date, end_date, status, created_at) 
                                        VALUES (:user_id, :package_id, :start_date, :end_date, 'active', NOW())");
            
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':package_id', $id);
            $stmt->bindParam(':start_date', $startDate);
            $stmt->bindParam(':end_date', $endDate);
            
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
            $stmt->bindParam(':created_by', $userId);
            
            if (!$stmt->execute()) {
                throw new Exception('Ödeme kaydı oluşturulurken bir hata oluştu.');
            }
            
            // İşlemleri tamamla
            $this->db->commit();
            
            setSuccess('Üyelik satın alma işlemi başarıyla tamamlandı.');
            redirect('index.php');
        } catch (Exception $e) {
            // Hata durumunda işlemleri geri al
            $this->db->rollBack();
            
            setError($e->getMessage());
            redirect("packages.php?action=purchase&id=$id");
        }
    }
}
?> 