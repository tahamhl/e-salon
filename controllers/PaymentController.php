<?php
/**
 * Ödeme işlemlerini yöneten sınıf
 */
class PaymentController {
    private $db;
    
    /**
     * Sınıf başlatıcı
     */
    public function __construct() {
        $this->db = connectDB();
    }
    
    /**
     * Tüm ödemeleri listele
     * 
     * @return void
     */
    public function index() {
        // Yalnızca admin ve personel erişebilir
        if ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff') {
            setError('Bu sayfaya erişim izniniz yok.');
            redirect('index.php');
        }
        
        // Filtreleri al
        $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
        $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
        $paymentType = isset($_GET['payment_type']) ? $_GET['payment_type'] : '';
        
        // Sorguyu oluştur
        $query = "SELECT p.*, 
                  u.name as user_name, 
                  u.email as user_email,
                  m.id as membership_id,
                  pk.name as package_name,
                  s.name as staff_name
                  FROM payments p 
                  JOIN users u ON p.user_id = u.id 
                  LEFT JOIN memberships m ON p.membership_id = m.id
                  LEFT JOIN packages pk ON m.package_id = pk.id
                  LEFT JOIN users s ON p.created_by = s.id
                  WHERE p.payment_date BETWEEN :start_date AND :end_date";
                  
        $queryParams = [
            ':start_date' => $startDate . ' 00:00:00',
            ':end_date' => $endDate . ' 23:59:59'
        ];
        
        // Ödeme tipine göre filtrele
        if (!empty($paymentType)) {
            $query .= " AND p.payment_type = :payment_type";
            $queryParams[':payment_type'] = $paymentType;
        }
        
        // Arama filtresi
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $search = '%' . $_GET['search'] . '%';
            $query .= " AND (u.name LIKE :search OR u.email LIKE :search OR pk.name LIKE :search)";
            $queryParams[':search'] = $search;
        }
        
        // Sıralama
        $query .= " ORDER BY p.payment_date DESC";
        
        $stmt = $this->db->prepare($query);
        
        // Parametreleri bağla
        foreach ($queryParams as $param => $value) {
            $stmt->bindValue($param, $value);
        }
        
        $stmt->execute();
        $payments = $stmt->fetchAll();
        
        // Toplam tutarı hesapla
        $totalAmount = 0;
        foreach ($payments as $payment) {
            $totalAmount += $payment['amount'];
        }
        
        // Ödeme tiplerini getir
        $stmt = $this->db->prepare("SELECT DISTINCT payment_type FROM payments ORDER BY payment_type");
        $stmt->execute();
        $paymentTypes = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Görünümü yükle
        $pageTitle = "Ödeme Listesi";
        include_once 'views/payments/index.php';
    }
    
    /**
     * Belirli bir ödemeyi göster
     * 
     * @param int $id Ödeme ID
     * @return void
     */
    public function show($id) {
        // Yalnızca admin ve personel erişebilir
        if ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff') {
            setError('Bu sayfaya erişim izniniz yok.');
            redirect('index.php');
        }
        
        $stmt = $this->db->prepare("SELECT p.*, 
                                   u.name as user_name, u.email as user_email, u.phone as user_phone,
                                   m.id as membership_id, m.start_date, m.end_date,
                                   pk.name as package_name, pk.description as package_description,
                                   s.name as staff_name
                                   FROM payments p 
                                   JOIN users u ON p.user_id = u.id 
                                   LEFT JOIN memberships m ON p.membership_id = m.id
                                   LEFT JOIN packages pk ON m.package_id = pk.id
                                   LEFT JOIN users s ON p.created_by = s.id
                                   WHERE p.id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $payment = $stmt->fetch();
        
        if (!$payment) {
            setError('Ödeme bulunamadı.');
            redirect('payments.php');
        }
        
        // Görünümü yükle
        $pageTitle = "Ödeme Detayı";
        include_once 'views/payments/show.php';
    }
    
    /**
     * Yeni ödeme formu göster
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
        
        // Görünümü yükle
        $pageTitle = "Yeni Ödeme Oluştur";
        include_once 'views/payments/create.php';
    }
    
    /**
     * Kullanıcı seçtiğinde üyeliklerini getir
     * 
     * @param int $userId Kullanıcı ID
     * @return void
     */
    public function getUserMemberships($userId) {
        // Yalnızca admin ve personel erişebilir
        if ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff') {
            echo json_encode(['error' => 'Yetkisiz erişim']);
            return;
        }
        
        $stmt = $this->db->prepare("SELECT m.id, m.start_date, m.end_date, p.name as package_name 
                                   FROM memberships m 
                                   JOIN packages p ON m.package_id = p.id 
                                   WHERE m.user_id = :user_id
                                   ORDER BY m.created_at DESC");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        $memberships = $stmt->fetchAll();
        
        // JSON formatında döndür
        header('Content-Type: application/json');
        echo json_encode($memberships);
    }
    
    /**
     * Yeni ödeme oluştur
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
        if (empty($data['user_id']) || empty($data['amount']) || empty($data['payment_type'])) {
            setError('Lütfen tüm alanları doldurun.');
            redirect('payments.php?action=create');
            return;
        }
        
        // Kullanıcı varlığını kontrol et
        $stmt = $this->db->prepare("SELECT id FROM users WHERE id = :id AND role = 'member'");
        $stmt->bindParam(':id', $data['user_id']);
        $stmt->execute();
        
        if (!$stmt->fetch()) {
            setError('Geçersiz kullanıcı seçildi.');
            redirect('payments.php?action=create');
            return;
        }
        
        // Üyelik ID kontrolü
        $membershipId = !empty($data['membership_id']) ? (int)$data['membership_id'] : null;
        
        if ($membershipId) {
            $stmt = $this->db->prepare("SELECT id FROM memberships WHERE id = :id AND user_id = :user_id");
            $stmt->bindParam(':id', $membershipId);
            $stmt->bindParam(':user_id', $data['user_id']);
            $stmt->execute();
            
            if (!$stmt->fetch()) {
                setError('Geçersiz üyelik seçildi.');
                redirect('payments.php?action=create');
                return;
            }
        }
        
        // Verileri temizle
        $userId = (int)$data['user_id'];
        $amount = (float)$data['amount'];
        $paymentType = sanitize($data['payment_type']);
        $description = !empty($data['description']) ? sanitize($data['description']) : null;
        $paymentDate = !empty($data['payment_date']) ? $data['payment_date'] : date('Y-m-d H:i:s');
        $createdBy = $_SESSION['user_id'];
        
        // Ödeme kaydı oluştur
        $stmt = $this->db->prepare("INSERT INTO payments 
                                   (user_id, membership_id, amount, payment_type, payment_date, description, created_by, created_at) 
                                   VALUES 
                                   (:user_id, :membership_id, :amount, :payment_type, :payment_date, :description, :created_by, NOW())");
        
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':membership_id', $membershipId);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':payment_type', $paymentType);
        $stmt->bindParam(':payment_date', $paymentDate);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':created_by', $createdBy);
        
        if ($stmt->execute()) {
            setSuccess('Ödeme başarıyla kaydedildi.');
            redirect('payments.php');
        } else {
            setError('Ödeme kaydedilirken bir hata oluştu.');
            redirect('payments.php?action=create');
        }
    }
    
    /**
     * Ödeme düzenleme formu göster
     * 
     * @param int $id Ödeme ID
     * @return void
     */
    public function edit($id) {
        // Yalnızca admin erişebilir
        if ($_SESSION['user_role'] !== 'admin') {
            setError('Bu sayfaya erişim izniniz yok.');
            redirect('index.php');
        }
        
        $stmt = $this->db->prepare("SELECT p.*, u.name as user_name, u.email as user_email 
                                   FROM payments p 
                                   JOIN users u ON p.user_id = u.id 
                                   WHERE p.id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $payment = $stmt->fetch();
        
        if (!$payment) {
            setError('Ödeme bulunamadı.');
            redirect('payments.php');
        }
        
        // Üyelik bilgilerini getir
        if ($payment['membership_id']) {
            $stmt = $this->db->prepare("SELECT m.*, p.name as package_name 
                                       FROM memberships m 
                                       JOIN packages p ON m.package_id = p.id 
                                       WHERE m.id = :id");
            $stmt->bindParam(':id', $payment['membership_id']);
            $stmt->execute();
            $membership = $stmt->fetch();
        } else {
            $membership = null;
        }
        
        // Görünümü yükle
        $pageTitle = "Ödeme Düzenle";
        include_once 'views/payments/edit.php';
    }
    
    /**
     * Ödeme bilgilerini güncelle
     * 
     * @param int $id Ödeme ID
     * @param array $data Form verileri
     * @return void
     */
    public function update($id, $data) {
        // Yalnızca admin erişebilir
        if ($_SESSION['user_role'] !== 'admin') {
            setError('Bu işlemi yapmaya yetkiniz yok.');
            redirect('index.php');
        }
        
        // Ödemeyi kontrol et
        $stmt = $this->db->prepare("SELECT * FROM payments WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $payment = $stmt->fetch();
        if (!$payment) {
            setError('Ödeme bulunamadı.');
            redirect('payments.php');
            return;
        }
        
        // Gerekli alanları kontrol et
        if (empty($data['amount']) || empty($data['payment_type'])) {
            setError('Lütfen tüm alanları doldurun.');
            redirect('payments.php?action=edit&id=' . $id);
            return;
        }
        
        // Verileri temizle
        $amount = (float)$data['amount'];
        $paymentType = sanitize($data['payment_type']);
        $description = !empty($data['description']) ? sanitize($data['description']) : null;
        $paymentDate = !empty($data['payment_date']) ? $data['payment_date'] : $payment['payment_date'];
        
        // Ödemeyi güncelle
        $stmt = $this->db->prepare("UPDATE payments 
                                   SET amount = :amount, 
                                       payment_type = :payment_type, 
                                       payment_date = :payment_date, 
                                       description = :description, 
                                       updated_at = NOW() 
                                   WHERE id = :id");
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':payment_type', $paymentType);
        $stmt->bindParam(':payment_date', $paymentDate);
        $stmt->bindParam(':description', $description);
        
        if ($stmt->execute()) {
            setSuccess('Ödeme başarıyla güncellendi.');
            redirect('payments.php?action=show&id=' . $id);
        } else {
            setError('Ödeme güncellenirken bir hata oluştu.');
            redirect('payments.php?action=edit&id=' . $id);
        }
    }
    
    /**
     * Ödemeyi sil
     * 
     * @param int $id Ödeme ID
     * @return void
     */
    public function delete($id) {
        // Yalnızca admin erişebilir
        if ($_SESSION['user_role'] !== 'admin') {
            setError('Bu işlemi yapmaya yetkiniz yok.');
            redirect('index.php');
        }
        
        // Ödemeyi kontrol et
        $stmt = $this->db->prepare("SELECT * FROM payments WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $payment = $stmt->fetch();
        if (!$payment) {
            setError('Ödeme bulunamadı.');
            redirect('payments.php');
            return;
        }
        
        // Ödemeyi sil
        $stmt = $this->db->prepare("DELETE FROM payments WHERE id = :id");
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            setSuccess('Ödeme başarıyla silindi.');
            redirect('payments.php');
        } else {
            setError('Ödeme silinirken bir hata oluştu.');
            redirect('payments.php?action=show&id=' . $id);
        }
    }
    
    /**
     * Rapor oluştur
     * 
     * @return void
     */
    public function reports() {
        // Yalnızca admin ve personel erişebilir
        if ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff') {
            setError('Bu sayfaya erişim izniniz yok.');
            redirect('index.php');
        }
        
        // Raporlama dönemini al
        $period = isset($_GET['period']) ? $_GET['period'] : 'month';
        $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
        $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';
        
        // Varsayılan tarih aralığını ayarla
        if (empty($startDate) || empty($endDate)) {
            if ($period == 'week') {
                $startDate = date('Y-m-d', strtotime('monday this week'));
                $endDate = date('Y-m-d', strtotime('sunday this week'));
            } elseif ($period == 'month') {
                $startDate = date('Y-m-01');
                $endDate = date('Y-m-t');
            } elseif ($period == 'year') {
                $startDate = date('Y-01-01');
                $endDate = date('Y-12-31');
            } else {
                $startDate = date('Y-m-d', strtotime('-30 days'));
                $endDate = date('Y-m-d');
            }
        }
        
        // Ödeme tiplerini getir
        $stmt = $this->db->prepare("SELECT DISTINCT payment_type FROM payments ORDER BY payment_type");
        $stmt->execute();
        $paymentTypes = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Toplam ödeme miktarını getir
        $stmt = $this->db->prepare("SELECT 
                                   payment_type,
                                   COUNT(*) as count,
                                   SUM(amount) as total
                                   FROM payments
                                   WHERE payment_date BETWEEN :start_date AND :end_date
                                   GROUP BY payment_type
                                   ORDER BY total DESC");
        
        $stmt->bindParam(':start_date', $startDate . ' 00:00:00');
        $stmt->bindParam(':end_date', $endDate . ' 23:59:59');
        $stmt->execute();
        
        $paymentSummary = $stmt->fetchAll();
        
        // Günlük ödeme özeti
        $stmt = $this->db->prepare("SELECT 
                                   DATE(payment_date) as date,
                                   COUNT(*) as count,
                                   SUM(amount) as total
                                   FROM payments
                                   WHERE payment_date BETWEEN :start_date AND :end_date
                                   GROUP BY DATE(payment_date)
                                   ORDER BY date");
        
        $stmt->bindParam(':start_date', $startDate . ' 00:00:00');
        $stmt->bindParam(':end_date', $endDate . ' 23:59:59');
        $stmt->execute();
        
        $dailyPayments = $stmt->fetchAll();
        
        // Görünümü yükle
        $pageTitle = "Ödeme Raporları";
        include_once 'views/payments/reports.php';
    }
    
    /**
     * Kullanıcının ödemelerini getir
     * 
     * @param int $userId Kullanıcı ID
     * @return array Ödemeler dizisi
     */
    public function getUserPayments($userId) {
        $stmt = $this->db->prepare("SELECT p.*, 
                                  m.id as membership_id, 
                                  pk.name as package_name 
                                  FROM payments p 
                                  LEFT JOIN memberships m ON p.membership_id = m.id 
                                  LEFT JOIN packages pk ON m.package_id = pk.id 
                                  WHERE p.user_id = :user_id 
                                  ORDER BY p.payment_date DESC");
        
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Belirli bir kullanıcının ödemelerini filtreler
     * 
     * @param int $userId Kullanıcı ID'si
     * @param string $dateFrom Başlangıç tarihi (YYYY-MM-DD)
     * @param string $dateTo Bitiş tarihi (YYYY-MM-DD)
     * @param string $paymentType Ödeme türü
     * @return array Filtrelenmiş ödemeler
     */
    public function filterUserPayments($userId, $dateFrom = '', $dateTo = '', $paymentType = '') {
        $userId = (int) $userId;
        $params = [$userId];
        
        $sql = "SELECT p.*, m.id as membership_id 
                FROM payments p 
                LEFT JOIN memberships m ON p.id = m.payment_id 
                WHERE p.user_id = ?";
        
        if (!empty($dateFrom)) {
            $sql .= " AND p.payment_date >= ?";
            $params[] = $dateFrom;
        }
        
        if (!empty($dateTo)) {
            $sql .= " AND p.payment_date <= ?";
            $params[] = $dateTo;
        }
        
        if (!empty($paymentType)) {
            $sql .= " AND p.payment_method = ?";
            $params[] = $paymentType;
        }
        
        $sql .= " ORDER BY p.payment_date DESC";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Ödeme filtreleme hatası: ' . $e->getMessage());
            return [];
        }
    }
}
?> 