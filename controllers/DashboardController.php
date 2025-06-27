<?php
/**
 * Kontrol paneli işlemlerini yöneten sınıf
 */
class DashboardController {
    private $db;
    
    /**
     * Sınıf başlatıcı
     */
    public function __construct() {
        $this->db = connectDB();
    }
    
    /**
     * Dashboard verilerini hazırlayıp döndürür
     * 
     * @return array Dashboard verileri
     */
    public function getDashboardData() {
        $userId = $_SESSION['user_id'];
        $userRole = $_SESSION['user_role'];
        $data = [];
        
        // Admin panel verileri
        if ($userRole === 'admin') {
            // Toplam üye sayısı
            $stmtUsers = $this->db->query("SELECT COUNT(*) as total FROM users WHERE role = 'member'");
            $data['totalMembers'] = $stmtUsers->fetch()['total'];
            
            // Aktif paket sayısı
            $stmtPackages = $this->db->query("SELECT COUNT(*) as total FROM memberships WHERE end_date >= CURDATE()");
            $data['activePackages'] = $stmtPackages->fetch()['total'];
            
            // Günlük gelir
            $stmtTodayIncome = $this->db->prepare("SELECT SUM(amount) as total FROM payments WHERE DATE(payment_date) = CURDATE()");
            $stmtTodayIncome->execute();
            $data['todayIncome'] = $stmtTodayIncome->fetch()['total'] ?: 0;
            
            // Aylık gelir
            $stmtMonthlyIncome = $this->db->prepare("SELECT SUM(amount) as total FROM payments WHERE MONTH(payment_date) = MONTH(CURDATE()) AND YEAR(payment_date) = YEAR(CURDATE())");
            $stmtMonthlyIncome->execute();
            $data['monthlyIncome'] = $stmtMonthlyIncome->fetch()['total'] ?: 0;
            
            // Bugün yapılan ziyaretler
            $stmtVisits = $this->db->prepare("SELECT COUNT(*) as total FROM visits WHERE DATE(check_in_time) = CURDATE()");
            $stmtVisits->execute();
            $data['todayVisits'] = $stmtVisits->fetch()['total'];
            
            // Son 10 üye
            $stmtRecentMembers = $this->db->prepare("
                SELECT u.id, u.first_name, u.last_name, u.email, u.created_at 
                FROM users u 
                WHERE u.role = 'member' 
                ORDER BY u.created_at DESC 
                LIMIT 10
            ");
            $stmtRecentMembers->execute();
            $data['recentMembers'] = $stmtRecentMembers->fetchAll();
            
            // Son 10 ödeme
            $stmtRecentPayments = $this->db->prepare("
                SELECT p.id, p.amount, p.payment_date, p.payment_type,
                       u.first_name, u.last_name
                FROM payments p
                JOIN users u ON p.user_id = u.id
                ORDER BY p.payment_date DESC
                LIMIT 10
            ");
            $stmtRecentPayments->execute();
            $data['recentPayments'] = $stmtRecentPayments->fetchAll();
        } 
        // Personel panel verileri
        else if ($userRole === 'staff') {
            // Bugün giriş yapan üyeler
            $stmtTodayVisits = $this->db->prepare("
                SELECT v.id, v.check_in_time, u.first_name, u.last_name
                FROM visits v
                JOIN users u ON v.user_id = u.id
                WHERE DATE(v.check_in_time) = CURDATE()
                ORDER BY v.check_in_time DESC
            ");
            $stmtTodayVisits->execute();
            $data['todayVisits'] = $stmtTodayVisits->fetchAll();
            
            // Aktif üyelikler
            $stmtActiveMembers = $this->db->prepare("
                SELECT u.id, u.first_name, u.last_name, m.end_date, p.name as package_name
                FROM users u 
                JOIN memberships m ON u.id = m.user_id
                JOIN packages p ON m.package_id = p.id
                WHERE m.end_date >= CURDATE()
                ORDER BY m.end_date ASC
            ");
            $stmtActiveMembers->execute();
            $data['activeMembers'] = $stmtActiveMembers->fetchAll();
            
            // Son 10 ödeme
            $stmtRecentPayments = $this->db->prepare("
                SELECT p.id, p.amount, p.payment_date, p.payment_type,
                       u.first_name, u.last_name
                FROM payments p
                JOIN users u ON p.user_id = u.id
                ORDER BY p.payment_date DESC
                LIMIT 10
            ");
            $stmtRecentPayments->execute();
            $data['recentPayments'] = $stmtRecentPayments->fetchAll();
        } 
        // Üye panel verileri
        else {
            // Üye bilgileri
            $stmtUser = $this->db->prepare("SELECT * FROM users WHERE id = :id");
            $stmtUser->bindParam(':id', $userId);
            $stmtUser->execute();
            $data['user'] = $stmtUser->fetch();
            
            // Üyelik bilgileri
            $stmtMembership = $this->db->prepare("
                SELECT m.*, p.name as package_name, p.description as package_description
                FROM memberships m
                JOIN packages p ON m.package_id = p.id
                WHERE m.user_id = :user_id
                ORDER BY m.end_date DESC
                LIMIT 1
            ");
            $stmtMembership->bindParam(':user_id', $userId);
            $stmtMembership->execute();
            $data['membership'] = $stmtMembership->fetch();
            
            // Son 5 ziyaret
            $stmtVisits = $this->db->prepare("
                SELECT * FROM visits 
                WHERE user_id = :user_id 
                ORDER BY check_in_time DESC 
                LIMIT 5
            ");
            $stmtVisits->bindParam(':user_id', $userId);
            $stmtVisits->execute();
            $data['visits'] = $stmtVisits->fetchAll();
            
            // Son 5 ödeme
            $stmtPayments = $this->db->prepare("
                SELECT * FROM payments 
                WHERE user_id = :user_id 
                ORDER BY payment_date DESC 
                LIMIT 5
            ");
            $stmtPayments->bindParam(':user_id', $userId);
            $stmtPayments->execute();
            $data['payments'] = $stmtPayments->fetchAll();
        }
        
        return $data;
    }
    
    /**
     * Ana sayfa görünümü (eski metot, artık kullanılmıyor)
     * 
     * @return void
     */
    public function index() {
        $userId = $_SESSION['user_id'];
        $userRole = $_SESSION['user_role'];
        
        // Admin paneli gösterimi
        if ($userRole === 'admin') {
            $this->showAdminDashboard();
        } 
        // Çalışan paneli gösterimi
        else if ($userRole === 'staff') {
            $this->showStaffDashboard();
        } 
        // Üye paneli gösterimi
        else {
            $this->showMemberDashboard($userId);
        }
    }
    
    /**
     * Admin paneli gösterimi (eski metot, artık kullanılmıyor)
     * 
     * @return void
     */
    private function showAdminDashboard() {
        // Toplam üye sayısı
        $stmtUsers = $this->db->query("SELECT COUNT(*) as total FROM users WHERE role = 'member'");
        $totalMembers = $stmtUsers->fetch()['total'];
        
        // Aktif paket sayısı
        $stmtPackages = $this->db->query("SELECT COUNT(*) as total FROM memberships WHERE end_date >= CURDATE()");
        $activePackages = $stmtPackages->fetch()['total'];
        
        // Günlük gelir
        $stmtTodayIncome = $this->db->prepare("SELECT SUM(amount) as total FROM payments WHERE DATE(payment_date) = CURDATE()");
        $stmtTodayIncome->execute();
        $todayIncome = $stmtTodayIncome->fetch()['total'] ?: 0;
        
        // Aylık gelir
        $stmtMonthlyIncome = $this->db->prepare("SELECT SUM(amount) as total FROM payments WHERE MONTH(payment_date) = MONTH(CURDATE()) AND YEAR(payment_date) = YEAR(CURDATE())");
        $stmtMonthlyIncome->execute();
        $monthlyIncome = $stmtMonthlyIncome->fetch()['total'] ?: 0;
        
        // Bugün yapılan ziyaretler
        $stmtVisits = $this->db->prepare("SELECT COUNT(*) as total FROM visits WHERE DATE(check_in_time) = CURDATE()");
        $stmtVisits->execute();
        $todayVisits = $stmtVisits->fetch()['total'];
        
        // Son 10 üye
        $stmtRecentMembers = $this->db->prepare("
            SELECT u.id, u.first_name, u.last_name, u.email, u.created_at 
            FROM users u 
            WHERE u.role = 'member' 
            ORDER BY u.created_at DESC 
            LIMIT 10
        ");
        $stmtRecentMembers->execute();
        $recentMembers = $stmtRecentMembers->fetchAll();
        
        // Son 10 ödeme
        $stmtRecentPayments = $this->db->prepare("
            SELECT p.id, p.amount, p.payment_date, p.payment_type,
                   u.first_name, u.last_name
            FROM payments p
            JOIN users u ON p.user_id = u.id
            ORDER BY p.payment_date DESC
            LIMIT 10
        ");
        $stmtRecentPayments->execute();
        $recentPayments = $stmtRecentPayments->fetchAll();
        
        // Veriyi görünüme gönder
        include_once 'views/dashboard/admin_dashboard.php';
    }
    
    /**
     * Personel paneli gösterimi (eski metot, artık kullanılmıyor)
     * 
     * @return void
     */
    private function showStaffDashboard() {
        // Bugün giriş yapan üyeler
        $stmtTodayVisits = $this->db->prepare("
            SELECT v.id, v.check_in_time, u.first_name, u.last_name
            FROM visits v
            JOIN users u ON v.user_id = u.id
            WHERE DATE(v.check_in_time) = CURDATE()
            ORDER BY v.check_in_time DESC
        ");
        $stmtTodayVisits->execute();
        $todayVisits = $stmtTodayVisits->fetchAll();
        
        // Aktif üyelikler
        $stmtActiveMembers = $this->db->prepare("
            SELECT u.id, u.first_name, u.last_name, m.end_date, p.name as package_name
            FROM users u 
            JOIN memberships m ON u.id = m.user_id
            JOIN packages p ON m.package_id = p.id
            WHERE m.end_date >= CURDATE()
            ORDER BY m.end_date ASC
        ");
        $stmtActiveMembers->execute();
        $activeMembers = $stmtActiveMembers->fetchAll();
        
        // Son 10 ödeme
        $stmtRecentPayments = $this->db->prepare("
            SELECT p.id, p.amount, p.payment_date, p.payment_type,
                   u.first_name, u.last_name
            FROM payments p
            JOIN users u ON p.user_id = u.id
            ORDER BY p.payment_date DESC
            LIMIT 10
        ");
        $stmtRecentPayments->execute();
        $recentPayments = $stmtRecentPayments->fetchAll();
        
        // Veriyi görünüme gönder
        include_once 'views/dashboard/staff_dashboard.php';
    }
    
    /**
     * Üye paneli gösterimi (eski metot, artık kullanılmıyor)
     * 
     * @param int $userId Kullanıcı ID
     * @return void
     */
    private function showMemberDashboard($userId) {
        // Üye bilgileri
        $stmtUser = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmtUser->bindParam(':id', $userId);
        $stmtUser->execute();
        $user = $stmtUser->fetch();
        
        // Üyelik bilgileri
        $stmtMembership = $this->db->prepare("
            SELECT m.*, p.name as package_name, p.description as package_description
            FROM memberships m
            JOIN packages p ON m.package_id = p.id
            WHERE m.user_id = :user_id
            ORDER BY m.end_date DESC
            LIMIT 1
        ");
        $stmtMembership->bindParam(':user_id', $userId);
        $stmtMembership->execute();
        $membership = $stmtMembership->fetch();
        
        // Son 5 ziyaret
        $stmtVisits = $this->db->prepare("
            SELECT * FROM visits 
            WHERE user_id = :user_id 
            ORDER BY check_in_time DESC 
            LIMIT 5
        ");
        $stmtVisits->bindParam(':user_id', $userId);
        $stmtVisits->execute();
        $visits = $stmtVisits->fetchAll();
        
        // Son 5 ödeme
        $stmtPayments = $this->db->prepare("
            SELECT * FROM payments 
            WHERE user_id = :user_id 
            ORDER BY payment_date DESC 
            LIMIT 5
        ");
        $stmtPayments->bindParam(':user_id', $userId);
        $stmtPayments->execute();
        $payments = $stmtPayments->fetchAll();
        
        // Veriyi görünüme gönder
        include_once 'views/dashboard/member_dashboard.php';
    }
}
?> 