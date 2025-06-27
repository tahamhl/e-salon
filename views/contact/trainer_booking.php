<?php
/**
 * Eğitmen randevu alma görünümü
 */

// Eğitmen bilgilerini getir
if (isset($trainerId)) {
    try {
        $db = connectDB(); // Veritabanı bağlantısı

        // Eğitmen bilgilerini sorgula
        $stmt = $db->prepare("SELECT id, name, specialty, bio, avatar FROM trainers WHERE id = :id");
        $stmt->bindParam(':id', $trainerId);
        $stmt->execute();
        $trainer = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$trainer) {
            $_SESSION['flash_message'] = 'Belirtilen eğitmen bulunamadı.';
            $_SESSION['flash_type'] = 'error';
            header('Location: trainers.php');
            exit;
        }

        // Eğitmenin uygun randevu saatlerini al (örnek veri, gerçek uygulamada veritabanından çekilebilir)
        $availableSlots = [
            'Pazartesi' => ['09:00', '11:00', '14:00', '16:00'],
            'Salı' => ['10:00', '13:00', '15:00', '17:00'],
            'Çarşamba' => ['09:30', '11:30', '14:30', '16:30'],
            'Perşembe' => ['10:30', '12:30', '15:30', '17:30'],
            'Cuma' => ['09:00', '12:00', '15:00', '18:00']
        ];
    } catch (PDOException $e) {
        $_SESSION['flash_message'] = 'Sistem hatası. Lütfen daha sonra tekrar deneyiniz.';
        $_SESSION['flash_type'] = 'error';
        error_log('Eğitmen bilgileri yüklenirken hata: ' . $e->getMessage());
        header('Location: trainers.php');
        exit;
    }
} else {
    redirect('trainers.php');
    exit;
}
?>

<div class="bg-gray-100 py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto">
            <!-- Başlık -->
            <div class="text-center mb-12">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">Eğitmen Randevusu</h1>
                <p class="text-gray-600">Eğitmenimiz <?php echo htmlspecialchars($trainer['name']); ?> ile özel bir seans ayırtın</p>
            </div>
            
            <!-- Eğitmen Bilgileri ve Randevu Formu -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden md:flex">
                <!-- Eğitmen Bilgileri -->
                <div class="md:w-1/3 bg-gray-800 text-white p-8">
                    <div class="text-center mb-6">
                        <img 
                            src="<?php echo !empty($trainer['avatar']) ? 'assets/images/trainers/' . $trainer['avatar'] : 'assets/images/default-trainer.jpg'; ?>" 
                            alt="<?php echo htmlspecialchars($trainer['name']); ?>" 
                            class="w-32 h-32 object-cover rounded-full mx-auto border-4 border-gray-700"
                        >
                        <h2 class="text-2xl font-bold mt-4"><?php echo htmlspecialchars($trainer['name']); ?></h2>
                        <p class="text-teal-400 font-medium"><?php echo htmlspecialchars($trainer['specialty']); ?></p>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-2 border-b border-gray-700 pb-2">Hakkında</h3>
                        <p class="text-gray-300"><?php echo nl2br(htmlspecialchars($trainer['bio'])); ?></p>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-semibold mb-2 border-b border-gray-700 pb-2">Müsait Saatler</h3>
                        <?php foreach ($availableSlots as $day => $times): ?>
                        <div class="mb-3">
                            <h4 class="font-medium text-teal-400"><?php echo $day; ?></h4>
                            <div class="flex flex-wrap gap-2 mt-1">
                                <?php foreach ($times as $time): ?>
                                <span class="inline-block bg-gray-700 px-2 py-1 text-xs rounded"><?php echo $time; ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Randevu Formu -->
                <div class="md:w-2/3 p-8">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6">Randevu Talep Formu</h3>
                    
                    <form action="booking_process.php" method="POST" class="space-y-6">
                        <input type="hidden" name="trainer_id" value="<?php echo $trainer['id']; ?>">
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <!-- İsim Soyisim -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">İsim Soyisim *</label>
                                <input 
                                    type="text" 
                                    id="name" 
                                    name="name" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500" 
                                    required
                                    value="<?php echo isset($_SESSION['user']) ? htmlspecialchars($_SESSION['user']['name']) : ''; ?>"
                                >
                            </div>
                            
                            <!-- E-posta -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-posta *</label>
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="email" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500" 
                                    required
                                    value="<?php echo isset($_SESSION['user']) ? htmlspecialchars($_SESSION['user']['email']) : ''; ?>"
                                >
                            </div>
                            
                            <!-- Telefon -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telefon *</label>
                                <input 
                                    type="tel" 
                                    id="phone" 
                                    name="phone" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500" 
                                    required
                                    value="<?php echo isset($_SESSION['user']) ? htmlspecialchars($_SESSION['user']['phone'] ?? '') : ''; ?>"
                                >
                            </div>
                            
                            <!-- Hizmet Türü -->
                            <div>
                                <label for="service_type" class="block text-sm font-medium text-gray-700 mb-1">Hizmet Türü *</label>
                                <select 
                                    id="service_type" 
                                    name="service_type" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500" 
                                    required
                                >
                                    <option value="">Seçiniz</option>
                                    <option value="kisisel_antrenman">Kişisel Antrenman</option>
                                    <option value="grup_dersi">Grup Dersi</option>
                                    <option value="beslenme_danismanligi">Beslenme Danışmanlığı</option>
                                    <option value="fitness_degerlendirmesi">Fitness Değerlendirmesi</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <!-- Tarih -->
                            <div>
                                <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Tarih *</label>
                                <input 
                                    type="date" 
                                    id="date" 
                                    name="date" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500" 
                                    required
                                    min="<?php echo date('Y-m-d'); ?>"
                                >
                            </div>
                            
                            <!-- Saat -->
                            <div>
                                <label for="time" class="block text-sm font-medium text-gray-700 mb-1">Saat *</label>
                                <select 
                                    id="time" 
                                    name="time" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500" 
                                    required
                                >
                                    <option value="">Seçiniz</option>
                                    <option value="09:00">09:00</option>
                                    <option value="10:00">10:00</option>
                                    <option value="11:00">11:00</option>
                                    <option value="12:00">12:00</option>
                                    <option value="14:00">14:00</option>
                                    <option value="15:00">15:00</option>
                                    <option value="16:00">16:00</option>
                                    <option value="17:00">17:00</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Notlar -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notlar (İsteğe Bağlı)</label>
                            <textarea 
                                id="notes" 
                                name="notes" 
                                rows="4" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500" 
                                placeholder="Özel istekleriniz veya sağlık durumunuz hakkında bilgi verebilirsiniz."
                            ></textarea>
                        </div>
                        
                        <!-- Gönderme Butonu -->
                        <div class="text-right">
                            <button 
                                type="submit" 
                                class="px-6 py-3 bg-teal-600 text-white font-medium rounded-md hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition-colors"
                            >
                                Randevu Talebi Gönder
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> 