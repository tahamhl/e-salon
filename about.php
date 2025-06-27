<?php
/**
 * Hakkımızda Sayfası
 */
require_once 'config/init.php';

// Sayfa başlığı
$pageTitle = "Hakkımızda";

// Üst kısmı dahil et
include_once 'views/partials/header.php';
include_once 'views/partials/navbar.php';
?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Hakkımızda</h1>
    
    <!-- Ana İçerik -->
    <div class="mb-12">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="md:flex">
                <div class="md:flex-shrink-0">
                    <img class="h-full w-full object-cover md:w-64" src="assets/img/gym-interior.jpg" alt="Spor salonu">
                </div>
                <div class="p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">E-Salon'a Hoş Geldiniz</h2>
                    <p class="text-gray-600 mb-4">
                        2010 yılında kurulan E-Salon, sağlıklı yaşam ve fitness konusunda Türkiye'nin önde gelen spor salonları zinciridir. 
                        Modern ekipmanlarımız, uzman eğitmenlerimiz ve çeşitli grup derslerimizle üyelerimize en iyi hizmeti sunmayı hedefliyoruz.
                    </p>
                    <p class="text-gray-600">
                        Misyonumuz, insanların hayatlarını sağlıklı alışkanlıklarla zenginleştirmek ve herkes için ulaşılabilir bir spor deneyimi sunmaktır. 
                        Salonlarımızda, her yaş ve fitness seviyesindeki bireylere uygun programlar ve ekipmanlar bulunmaktadır.
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Değerlerimiz -->
    <div class="mb-12">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Değerlerimiz</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="w-12 h-12 flex items-center justify-center bg-blue-100 text-blue-500 rounded-full mb-4">
                    <i class="fas fa-heart text-xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Kalite</h3>
                <p class="text-gray-600">En son teknoloji ekipmanlar ve konforlu bir ortamda üstün hizmet kalitesi sunuyoruz.</p>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="w-12 h-12 flex items-center justify-center bg-green-100 text-green-500 rounded-full mb-4">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Topluluk</h3>
                <p class="text-gray-600">Destekleyici bir topluluk yaratarak üyelerimizin fitness yolculuğunda yalnız olmadıklarını hissetmelerini sağlıyoruz.</p>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="w-12 h-12 flex items-center justify-center bg-purple-100 text-purple-500 rounded-full mb-4">
                    <i class="fas fa-dumbbell text-xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Yenilik</h3>
                <p class="text-gray-600">Sürekli gelişen fitness trendlerini takip ediyor ve üyelerimize en güncel antrenman metodlarını sunuyoruz.</p>
            </div>
        </div>
    </div>
    
    <!-- Ekibimiz -->
    <div class="mb-12">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Uzman Ekibimiz</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <img class="w-full h-56 object-cover" src="assets/img/team-1.jpg" alt="Takım üyesi">
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-800">Ahmet Yılmaz</h3>
                    <p class="text-sm text-blue-500">Genel Müdür</p>
                    <p class="text-sm text-gray-600 mt-2">15+ yıl fitness sektörü deneyimi</p>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <img class="w-full h-56 object-cover" src="assets/img/team-2.jpg" alt="Takım üyesi">
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-800">Ayşe Demir</h3>
                    <p class="text-sm text-blue-500">Baş Eğitmen</p>
                    <p class="text-sm text-gray-600 mt-2">Uluslararası sertifikalı fitness koçu</p>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <img class="w-full h-56 object-cover" src="assets/img/team-3.jpg" alt="Takım üyesi">
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-800">Mehmet Kaya</h3>
                    <p class="text-sm text-blue-500">Beslenme Uzmanı</p>
                    <p class="text-sm text-gray-600 mt-2">Spor beslenmesi üzerine uzmanlaşmış diyetisyen</p>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <img class="w-full h-56 object-cover" src="assets/img/team-4.jpg" alt="Takım üyesi">
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-800">Zeynep Öztürk</h3>
                    <p class="text-sm text-blue-500">Grup Dersleri Koordinatörü</p>
                    <p class="text-sm text-gray-600 mt-2">Yoga ve pilates alanında sertifikalı eğitmen</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tesislerimiz -->
    <div class="mb-12">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Tesislerimiz</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-3">Modern Ekipmanlar</h3>
                <p class="text-gray-600 mb-4">En son teknoloji cardio ve ağırlık ekipmanlarıyla donatılmış salonlarımızda, her seviyede fitness hedefine ulaşmanız için gereken tüm araçlar bulunmaktadır.</p>
                <ul class="list-disc list-inside text-gray-600 space-y-1">
                    <li>Technogym ve Life Fitness marka son model ekipmanlar</li>
                    <li>Geniş serbest ağırlık alanları</li>
                    <li>Fonksiyonel antrenman bölgesi</li>
                    <li>Koşu bantları, bisikletler ve eliptik aletler</li>
                </ul>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-3">Grup Dersleri</h3>
                <p class="text-gray-600 mb-4">Motivasyonunuzu yüksek tutmak ve yeni arkadaşlar edinmek için birbirinden eğlenceli grup derslerimize katılabilirsiniz.</p>
                <ul class="list-disc list-inside text-gray-600 space-y-1">
                    <li>Zumba ve Aerobik</li>
                    <li>Yoga ve Pilates</li>
                    <li>Spinning</li>
                    <li>HIIT ve CrossFit</li>
                    <li>Kickbox ve Dövüş Sanatları</li>
                </ul>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-3">Özel Hizmetler</h3>
                <p class="text-gray-600 mb-4">Kişisel ihtiyaçlarınıza göre özelleştirilmiş hizmetlerimizle fitness deneyiminizi bir üst seviyeye taşıyın.</p>
                <ul class="list-disc list-inside text-gray-600 space-y-1">
                    <li>Kişisel antrenörlük seansları</li>
                    <li>Beslenme danışmanlığı</li>
                    <li>Vücut analizi ve fitness değerlendirmesi</li>
                    <li>Özel hazırlanmış antrenman programları</li>
                </ul>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-3">Konfor Alanları</h3>
                <p class="text-gray-600 mb-4">Antrenman öncesi ve sonrası rahatlığınız için tasarlanmış alanlarımızla salon deneyiminizi tamamlayın.</p>
                <ul class="list-disc list-inside text-gray-600 space-y-1">
                    <li>Modern duş ve soyunma odaları</li>
                    <li>Sauna ve buhar odası</li>
                    <li>Dinlenme alanları</li>
                    <li>Sağlıklı atıştırmalık ve içecek köşesi</li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- İletişim Bilgileri -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Bize Ulaşın</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Adres</h3>
                <p class="text-gray-600 mb-4">
                    Atatürk Bulvarı No: 123<br>
                    Beşiktaş, İstanbul
                </p>
                
                <h3 class="text-lg font-semibold text-gray-800 mb-2">İletişim</h3>
                <p class="text-gray-600">
                    <strong>Telefon:</strong> +90 (212) 555 7890<br>
                    <strong>E-posta:</strong> info@e-salon.com
                </p>
            </div>
            
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Çalışma Saatleri</h3>
                <p class="text-gray-600">
                    <strong>Pazartesi - Cuma:</strong> 06:00 - 23:00<br>
                    <strong>Cumartesi:</strong> 08:00 - 22:00<br>
                    <strong>Pazar:</strong> 09:00 - 20:00
                </p>
            </div>
            
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Sosyal Medya</h3>
                <div class="flex space-x-4">
                    <a href="#" class="text-blue-600 hover:text-blue-800 transition duration-300">
                        <i class="fab fa-facebook-f text-2xl"></i>
                    </a>
                    <a href="#" class="text-blue-400 hover:text-blue-600 transition duration-300">
                        <i class="fab fa-twitter text-2xl"></i>
                    </a>
                    <a href="#" class="text-pink-600 hover:text-pink-800 transition duration-300">
                        <i class="fab fa-instagram text-2xl"></i>
                    </a>
                    <a href="#" class="text-red-600 hover:text-red-800 transition duration-300">
                        <i class="fab fa-youtube text-2xl"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'views/partials/footer.php'; ?> 