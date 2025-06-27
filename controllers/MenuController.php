<?php
/**
 * Menü kontrolcüsü - Navigasyon menüleri için işlemleri yönetir
 */
class MenuController {
    private $db;
    
    /**
     * Sınıf başlatıcı
     */
    public function __construct() {
        $this->db = connectDB();
    }
    
    /**
     * Program sayfasını göster
     * 
     * @return void
     */
    public function showProgram() {
        $pageTitle = "Program";
        
        // Güncel haftanın programını getir
        $startOfWeek = date('Y-m-d', strtotime('monday this week'));
        $endOfWeek = date('Y-m-d', strtotime('sunday this week'));
        
        $query = "SELECT c.*, 
                 u.name as trainer_name,
                 r.name as room_name
                 FROM classes c
                 JOIN users u ON c.trainer_id = u.id
                 JOIN rooms r ON c.room_id = r.id
                 WHERE c.class_date BETWEEN :start_date AND :end_date
                 ORDER BY c.class_date, c.start_time";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':start_date', $startOfWeek);
        $stmt->bindParam(':end_date', $endOfWeek);
        $stmt->execute();
        
        $classes = $stmt->fetchAll();
        
        // Günlere göre dersleri düzenle
        $weekProgram = [];
        $days = ['Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi', 'Pazar'];
        
        foreach ($days as $index => $day) {
            $date = date('Y-m-d', strtotime($startOfWeek . ' +' . $index . ' days'));
            $weekProgram[$date] = [
                'date' => $date,
                'day_name' => $day,
                'classes' => []
            ];
        }
        
        foreach ($classes as $class) {
            $classDate = $class['class_date'];
            if (isset($weekProgram[$classDate])) {
                $weekProgram[$classDate]['classes'][] = $class;
            }
        }
        
        include_once 'views/program/index.php';
    }
    
    /**
     * Paketler sayfasını göster
     * 
     * @return void
     */
    public function showPackages() {
        // Aktif paketleri getir
        $stmt = $this->db->prepare("SELECT * FROM packages WHERE is_active = 1 ORDER BY display_order, price");
        $stmt->execute();
        $packages = $stmt->fetchAll();
        
        $pageTitle = "Paketler";
        include_once 'views/packages/index.php';
    }
    
    /**
     * Eğitmenler sayfasını göster
     * 
     * @return void
     */
    public function showTrainers() {
        // Eğitmenleri getir
        $stmt = $this->db->prepare("SELECT * FROM users WHERE role = 'trainer' ORDER BY name");
        $stmt->execute();
        $trainers = $stmt->fetchAll();
        
        $pageTitle = "Eğitmenler";
        include_once 'views/trainers/index.php';
    }
    
    /**
     * Eğitmen detayını göster
     * 
     * @param int $id Eğitmen ID
     * @return void
     */
    public function showTrainerDetails($id) {
        // Eğitmen bilgilerini getir
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id AND role = 'trainer'");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $trainer = $stmt->fetch();
        
        if (!$trainer) {
            setError('Eğitmen bulunamadı.');
            redirect('trainers.php');
        }
        
        // Eğitmenin derslerini getir
        $stmt = $this->db->prepare("SELECT c.*, r.name as room_name 
                                 FROM classes c 
                                 JOIN rooms r ON c.room_id = r.id
                                 WHERE c.trainer_id = :trainer_id 
                                 AND c.class_date >= CURDATE()
                                 ORDER BY c.class_date, c.start_time
                                 LIMIT 10");
        $stmt->bindParam(':trainer_id', $id);
        $stmt->execute();
        $classes = $stmt->fetchAll();
        
        $pageTitle = $trainer['name'];
        include_once 'views/trainers/details.php';
    }
    
    /**
     * İletişim sayfasını göster
     * 
     * @return void
     */
    public function showContact() {
        $pageTitle = "İletişim";
        include_once 'views/contact/index.php';
    }
    
    /**
     * İletişim formunu gönder
     * 
     * @param array $data Form verileri
     * @return void
     */
    public function sendContactForm($data) {
        // Gerekli alanların kontrolü
        if (empty($data['name']) || empty($data['email']) || empty($data['message'])) {
            setError('Lütfen tüm alanları doldurun.');
            redirect('contact.php');
            return;
        }
        
        // Email formatı kontrolü
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            setError('Geçersiz email formatı.');
            redirect('contact.php');
            return;
        }
        
        // Verileri temizle
        $name = sanitize($data['name']);
        $email = sanitize($data['email']);
        $phone = !empty($data['phone']) ? sanitize($data['phone']) : null;
        $subject = !empty($data['subject']) ? sanitize($data['subject']) : 'İletişim Formu';
        $message = sanitize($data['message']);
        $created = date('Y-m-d H:i:s');
        
        // İletişim mesajını veritabanına kaydet
        $stmt = $this->db->prepare("INSERT INTO contact_messages (name, email, phone, subject, message, status, created_at) 
                                 VALUES (:name, :email, :phone, :subject, :message, 'new', :created_at)");
        
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':subject', $subject);
        $stmt->bindParam(':message', $message);
        $stmt->bindParam(':created_at', $created);
        
        if ($stmt->execute()) {
            setSuccess('Mesajınız başarıyla gönderildi. En kısa sürede size dönüş yapacağız.');
            redirect('contact.php');
        } else {
            setError('Mesajınız gönderilirken bir hata oluştu.');
            redirect('contact.php');
        }
    }
    
    /**
     * Hakkımızda sayfasını göster
     * 
     * @return void
     */
    public function showAbout() {
        $pageTitle = "Hakkımızda";
        include_once 'views/about/index.php';
    }
}
?> 