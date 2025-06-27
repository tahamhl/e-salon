<?php
/**
 * BaseController - Tüm kontrolcüler için temel sınıf
 * Ortak fonksiyonlar ve veritabanı bağlantısı içerir
 */
class BaseController {
    protected $db;
    protected $currentUser;
    protected $viewPath = 'views/';
    
    /**
     * Constructor - Veritabanı bağlantısı oluşturur ve mevcut kullanıcı bilgisini alır
     */
    public function __construct() {
        // Veritabanı bağlantısı
        $this->db = Database::getInstance();
        
        // Mevcut kullanıcı bilgisi
        $this->currentUser = isset($_SESSION['user']) ? $_SESSION['user'] : null;
    }
    
    /**
     * Görünüm dosyasını yükler
     * 
     * @param string $view Görünüm dosyası adı
     * @param array $data Görünüme gönderilecek veriler
     * @return void
     */
    protected function view($view, $data = []) {
        // Verileri ayrıştır
        if (!empty($data)) {
            extract($data);
        }
        
        // Dosya yolunu oluştur
        $viewFile = $this->viewPath . $view . '.php';
        
        // Görünüm dosyası var mı kontrol et
        if (!file_exists($viewFile)) {
            die("View dosyası bulunamadı: {$viewFile}");
        }
        
        // Başlık bilgisini yükle
        require_once 'views/partials/header.php';
        
        // Görünüm dosyasını yükle
        require_once $viewFile;
        
        // Alt bilgiyi yükle
        require_once 'views/partials/footer.php';
    }
    
    /**
     * Sadece görünüm dosyasını yükler (header ve footer olmadan)
     * 
     * @param string $view Görünüm dosyası adı
     * @param array $data Görünüme gönderilecek veriler
     * @return void
     */
    protected function renderPartial($view, $data = []) {
        // Verileri ayrıştır
        if (!empty($data)) {
            extract($data);
        }
        
        // Dosya yolunu oluştur
        $viewFile = $this->viewPath . $view . '.php';
        
        // Görünüm dosyası var mı kontrol et
        if (!file_exists($viewFile)) {
            die("View dosyası bulunamadı: {$viewFile}");
        }
        
        // Görünüm dosyasını yükle
        require_once $viewFile;
    }
    
    /**
     * JSON veri döndürür
     * 
     * @param array $data JSON'a dönüştürülecek veriler
     * @param int $statusCode HTTP durum kodu
     * @return void
     */
    protected function json($data, $statusCode = 200) {
        // HTTP durum kodunu ayarla
        http_response_code($statusCode);
        
        // JSON başlığını ayarla
        header('Content-Type: application/json; charset=utf-8');
        
        // Veriyi JSON'a dönüştür ve çıktıla
        echo json_encode($data);
        exit;
    }
    
    /**
     * Kullanıcının giriş yapmış olup olmadığını kontrol eder
     * 
     * @return bool Giriş yapmışsa true, yapmamışsa false
     */
    protected function isLoggedIn() {
        return isset($_SESSION['user']) && !empty($_SESSION['user']);
    }
    
    /**
     * Kullanıcının belirtilen role sahip olup olmadığını kontrol eder
     * 
     * @param string|array $roles Kontrol edilecek rol veya roller
     * @return bool Yetkisi varsa true, yoksa false
     */
    protected function hasRole($roles) {
        // Kullanıcı giriş yapmamışsa false
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        // Tek bir rol geldiyse dizi yap
        if (!is_array($roles)) {
            $roles = [$roles];
        }
        
        // Kullanıcının rolü, kontrol edilen roller arasında mı?
        return in_array($_SESSION['user']['role'], $roles);
    }
    
    /**
     * Kullanıcının yetkisi yoksa yönlendirir
     * 
     * @param string|array $roles İzin verilen rol veya roller
     * @param string $redirectUrl Yönlendirilecek URL
     * @return void
     */
    protected function requireRole($roles, $redirectUrl = '/login') {
        if (!$this->hasRole($roles)) {
            // Hata mesajı oluştur
            setError('Bu sayfaya erişim yetkiniz bulunmamaktadır.');
            
            // Yönlendir
            redirect($redirectUrl);
        }
    }
    
    /**
     * Kullanıcının giriş yapmış olmasını gerektirir
     * 
     * @param string $redirectUrl Yönlendirilecek URL
     * @return void
     */
    protected function requireLogin($redirectUrl = '/login') {
        if (!$this->isLoggedIn()) {
            // Hata mesajı oluştur
            setError('Bu sayfaya erişmek için giriş yapmalısınız.');
            
            // Yönlendir
            redirect($redirectUrl);
        }
    }
    
    /**
     * POST verilerini alır ve temizler
     * 
     * @param array $requiredFields Zorunlu alanlar
     * @return array Temizlenmiş veriler veya hata
     */
    protected function getPostData($requiredFields = []) {
        $data = [];
        $errors = [];
        
        // POST verilerini döngüyle al ve temizle
        foreach ($_POST as $key => $value) {
            $data[$key] = sanitize($value);
            
            // Zorunlu alan kontrolü
            if (in_array($key, $requiredFields) && empty($data[$key])) {
                $errors[] = ucfirst($key) . ' alanı zorunludur.';
            }
        }
        
        // Hata varsa dön
        if (!empty($errors)) {
            setError(implode('<br>', $errors));
            return false;
        }
        
        return $data;
    }
    
    /**
     * Dosya yükler
     * 
     * @param string $fileKey Form alanı adı
     * @param string $uploadDir Yükleme dizini
     * @param array $allowedTypes İzin verilen dosya türleri
     * @param int $maxSize Maksimum dosya boyutu (byte)
     * @return string|false Yüklenen dosya adı veya hata durumunda false
     */
    protected function uploadFile($fileKey, $uploadDir = 'uploads/', $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf'], $maxSize = 5242880) {
        // Dosya var mı kontrol et
        if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] != UPLOAD_ERR_OK) {
            setError('Dosya yüklenirken bir hata oluştu.');
            return false;
        }
        
        $file = $_FILES[$fileKey];
        
        // Dizin var mı kontrol et, yoksa oluştur
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Dosya boyutu kontrolü
        if ($file['size'] > $maxSize) {
            setError('Dosya boyutu çok büyük. Maksimum ' . ($maxSize / 1024 / 1024) . 'MB olmalıdır.');
            return false;
        }
        
        // Dosya uzantısı kontrolü
        $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExt, $allowedTypes)) {
            setError('Dosya türü desteklenmiyor. İzin verilen türler: ' . implode(', ', $allowedTypes));
            return false;
        }
        
        // Benzersiz dosya adı oluştur
        $fileName = uniqid() . '.' . $fileExt;
        $targetPath = $uploadDir . $fileName;
        
        // Dosyayı yükle
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return $fileName;
        } else {
            setError('Dosya yüklenirken bir hata oluştu.');
            return false;
        }
    }
}
?> 