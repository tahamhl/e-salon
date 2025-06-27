<?php include_once 'views/partials/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl md:text-3xl font-bold mb-6 text-gray-800">Üyelik Paketlerimiz</h1>
    
    <div class="flex flex-col md:flex-row justify-between mb-8">
        <div class="w-full md:w-2/3 mb-4 md:mb-0">
            <p class="text-lg text-gray-600">
                Farklı ihtiyaçlara göre hazırlanmış üyelik paketlerimizden size en uygun olanı seçin. 
                Tüm paketlerimiz salon ekipmanları ve grup derslerine erişimi içerir.
            </p>
        </div>
        <div class="w-full md:w-1/3 md:text-right">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="my_memberships.php" class="inline-block py-2 px-4 border border-blue-500 text-blue-600 rounded hover:bg-blue-50 transition duration-300">
                    <i class="fas fa-id-card mr-2"></i> Üyeliklerim
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
        <?php foreach ($packages as $package): ?>
        <div class="bg-white rounded-lg shadow-md overflow-hidden <?php echo ($package['is_highlighted']) ? 'ring-2 ring-blue-500' : ''; ?>">
            <?php if ($package['is_highlighted']): ?>
            <div class="bg-blue-600 text-white p-3 flex items-center">
                <span class="bg-yellow-500 text-xs font-bold py-1 px-2 rounded-full mr-2">Popüler</span> 
                <span>En Çok Tercih Edilen</span>
            </div>
            <?php endif; ?>
            
            <div class="p-6">
                <h5 class="text-xl font-semibold text-gray-800"><?php echo $package['name']; ?></h5>
                <h6 class="text-sm text-gray-500 mt-1">
                    <?php echo $package['duration_unit'] == 'day' ? $package['duration'] . ' gün' : ($package['duration'] / 30) . ' ay'; ?>
                </h6>
                
                <p class="mt-4 text-gray-600">
                    <?php echo $package['description']; ?>
                </p>
                
                <?php if (!empty($package['features'])): ?>
                <div class="mt-4">
                    <h6 class="font-medium text-gray-700">Paket Özellikleri</h6>
                    <ul class="mt-2 space-y-2">
                        <?php 
                        $features = explode("\n", $package['features']);
                        foreach ($features as $feature): 
                            if (trim($feature)):
                        ?>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                            <span class="text-gray-600"><?php echo trim($feature); ?></span>
                        </li>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </ul>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($package['session_limit'])): ?>
                <div class="mt-4 flex items-start bg-blue-50 text-blue-700 p-3 rounded">
                    <i class="fas fa-info-circle mt-1 mr-2"></i>
                    <p>Bu paket <?php echo $package['session_limit']; ?> seans kullanım hakkı içerir.</p>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-100">
                <div class="flex flex-col sm:flex-row justify-between items-center">
                    <span class="text-2xl font-bold text-gray-800 mb-3 sm:mb-0"><?php echo number_format($package['price'], 2, ',', '.'); ?> ₺</span>
                    <a href="packages.php?action=purchase&id=<?php echo $package['id']; ?>" class="w-full sm:w-auto inline-block text-center py-2 px-4 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-300">
                        <i class="fas fa-shopping-cart mr-2"></i> Satın Al
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <div class="mt-12">
        <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <h5 class="text-lg font-semibold text-gray-800">Sıkça Sorulan Sorular</h5>
            </div>
            <div class="p-6">
                <div class="space-y-4" x-data="{active: 1}">
                    <div class="border border-gray-200 rounded-md">
                        <button @click="active !== 1 ? active = 1 : active = null" class="flex justify-between items-center w-full p-4 text-left font-medium">
                            <span>Üyeliğimi nasıl başlatabilirim?</span>
                            <svg class="w-5 h-5 transform transition-transform duration-300" :class="{'rotate-180': active === 1}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </button>
                        <div x-show="active === 1" class="p-4 pt-0 border-t border-gray-200">
                            <p class="text-gray-600">
                                İstediğiniz paketi seçip "Satın Al" butonuna tıklayarak üyelik işleminizi başlatabilirsiniz. 
                                Ödeme işlemini online olarak kredi kartı ile yapabilir veya salonumuza gelerek nakit ödeme seçeneğini kullanabilirsiniz.
                            </p>
                        </div>
                    </div>
                    
                    <div class="border border-gray-200 rounded-md">
                        <button @click="active !== 2 ? active = 2 : active = null" class="flex justify-between items-center w-full p-4 text-left font-medium">
                            <span>Üyeliğimi dondurabilir miyim?</span>
                            <svg class="w-5 h-5 transform transition-transform duration-300" :class="{'rotate-180': active === 2}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </button>
                        <div x-show="active === 2" class="p-4 pt-0 border-t border-gray-200">
                            <p class="text-gray-600">
                                Evet, sağlık sorunları ve şehir dışı seyahatler gibi özel durumlarda üyeliğinizi en fazla 30 gün dondurabilirsiniz. 
                                Bu işlemi resepsiyondan veya üyelik sayfanız üzerinden talep edebilirsiniz.
                            </p>
                        </div>
                    </div>
                    
                    <div class="border border-gray-200 rounded-md">
                        <button @click="active !== 3 ? active = 3 : active = null" class="flex justify-between items-center w-full p-4 text-left font-medium">
                            <span>Grup derslerine nasıl katılabilirim?</span>
                            <svg class="w-5 h-5 transform transition-transform duration-300" :class="{'rotate-180': active === 3}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </button>
                        <div x-show="active === 3" class="p-4 pt-0 border-t border-gray-200">
                            <p class="text-gray-600">
                                Tüm üyelik paketleri sınırsız grup dersi katılım hakkı içerir. Derslere katılmak için Program sayfasından ders programını inceleyebilir ve 
                                salondan veya uygulamamız üzerinden rezervasyon yaptırabilirsiniz.
                            </p>
                        </div>
                    </div>
                    
                    <div class="border border-gray-200 rounded-md">
                        <button @click="active !== 4 ? active = 4 : active = null" class="flex justify-between items-center w-full p-4 text-left font-medium">
                            <span>Özel ders alabilir miyim?</span>
                            <svg class="w-5 h-5 transform transition-transform duration-300" :class="{'rotate-180': active === 4}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </button>
                        <div x-show="active === 4" class="p-4 pt-0 border-t border-gray-200">
                            <p class="text-gray-600">
                                Evet, uzman eğitmenlerimizden özel ders alabilirsiniz. Özel ders ücretleri paket fiyatlarına dahil değildir ve 
                                ayrıca ücretlendirilir. Detaylı bilgi için resepsiyonumuza başvurabilirsiniz.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'views/partials/footer.php'; ?> 