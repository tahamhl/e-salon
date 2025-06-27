<?php
/**
 * Profil Sayfası
 */
require_once 'config/init.php';

// Oturum kontrolü
if (!isset($_SESSION['user_id'])) {
    setError('Bu sayfayı görüntülemek için giriş yapmalısınız.');
    redirect('login.php');
    exit;
}

// Kullanıcı bilgilerini getir
$userId = $_SESSION['user_id'];
$userRole = $_SESSION['user_role'];

require_once 'controllers/UserController.php';
$userController = new UserController();
$user = $userController->getUserById($userId);

// Formdan gelen verileri işle (profil güncelleme)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $data = [
        'name' => $_POST['name'] ?? '',
        'surname' => $_POST['surname'] ?? '',
        'email' => $_POST['email'] ?? '',
        'phone' => $_POST['phone'] ?? '',
        'address' => $_POST['address'] ?? '',
        'birth_date' => $_POST['birth_date'] ?? null,
        'gender' => $_POST['gender'] ?? '',
        'emergency_contact' => $_POST['emergency_contact'] ?? '',
        'emergency_phone' => $_POST['emergency_phone'] ?? ''
    ];
    
    $userController->updateProfile($userId, $data);
    redirect('profile.php');
    exit;
}

// Şifre değiştirme formu işleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        setError('Lütfen tüm alanları doldurun.');
    } elseif ($newPassword !== $confirmPassword) {
        setError('Yeni şifreler eşleşmiyor.');
    } elseif (strlen($newPassword) < 6) {
        setError('Yeni şifre en az 6 karakter olmalıdır.');
    } else {
        $result = $userController->changePassword($userId, $currentPassword, $newPassword);
        if ($result) {
            setSuccess('Şifreniz başarıyla değiştirildi.');
        }
    }
    
    redirect('profile.php');
    exit;
}

// Sayfa başlığı
$pageTitle = "Profilim";

// Üst kısmı dahil et
include_once 'views/partials/header.php';

?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Profilim</h1>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sol Kısım - Kullanıcı Bilgileri ve Hızlı Bağlantılar -->
        <div class="lg:col-span-1">
            <!-- Profil Kartı -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                <div class="bg-blue-500 h-24 flex items-center justify-center">
                    <div class="avatar w-20 h-20 bg-white rounded-full flex items-center justify-center text-blue-500 text-4xl border-4 border-white transform translate-y-10">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                <div class="pt-12 pb-6 px-6">
                    <h2 class="text-xl font-bold text-center text-gray-800">
                        <?php echo htmlspecialchars($user['name'] . ' ' . ($user['surname'] ?? '')); ?>
                    </h2>
                    <p class="text-center text-gray-500 mt-1"><?php echo htmlspecialchars($user['email']); ?></p>
                    <p class="text-center mt-2">
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                            <?php echo $userRole === 'admin' ? 'bg-purple-100 text-purple-800' : 
                                ($userRole === 'staff' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'); ?>">
                            <?php echo $userRole === 'admin' ? 'Yönetici' : 
                                ($userRole === 'staff' ? 'Personel' : 'Üye'); ?>
                        </span>
                    </p>
                </div>
            </div>
            
            <!-- Hızlı Bağlantılar -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                <div class="p-4 bg-gray-50 border-b">
                    <h3 class="font-semibold text-gray-700">Hızlı Bağlantılar</h3>
                </div>
                <div class="p-4">
                    <ul class="space-y-2">
                        <li>
                            <a href="my_memberships.php" class="flex items-center text-blue-500 hover:text-blue-700 transition duration-300">
                                <i class="fas fa-id-card w-5"></i>
                                <span>Üyeliklerim</span>
                            </a>
                        </li>
                        <li>
                            <a href="dashboard.php" class="flex items-center text-blue-500 hover:text-blue-700 transition duration-300">
                                <i class="fas fa-tachometer-alt w-5"></i>
                                <span>Gösterge Paneli</span>
                            </a>
                        </li>
                        <li>
                            <a href="packages.php" class="flex items-center text-blue-500 hover:text-blue-700 transition duration-300">
                                <i class="fas fa-box w-5"></i>
                                <span>Paketleri İncele</span>
                            </a>
                        </li>
                        <li>
                            <a href="nutrition.php" class="flex items-center text-blue-500 hover:text-blue-700 transition duration-300">
                                <i class="fas fa-apple-alt w-5"></i>
                                <span>Beslenme Tavsiyeleri</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Şifre Değiştirme -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-4 bg-gray-50 border-b">
                    <h3 class="font-semibold text-gray-700">Şifre Değiştir</h3>
                </div>
                <div class="p-4">
                    <form action="profile.php" method="POST">
                        <input type="hidden" name="action" value="change_password">
                        
                        <div class="mb-4">
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Mevcut Şifre</label>
                            <input type="password" id="current_password" name="current_password" required 
                                   class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div class="mb-4">
                            <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">Yeni Şifre</label>
                            <input type="password" id="new_password" name="new_password" required minlength="6"
                                   class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-gray-500 mt-1">En az 6 karakter</p>
                        </div>
                        
                        <div class="mb-4">
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Yeni Şifre (Tekrar)</label>
                            <input type="password" id="confirm_password" name="confirm_password" required minlength="6"
                                   class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-md transition duration-300">
                            Şifreyi Değiştir
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Sağ Kısım - Profil Bilgileri ve Düzenleme -->
        <div class="lg:col-span-2">
            <!-- Profil Düzenleme -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                <div class="p-4 bg-gray-50 border-b">
                    <h3 class="font-semibold text-gray-700">Profil Bilgileri</h3>
                </div>
                <div class="p-6">
                    <form action="profile.php" method="POST">
                        <input type="hidden" name="action" value="update_profile">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Ad -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Ad</label>
                                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required 
                                       class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <!-- Soyad -->
                            <div>
                                <label for="surname" class="block text-sm font-medium text-gray-700 mb-1">Soyad</label>
                                <input type="text" id="surname" name="surname" value="<?php echo htmlspecialchars($user['surname'] ?? ''); ?>" required 
                                       class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <!-- E-posta -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-posta</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required 
                                       class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <!-- Telefon -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
                                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" 
                                       class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <!-- Doğum Tarihi -->
                            <div>
                                <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-1">Doğum Tarihi</label>
                                <input type="date" id="birth_date" name="birth_date" 
                                       value="<?php echo isset($user['birth_date']) ? date('Y-m-d', strtotime($user['birth_date'])) : ''; ?>" 
                                       class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <!-- Cinsiyet -->
                            <div>
                                <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Cinsiyet</label>
                                <select id="gender" name="gender" 
                                        class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value=""></option>
                                    <option value="male" <?php echo isset($user['gender']) && $user['gender'] === 'male' ? 'selected' : ''; ?>>Erkek</option>
                                    <option value="female" <?php echo isset($user['gender']) && $user['gender'] === 'female' ? 'selected' : ''; ?>>Kadın</option>
                                    <option value="other" <?php echo isset($user['gender']) && $user['gender'] === 'other' ? 'selected' : ''; ?>>Diğer</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Adres -->
                        <div class="mb-6">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Adres</label>
                            <textarea id="address" name="address" rows="3" 
                                      class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Acil Durum Kişisi -->
                            <div>
                                <label for="emergency_contact" class="block text-sm font-medium text-gray-700 mb-1">Acil Durum Kişisi</label>
                                <input type="text" id="emergency_contact" name="emergency_contact" 
                                       value="<?php echo htmlspecialchars($user['emergency_contact'] ?? ''); ?>" 
                                       class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <!-- Acil Durum Telefonu -->
                            <div>
                                <label for="emergency_phone" class="block text-sm font-medium text-gray-700 mb-1">Acil Durum Telefonu</label>
                                <input type="tel" id="emergency_phone" name="emergency_phone" 
                                       value="<?php echo htmlspecialchars($user['emergency_phone'] ?? ''); ?>" 
                                       class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-6 rounded-md shadow transition duration-300">
                                Profili Güncelle
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Hesap Bilgileri -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                <div class="p-4 bg-gray-50 border-b">
                    <h3 class="font-semibold text-gray-700">Hesap Bilgileri</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Üyelik Durumu</p>
                            <p class="font-medium">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Aktif
                                </span>
                            </p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Kayıt Tarihi</p>
                            <p class="font-medium"><?php echo date('d.m.Y', strtotime($user['created_at'])); ?></p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Son Giriş</p>
                            <p class="font-medium"><?php echo isset($user['last_login']) ? date('d.m.Y H:i', strtotime($user['last_login'])) : 'Bilgi Yok'; ?></p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Profil Güncellemesi</p>
                            <p class="font-medium"><?php echo isset($user['updated_at']) ? date('d.m.Y H:i', strtotime($user['updated_at'])) : 'Bilgi Yok'; ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Bilgilendirmeler ve Gizlilik -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-4 bg-gray-50 border-b">
                    <h3 class="font-semibold text-gray-700">Bilgilendirmeler ve Gizlilik</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="email_notifications" name="email_notifications" type="checkbox" checked 
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="email_notifications" class="font-medium text-gray-700">E-posta Bildirimleri</label>
                                <p class="text-gray-500">Kampanyalar, özel teklifler ve önemli duyurular hakkında e-posta ile bilgilendirme al.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="sms_notifications" name="sms_notifications" type="checkbox" checked 
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="sms_notifications" class="font-medium text-gray-700">SMS Bildirimleri</label>
                                <p class="text-gray-500">Randevu hatırlatmaları ve önemli duyurular hakkında SMS ile bilgilendirme al.</p>
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <p class="text-sm text-gray-500">
                                Üyeliğinizi iptal etmek veya hesabınızı silmek için lütfen <a href="#" class="text-blue-500 hover:text-blue-700">müşteri hizmetleri</a> ile iletişime geçin.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'views/partials/footer.php'; ?> 