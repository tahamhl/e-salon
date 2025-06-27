<?php
/**
 * Beslenme Sayfası
 */
require_once 'config/init.php';

// Sayfa başlığı
$pageTitle = "Beslenme Tavsiyeleri";

// Üst kısmı dahil et
include_once 'views/partials/header.php';
include_once 'views/partials/navbar.php';
?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Beslenme Tavsiyeleri</h1>
    
    <!-- Giriş Bölümü -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="flex flex-col md:flex-row">
            <div class="md:w-3/4 pr-0 md:pr-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Spor ve Beslenme</h2>
                <p class="text-gray-600 mb-4">
                    Doğru beslenme, fitness hedeflerinize ulaşmanız için en önemli faktörlerden biridir. 
                    Dengeli bir beslenme planı, antrenman performansınızı artırır, kas gelişiminizi destekler ve vücut yağ oranınızı kontrol etmenize yardımcı olur.
                </p>
                <p class="text-gray-600">
                    Bu sayfada, farklı hedefler için beslenme stratejileri, makro besin dengesi, sporcu beslenmesi ve daha fazlası hakkında bilgilere ulaşabilirsiniz. 
                    Unutmayın ki, bireysel ihtiyaçlar farklılık gösterebilir ve kişiye özel beslenme programı için bir beslenme uzmanına danışmanızı öneririz.
                </p>
            </div>
            <div class="md:w-1/4 mt-4 md:mt-0">
                <img src="assets/img/nutrition.jpg" alt="Sağlıklı beslenme" class="rounded-lg w-full h-auto shadow">
            </div>
        </div>
    </div>
    
    <!-- Temel Beslenme İlkeleri -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Temel Beslenme İlkeleri</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="w-14 h-14 flex items-center justify-center rounded-full bg-blue-100 text-blue-500 mb-4">
                    <i class="fas fa-balance-scale text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Makro Besinler</h3>
                <p class="text-gray-600 mb-3">Makro besinler, vücudunuzun enerji üretmek için kullandığı ana besin gruplarıdır:</p>
                <ul class="list-disc list-inside text-gray-600 space-y-1">
                    <li><strong>Proteinler:</strong> Kas onarımı ve büyümesi için temel yapı taşları</li>
                    <li><strong>Karbonhidratlar:</strong> Birincil enerji kaynağı</li>
                    <li><strong>Yağlar:</strong> Hormon üretimi ve vitamin emilimi için gerekli</li>
                </ul>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="w-14 h-14 flex items-center justify-center rounded-full bg-green-100 text-green-500 mb-4">
                    <i class="fas fa-apple-alt text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Mikro Besinler</h3>
                <p class="text-gray-600 mb-3">Mikro besinler, az miktarda ihtiyaç duyulan ancak sağlık için hayati öneme sahip vitaminler ve minerallerdir:</p>
                <ul class="list-disc list-inside text-gray-600 space-y-1">
                    <li><strong>Vitaminler:</strong> Metabolik süreçler için gerekli</li>
                    <li><strong>Mineraller:</strong> Kemik sağlığı ve kas fonksiyonu için önemli</li>
                    <li><strong>Antioksidanlar:</strong> Hücre hasarına karşı koruma sağlar</li>
                </ul>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="w-14 h-14 flex items-center justify-center rounded-full bg-purple-100 text-purple-500 mb-4">
                    <i class="fas fa-tint text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Hidrasyon</h3>
                <p class="text-gray-600 mb-3">Su tüketimi, optimal performans ve genel sağlık için kritik öneme sahiptir:</p>
                <ul class="list-disc list-inside text-gray-600 space-y-1">
                    <li>Günde en az 2-3 litre su içmeyi hedefleyin</li>
                    <li>Antrenman öncesi, sırası ve sonrasında hidrate kalın</li>
                    <li>Spor içecekleri, uzun ve yoğun antrenmanlar için uygun olabilir</li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Hedeflere Göre Beslenme -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Hedeflere Göre Beslenme</h2>
        
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="md:flex">
                <div class="md:w-1/3 bg-blue-50 p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Kas Kazanımı</h3>
                    <p class="text-gray-600">
                        Kas kütlesi artırmak için, kalori alımınızın harcadığınız kaloriden fazla olması gerekir (kalori fazlası).
                    </p>
                </div>
                <div class="md:w-2/3 p-6">
                    <ul class="list-disc list-inside text-gray-600 space-y-2">
                        <li>Günlük protein alımınızı vücut ağırlığınızın kilogramı başına 1.6-2.2 gram olacak şekilde ayarlayın</li>
                        <li>Karbonhidrat alımını artırın (vücut ağırlığının kilogramı başına 4-7 gram)</li>
                        <li>Sağlıklı yağlar tüketmeyi ihmal etmeyin (toplam kalorinin %20-30'u)</li>
                        <li>Antrenman sonrası protein ve karbonhidrat içeren bir öğün tüketin</li>
                        <li>Günlük kalori alımınızı bakım kalori ihtiyacınızın 300-500 kalori üzerine çıkarın</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="md:flex">
                <div class="md:w-1/3 bg-green-50 p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Yağ Yakımı</h3>
                    <p class="text-gray-600">
                        Vücut yağını azaltmak için kalori alımınızı harcadığınız kaloriden az olacak şekilde ayarlamalısınız (kalori açığı).
                    </p>
                </div>
                <div class="md:w-2/3 p-6">
                    <ul class="list-disc list-inside text-gray-600 space-y-2">
                        <li>Protein alımını yüksek tutun (vücut ağırlığının kilogramı başına 1.8-2.2 gram)</li>
                        <li>Karbonhidrat alımını azaltın, ancak tamamen kesmeyin</li>
                        <li>Düşük glisemik indeksli karbonhidratları tercih edin</li>
                        <li>Sağlıklı yağ kaynaklarına odaklanın (avokado, zeytinyağı, balık)</li>
                        <li>Lifli gıdaları artırın, tokluk hissi ve sindirim sağlığı için önemlidir</li>
                        <li>Günlük kalori alımınızı bakım kalori ihtiyacınızın 300-500 kalori altına indirin</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="md:flex">
                <div class="md:w-1/3 bg-yellow-50 p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Performans İyileştirme</h3>
                    <p class="text-gray-600">
                        Atletik performansı artırmak için beslenme stratejinizi antrenman tipinize ve zamanlamasına göre ayarlayın.
                    </p>
                </div>
                <div class="md:w-2/3 p-6">
                    <ul class="list-disc list-inside text-gray-600 space-y-2">
                        <li>Antrenman öncesi kompleks karbonhidratlar tüketin (antrenman öncesi 2-3 saat)</li>
                        <li>Antrenman sonrası karbonhidrat ve protein içeren bir öğün tüketin (30-60 dakika içinde)</li>
                        <li>Dayanıklılık antrenmanları için karbonhidrat alımını artırın</li>
                        <li>Yarışma/müsabaka öncesi beslenmenizi en az 1 hafta önceden planlayın</li>
                        <li>Yüksek yoğunluklu antrenmanlar için kreatin gibi destekleri değerlendirin</li>
                        <li>Antrenman zamanlamanıza göre öğün planlaması yapın</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Örnek Öğün Planları -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Örnek Günlük Öğün Planları</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-4 bg-blue-50 border-b">
                    <h3 class="font-semibold text-gray-800">Kas Kazanımı Örnek Plan</h3>
                </div>
                <div class="p-4">
                    <div class="mb-4">
                        <h4 class="font-medium text-gray-700">Kahvaltı</h4>
                        <ul class="list-disc list-inside text-gray-600 text-sm">
                            <li>4 yumurta (2 tam, 2 beyaz)</li>
                            <li>Yulaf ezmesi (1 kase)</li>
                            <li>Muz ve bal</li>
                            <li>Badem sütü</li>
                        </ul>
                    </div>
                    <div class="mb-4">
                        <h4 class="font-medium text-gray-700">Ara Öğün</h4>
                        <ul class="list-disc list-inside text-gray-600 text-sm">
                            <li>Protein shake</li>
                            <li>Badem (bir avuç)</li>
                        </ul>
                    </div>
                    <div class="mb-4">
                        <h4 class="font-medium text-gray-700">Öğle Yemeği</h4>
                        <ul class="list-disc list-inside text-gray-600 text-sm">
                            <li>Tavuk göğsü (150g)</li>
                            <li>Kahverengi pirinç (1 kase)</li>
                            <li>Karışık sebzeler</li>
                            <li>Zeytinyağı (1 yemek kaşığı)</li>
                        </ul>
                    </div>
                    <div class="mb-4">
                        <h4 class="font-medium text-gray-700">Ara Öğün</h4>
                        <ul class="list-disc list-inside text-gray-600 text-sm">
                            <li>Yulaf/protein barı</li>
                            <li>Elma</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-700">Akşam Yemeği</h4>
                        <ul class="list-disc list-inside text-gray-600 text-sm">
                            <li>Somon (150g)</li>
                            <li>Tatlı patates (1 orta boy)</li>
                            <li>Brokoli ve karnabahar</li>
                            <li>Avokado (1/2)</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-4 bg-green-50 border-b">
                    <h3 class="font-semibold text-gray-800">Yağ Yakımı Örnek Plan</h3>
                </div>
                <div class="p-4">
                    <div class="mb-4">
                        <h4 class="font-medium text-gray-700">Kahvaltı</h4>
                        <ul class="list-disc list-inside text-gray-600 text-sm">
                            <li>Omlet (2 tam yumurta + 2 beyaz)</li>
                            <li>Ispanak ve mantar</li>
                            <li>Avokado (1/4)</li>
                        </ul>
                    </div>
                    <div class="mb-4">
                        <h4 class="font-medium text-gray-700">Ara Öğün</h4>
                        <ul class="list-disc list-inside text-gray-600 text-sm">
                            <li>Yoğurt (150g, yağsız)</li>
                            <li>Yaban mersini</li>
                            <li>Chia tohumu (1 yemek kaşığı)</li>
                        </ul>
                    </div>
                    <div class="mb-4">
                        <h4 class="font-medium text-gray-700">Öğle Yemeği</h4>
                        <ul class="list-disc list-inside text-gray-600 text-sm">
                            <li>Hindi göğsü (120g)</li>
                            <li>Büyük karışık salata</li>
                            <li>Zeytinyağı ve limon suyu</li>
                        </ul>
                    </div>
                    <div class="mb-4">
                        <h4 class="font-medium text-gray-700">Ara Öğün</h4>
                        <ul class="list-disc list-inside text-gray-600 text-sm">
                            <li>Protein shake</li>
                            <li>Salatalık dilimleri</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-700">Akşam Yemeği</h4>
                        <ul class="list-disc list-inside text-gray-600 text-sm">
                            <li>Izgara balık (120g)</li>
                            <li>Buharda sebzeler</li>
                            <li>Kinoa (1/2 kase)</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-4 bg-yellow-50 border-b">
                    <h3 class="font-semibold text-gray-800">Performans Örnek Plan</h3>
                </div>
                <div class="p-4">
                    <div class="mb-4">
                        <h4 class="font-medium text-gray-700">Kahvaltı</h4>
                        <ul class="list-disc list-inside text-gray-600 text-sm">
                            <li>Yulaf ezmesi (1 kase)</li>
                            <li>Muz ve bal</li>
                            <li>Yumurta (2 adet)</li>
                            <li>Yeşil çay</li>
                        </ul>
                    </div>
                    <div class="mb-4">
                        <h4 class="font-medium text-gray-700">Antrenman Öncesi</h4>
                        <ul class="list-disc list-inside text-gray-600 text-sm">
                            <li>Tam tahıllı ekmek (2 dilim)</li>
                            <li>Yağsız peynir</li>
                            <li>Mevsim meyveleri</li>
                        </ul>
                    </div>
                    <div class="mb-4">
                        <h4 class="font-medium text-gray-700">Antrenman Sonrası</h4>
                        <ul class="list-disc list-inside text-gray-600 text-sm">
                            <li>Protein shake</li>
                            <li>Muz</li>
                            <li>BCAA takviyesi</li>
                        </ul>
                    </div>
                    <div class="mb-4">
                        <h4 class="font-medium text-gray-700">Öğle Yemeği</h4>
                        <ul class="list-disc list-inside text-gray-600 text-sm">
                            <li>Tavuk göğsü (150g)</li>
                            <li>Kahverengi pirinç (1 kase)</li>
                            <li>Karışık sebzeler</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-700">Akşam Yemeği</h4>
                        <ul class="list-disc list-inside text-gray-600 text-sm">
                            <li>Lean kırmızı et (120g)</li>
                            <li>Tatlı patates (1 orta boy)</li>
                            <li>Buharda sebzeler</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sık Sorulan Sorular -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Sık Sorulan Sorular</h2>
        
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6 space-y-4">
                <div x-data="{ open: false }" class="border-b border-gray-200 pb-4">
                    <button @click="open = !open" class="flex justify-between items-center w-full text-left font-medium text-gray-900 focus:outline-none">
                        <span>Antrenman öncesi ne yemeliyim?</span>
                        <i :class="open ? 'fas fa-chevron-up' : 'fas fa-chevron-down'" class="text-gray-500"></i>
                    </button>
                    <div x-show="open" class="mt-2 text-sm text-gray-600">
                        <p>Antrenman öncesi, 2-3 saat önce kompleks karbonhidratlar ve orta miktarda protein içeren bir öğün tüketmeniz idealdir. Bu, vücudunuza enerji sağlar ve kas yıkımını önlemeye yardımcı olur. Eğer zamanınız kısıtlıysa, antrenman öncesi 30-60 dakika önce muz, yulaf barı gibi hızlı sindirilen bir atıştırmalık tercih edebilirsiniz.</p>
                    </div>
                </div>
                
                <div x-data="{ open: false }" class="border-b border-gray-200 pb-4">
                    <button @click="open = !open" class="flex justify-between items-center w-full text-left font-medium text-gray-900 focus:outline-none">
                        <span>Ne kadar protein tüketmeliyim?</span>
                        <i :class="open ? 'fas fa-chevron-up' : 'fas fa-chevron-down'" class="text-gray-500"></i>
                    </button>
                    <div x-show="open" class="mt-2 text-sm text-gray-600">
                        <p>Protein ihtiyacınız aktivite seviyenize ve hedeflerinize bağlıdır. Genel olarak, aktif bireylerin günlük vücut ağırlığının kilogramı başına 1.4-2.2 gram protein alması önerilir. Kas kazanımı hedefliyorsanız üst sınıra, kilo kaybı hedefliyorsanız orta-üst sınıra yakın protein alımı faydalı olabilir.</p>
                    </div>
                </div>
                
                <div x-data="{ open: false }" class="border-b border-gray-200 pb-4">
                    <button @click="open = !open" class="flex justify-between items-center w-full text-left font-medium text-gray-900 focus:outline-none">
                        <span>Karbonhidratlar zararlı mı?</span>
                        <i :class="open ? 'fas fa-chevron-up' : 'fas fa-chevron-down'" class="text-gray-500"></i>
                    </button>
                    <div x-show="open" class="mt-2 text-sm text-gray-600">
                        <p>Hayır, karbonhidratlar zararlı değildir. Karbonhidratlar, vücudun birincil enerji kaynağıdır ve özellikle yoğun antrenman yapanlar için gereklidir. Ancak karbonhidrat kalitesi önemlidir. İşlenmiş şekerler ve rafine karbonhidratlar yerine tam tahıllar, meyveler, sebzeler ve baklagiller gibi kompleks karbonhidratları tercih etmelisiniz.</p>
                    </div>
                </div>
                
                <div x-data="{ open: false }" class="border-b border-gray-200 pb-4">
                    <button @click="open = !open" class="flex justify-between items-center w-full text-left font-medium text-gray-900 focus:outline-none">
                        <span>Beslenme takviyeleri kullanmalı mıyım?</span>
                        <i :class="open ? 'fas fa-chevron-up' : 'fas fa-chevron-down'" class="text-gray-500"></i>
                    </button>
                    <div x-show="open" class="mt-2 text-sm text-gray-600">
                        <p>Besin takviyeleri, dengeli bir beslenme düzenine sahip olmadığınızda veya belirli besin ihtiyaçlarınızı karşılayamadığınızda faydalı olabilir. Whey protein, kreatin, BCAA'lar ve balık yağı gibi takviyeler, sporcularda yaygın olarak kullanılır ve bilimsel olarak etkinlikleri kanıtlanmıştır. Ancak, takviyelere başlamadan önce bir sağlık uzmanına danışmanız önerilir.</p>
                    </div>
                </div>
                
                <div x-data="{ open: false }">
                    <button @click="open = !open" class="flex justify-between items-center w-full text-left font-medium text-gray-900 focus:outline-none">
                        <span>Öğün sıklığı ne kadar olmalı?</span>
                        <i :class="open ? 'fas fa-chevron-up' : 'fas fa-chevron-down'" class="text-gray-500"></i>
                    </button>
                    <div x-show="open" class="mt-2 text-sm text-gray-600">
                        <p>Öğün sıklığı kişisel tercihlere, yaşam tarzına ve hedeflere bağlı olarak değişebilir. Genellikle günde 3-6 öğün tüketmek yaygındır. Bazı araştırmalar, toplam kalori ve makro besin alımının, öğün sıklığından daha önemli olduğunu göstermektedir. Vücudunuzu dinleyin ve sizin için en uygun öğün planını belirleyin.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Beslenme Danışmanlığı -->
    <div class="bg-blue-50 rounded-lg shadow-md p-6">
        <div class="md:flex items-center">
            <div class="md:w-3/4 pr-0 md:pr-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Kişisel Beslenme Danışmanlığı</h2>
                <p class="text-gray-600 mb-4">
                    Hedeflerinize daha hızlı ulaşmak ve kişiselleştirilmiş bir beslenme planı almak için beslenme uzmanlarımızla çalışabilirsiniz. 
                    Uzmanlarımız, vücut tipinize, yaşam tarzınıza ve fitness hedeflerinize göre özel planlar oluşturarak başarınızı destekler.
                </p>
                <div class="mt-4">
                    <a href="#" class="inline-flex items-center px-5 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-md shadow transition duration-300">
                        <i class="fas fa-calendar-check mr-2"></i> Randevu Al
                    </a>
                </div>
            </div>
            <div class="md:w-1/4 mt-4 md:mt-0">
                <img src="assets/img/nutrition-coach.jpg" alt="Beslenme uzmanı" class="rounded-lg w-full h-auto shadow">
            </div>
        </div>
    </div>
</div>

<?php include_once 'views/partials/footer.php'; ?> 