<?php
// Üst bilgi dahil edilir
include_once 'views/partials/header.php';

// Veritabanı bağlantısı
$db = connectDB();

// Eğer trainer_id parametresi varsa, eğitmeni getir
$trainer = null;
$isBookingForm = false;

if (isset($_GET['trainer_id']) && is_numeric($_GET['trainer_id'])) {
    try {
        $query = "
            SELECT u.id, u.name, u.email, u.profile_image, 
                   t.specialty, t.bio, t.experience_years, t.hourly_rate,
                   t.available_hours, t.available_days
            FROM users u
            JOIN trainers t ON u.id = t.user_id
            WHERE u.id = :trainer_id AND u.role = 'trainer' AND u.status = 'active'
        ";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':trainer_id', $_GET['trainer_id'], PDO::PARAM_INT);
        $stmt->execute();
        $trainer = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Eğitmen bulunduysa rezervasyon formu gösterilecek
        $isBookingForm = ($trainer !== false);
        
    } catch (PDOException $e) {
        $_SESSION['flash_message'] = 'Eğitmen bilgisi yüklenirken bir hata oluştu.';
        $_SESSION['flash_type'] = 'error';
        error_log('Eğitmen getirme hatası: ' . $e->getMessage());
    }
}

// Sabit salon bilgileri
$salonInfo = [
    'name' => APP_NAME,
    'address' => 'Atlantis AVM, Kat: 3, No: 45, Ataşehir, İstanbul',
    'phone' => '+90 (212) 555 7890',
    'email' => 'info@e-salon.com',
    'workingHours' => [
        'Pazartesi - Cuma: 10:00 - 20:00',
        'Cumartesi: 10:00 - 18:00',
        'Pazar: Kapalı'
    ],
    'socialMedia' => [
        'instagram' => 'https://instagram.com/e-salon',
        'facebook' => 'https://facebook.com/e-salon',
        'twitter' => 'https://twitter.com/e-salon'
    ]
];
?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-center mb-8">
        <?= $isBookingForm ? 'Randevu Al' : 'Bize Ulaşın' ?>
    </h1>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- İletişim Bilgileri veya Eğitmen Bilgisi -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <?php if ($isBookingForm): ?>
                <!-- Eğitmen Bilgisi -->
                <div class="flex flex-col items-center mb-6">
                    <?php if (!empty($trainer['profile_image'])): ?>
                        <img src="<?= htmlspecialchars($trainer['profile_image']) ?>" 
                             alt="<?= htmlspecialchars($trainer['name']) ?>" 
                             class="w-32 h-32 rounded-full object-cover mb-4">
                    <?php else: ?>
                        <div class="w-32 h-32 rounded-full bg-gray-300 flex items-center justify-center mb-4">
                            <span class="text-5xl text-gray-500">
                                <i class="fas fa-user-circle"></i>
                            </span>
                        </div>
                    <?php endif; ?>
                    
                    <h2 class="text-2xl font-bold"><?= htmlspecialchars($trainer['name']) ?></h2>
                    <p class="text-blue-600 font-medium"><?= htmlspecialchars($trainer['specialty']) ?></p>
                    <p class="text-gray-600 mt-1"><?= intval($trainer['experience_years']) ?> yıl deneyim</p>
                </div>
                
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-2">Hakkında</h3>
                    <p class="text-gray-700"><?= nl2br(htmlspecialchars($trainer['bio'] ?? 'Bilgi bulunmuyor.')) ?></p>
                </div>
                
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-2">Çalışma Saatleri</h3>
                    <?php 
                    $availableDays = !empty($trainer['available_days']) ? 
                        explode(',', $trainer['available_days']) : 
                        ['Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma'];
                    
                    $availableHours = !empty($trainer['available_hours']) ? 
                        explode(',', $trainer['available_hours']) : 
                        ['10:00-18:00'];
                    ?>
                    
                    <ul class="space-y-1">
                        <?php foreach ($availableDays as $day): ?>
                            <li class="text-gray-700">
                                <span class="font-medium"><?= htmlspecialchars(trim($day)) ?>:</span>
                                <?= htmlspecialchars(implode(', ', array_map('trim', $availableHours))) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-2">Seans Ücreti</h3>
                    <p class="text-2xl font-bold text-green-600">
                        <?= number_format($trainer['hourly_rate'], 2) ?> ₺/saat
                    </p>
                </div>
            <?php else: ?>
                <!-- Salon İletişim Bilgileri -->
                <h2 class="text-2xl font-bold mb-6"><?= htmlspecialchars($salonInfo['name']) ?></h2>
                
                <div class="space-y-4">
                    <div class="flex items-start">
                        <span class="text-blue-600 mr-3 mt-1"><i class="fas fa-map-marker-alt"></i></span>
                        <p class="text-gray-700"><?= htmlspecialchars($salonInfo['address']) ?></p>
                    </div>
                    
                    <div class="flex items-start">
                        <span class="text-blue-600 mr-3 mt-1"><i class="fas fa-phone"></i></span>
                        <p class="text-gray-700"><?= htmlspecialchars($salonInfo['phone']) ?></p>
                    </div>
                    
                    <div class="flex items-start">
                        <span class="text-blue-600 mr-3 mt-1"><i class="fas fa-envelope"></i></span>
                        <p class="text-gray-700"><?= htmlspecialchars($salonInfo['email']) ?></p>
                    </div>
                    
                    <div class="flex items-start">
                        <span class="text-blue-600 mr-3 mt-1"><i class="fas fa-clock"></i></span>
                        <div>
                            <p class="font-semibold mb-1">Çalışma Saatleri</p>
                            <ul class="space-y-1">
                                <?php foreach ($salonInfo['workingHours'] as $hours): ?>
                                    <li class="text-gray-700"><?= htmlspecialchars($hours) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="flex items-center mt-6 space-x-4">
                        <?php foreach ($salonInfo['socialMedia'] as $platform => $url): ?>
                            <a href="<?= htmlspecialchars($url) ?>" target="_blank" rel="noopener noreferrer"
                               class="text-blue-600 hover:text-blue-800 text-2xl">
                                <i class="fab fa-<?= $platform ?>"></i>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Google Harita -->
                <div class="mt-8">
                    <h3 class="text-lg font-semibold mb-3">Bizi Haritada Bulun</h3>
                    <div class="w-full h-64 bg-gray-200 rounded-lg">
                        <!-- Google Harita kodu buraya eklenecek -->
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3011.6505890203293!2d29.1076863!3d40.9923046!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14cacf8f8ed384c1%3A0xf243f1aaedf98e76!2zQXRhxZ9laGlyLCDEsHN0YW5idWw!5e0!3m2!1str!2str!4v1658922642159!5m2!1str!2str"
                            width="100%" 
                            height="100%" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- İletişim Formu veya Randevu Formu -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold mb-6">
                <?= $isBookingForm ? 'Randevu Talep Formu' : 'Mesaj Gönderin' ?>
            </h2>
            
            <?php if (!empty($_SESSION['flash_message'])): ?>
                <div class="mb-4 p-4 rounded-md <?= ($_SESSION['flash_type'] === 'error') ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' ?>">
                    <?= htmlspecialchars($_SESSION['flash_message']) ?>
                </div>
                <?php 
                unset($_SESSION['flash_message']); 
                unset($_SESSION['flash_type']);
                ?>
            <?php endif; ?>
            
            <form action="<?= $isBookingForm ? 'booking_process.php' : 'contact_process.php' ?>" method="post" class="space-y-4">
                <?php if ($isBookingForm): ?>
                    <!-- Eğitmen ID'si gizli alan olarak gönderilir -->
                    <input type="hidden" name="trainer_id" value="<?= (int)$trainer['id'] ?>">
                <?php endif; ?>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Ad Soyad -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Ad Soyad</label>
                        <input type="text" id="name" name="name" required
                               class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <!-- E-posta -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-posta</label>
                        <input type="email" id="email" name="email" required
                               class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
                
                <!-- Telefon -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
                    <input type="tel" id="phone" name="phone" required
                           class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                
                <?php if ($isBookingForm): ?>
                    <!-- Hizmet Türü -->
                    <div>
                        <label for="service_type" class="block text-sm font-medium text-gray-700 mb-1">Hizmet Türü</label>
                        <select id="service_type" name="service_type" required
                                class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Seçiniz</option>
                            <option value="kişisel_antrenman">Kişisel Antrenman</option>
                            <option value="grup_antrenmanı">Grup Antrenmanı</option>
                            <option value="fitness_danışmanlığı">Fitness Danışmanlığı</option>
                            <option value="beslenme_danışmanlığı">Beslenme Danışmanlığı</option>
                            <option value="rehabilitasyon">Rehabilitasyon</option>
                        </select>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Tarih -->
                        <div>
                            <label for="booking_date" class="block text-sm font-medium text-gray-700 mb-1">Tarih</label>
                            <input type="date" id="booking_date" name="booking_date" required
                                   min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                                   max="<?= date('Y-m-d', strtotime('+30 days')) ?>"
                                   class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <!-- Saat -->
                        <div>
                            <label for="booking_time" class="block text-sm font-medium text-gray-700 mb-1">Saat</label>
                            <select id="booking_time" name="booking_time" required
                                    class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Seçiniz</option>
                                <?php
                                // Saatlik randevu süresi
                                $startHour = 10; // 10:00
                                $endHour = 19;   // 19:00 (son randevu 18:00'de)
                                
                                for ($hour = $startHour; $hour < $endHour; $hour++) {
                                    $timeStr = sprintf("%02d:00", $hour);
                                    echo '<option value="' . $timeStr . '">' . $timeStr . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Ek Notlar -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Ek Notlar (İsteğe Bağlı)</label>
                        <textarea id="notes" name="notes" rows="3"
                                  class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>
                <?php else: ?>
                    <!-- Konu -->
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Konu</label>
                        <input type="text" id="subject" name="subject" required
                               class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <!-- Mesaj -->
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Mesajınız</label>
                        <textarea id="message" name="message" rows="5" required
                                  class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>
                <?php endif; ?>
                
                <!-- Gönder Butonu -->
                <div class="pt-2">
                    <button type="submit" class="w-full px-6 py-3 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                        <?= $isBookingForm ? 'Randevu Talebi Gönder' : 'Mesaj Gönder' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if ($isBookingForm): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const bookingDateInput = document.getElementById('booking_date');
    const bookingTimeSelect = document.getElementById('booking_time');
    
    // Tarih değiştiğinde kontrol et
    bookingDateInput.addEventListener('change', function() {
        const selectedDate = new Date(this.value);
        const dayOfWeek = selectedDate.getDay(); // 0: Pazar, 1: Pazartesi, ...
        
        // Eğer seçilen gün Pazar ise (0)
        if (dayOfWeek === 0) {
            alert('Pazar günü randevu alınamaz. Lütfen başka bir gün seçin.');
            this.value = ''; // Tarihi temizle
            bookingTimeSelect.disabled = true;
        } else {
            bookingTimeSelect.disabled = false;
        }
    });
});
</script>
<?php endif; ?>

<?php
// Alt bilgi dahil edilir
include_once 'views/partials/footer.php';
?> 