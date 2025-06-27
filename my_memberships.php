<?php
/**
 * Üyeliklerim Sayfası
 * Bu sayfa kullanıcının üyeliklerini görüntüler
 */
require_once 'config/init.php';

// Oturum kontrolü
if (!isset($_SESSION['user_id'])) {
    setError('Bu sayfayı görüntülemek için giriş yapmalısınız.');
    redirect('login.php');
    exit;
}

// MembershipController sınıfını yükle
require_once 'controllers/MembershipController.php';
$membershipController = new MembershipController();

// Kullanıcının üyeliklerini al
$memberships = $membershipController->getUserMemberships($_SESSION['user_id']);

// Aktif üyelik varsa al
$activeMembership = $membershipController->getActiveMembership($_SESSION['user_id']);

// Sayfa başlığı
$pageTitle = "Üyeliklerim";

// Üst kısmı dahil et
include_once 'views/partials/header.php';
include_once 'views/partials/navbar.php';
?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Üyeliklerim</h1>
    
    <!-- Aktif Üyelik Bilgisi -->
    <div class="mb-8">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Aktif Üyelik Durumu</h2>
        
        <?php if ($activeMembership): ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden border-t-4 border-green-500">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800"><?php echo htmlspecialchars($activeMembership['package_name']); ?></h3>
                            <p class="text-sm text-gray-500 mt-1">
                                <i class="far fa-calendar-alt mr-1"></i> 
                                <?php echo date('d.m.Y', strtotime($activeMembership['start_date'])); ?> - 
                                <?php echo date('d.m.Y', strtotime($activeMembership['end_date'])); ?>
                            </p>
                        </div>
                        <div class="mt-4 md:mt-0">
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i> Aktif
                            </span>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <?php
                            // Geçen süreyi hesapla
                            $startDate = strtotime($activeMembership['start_date']);
                            $endDate = strtotime($activeMembership['end_date']);
                            $now = time();
                            
                            $totalDays = ($endDate - $startDate) / (60 * 60 * 24);
                            $daysLeft = ($endDate - $now) / (60 * 60 * 24);
                            $daysUsed = $totalDays - $daysLeft;
                            
                            // İlerleme yüzdesi
                            $progressPercent = min(100, max(0, ($daysUsed / $totalDays) * 100));
                            ?>
                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: <?php echo $progressPercent; ?>%"></div>
                        </div>
                        <div class="flex justify-between text-sm mt-1">
                            <span><?php echo date('d.m.Y', $startDate); ?></span>
                            <span class="font-medium"><?php echo round($daysLeft); ?> gün kaldı</span>
                            <span><?php echo date('d.m.Y', $endDate); ?></span>
                        </div>
                    </div>
                    
                    <!-- Detay ve Yenileme Linki -->
                    <div class="mt-6 flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
                        <a href="memberships.php?action=show&id=<?php echo $activeMembership['id']; ?>" class="inline-flex justify-center items-center px-4 py-2 border border-blue-500 text-blue-500 rounded-md hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                            <i class="fas fa-info-circle mr-2"></i> Detayları Görüntüle
                        </a>
                        <a href="packages.php" class="inline-flex justify-center items-center px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                            <i class="fas fa-sync mr-2"></i> Üyeliğimi Yenile
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden border-t-4 border-yellow-500">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-yellow-500 text-3xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Aktif üyeliğiniz bulunmuyor</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Şu anda aktif bir üyeliğiniz bulunmamaktadır. Salon hizmetlerimizden yararlanmak için bir üyelik paketi satın alabilirsiniz.
                            </p>
                            <div class="mt-4">
                                <a href="packages.php" class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                                    <i class="fas fa-shopping-cart mr-2"></i> Paketleri İncele
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Üyelik Geçmişi -->
    <div class="mb-8">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Üyelik Geçmişim</h2>
        
        <?php if (!empty($memberships)): ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paket</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Başlangıç</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bitiş</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($memberships as $membership): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($membership['package_name']); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?php echo date('d.m.Y', strtotime($membership['start_date'])); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?php echo date('d.m.Y', strtotime($membership['end_date'])); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                        $now = time();
                                        $endDate = strtotime($membership['end_date']);
                                        $isActive = ($now <= $endDate && $membership['status'] == 'active');
                                        
                                        if ($isActive): ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Aktif
                                            </span>
                                        <?php else: ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                Sona Ermiş
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="memberships.php?action=show&id=<?php echo $membership['id']; ?>" class="text-blue-600 hover:text-blue-900">
                                            Detaylar
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <p class="text-gray-500">Henüz üyelik kaydınız bulunmuyor.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Sık Sorulan Sorular -->
    <div class="mb-8">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Sık Sorulan Sorular</h2>
        
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6 space-y-4">
                <div x-data="{ open: false }">
                    <button @click="open = !open" class="flex justify-between items-center w-full text-left font-medium text-gray-900 focus:outline-none">
                        <span>Üyeliğim ne zaman sona erecek?</span>
                        <i :class="open ? 'fas fa-chevron-up' : 'fas fa-chevron-down'" class="text-gray-500"></i>
                    </button>
                    <div x-show="open" class="mt-2 text-sm text-gray-600">
                        <p>Üyeliğinizin bitiş tarihi üyelik detay sayfasında belirtilmiştir. Ayrıca aktif üyelik bilgilerinizin yanında kalan gün sayısını da görebilirsiniz.</p>
                    </div>
                </div>
                
                <div x-data="{ open: false }">
                    <button @click="open = !open" class="flex justify-between items-center w-full text-left font-medium text-gray-900 focus:outline-none">
                        <span>Üyeliğimi nasıl yenileyebilirim?</span>
                        <i :class="open ? 'fas fa-chevron-up' : 'fas fa-chevron-down'" class="text-gray-500"></i>
                    </button>
                    <div x-show="open" class="mt-2 text-sm text-gray-600">
                        <p>Üyeliğinizi yenilemek için "Üyeliğimi Yenile" butonuna tıklayarak paketleri inceleyebilir ve yeni bir üyelik satın alabilirsiniz. Alternatif olarak, resepsiyonu ziyaret ederek de üyeliğinizi yenileyebilirsiniz.</p>
                    </div>
                </div>
                
                <div x-data="{ open: false }">
                    <button @click="open = !open" class="flex justify-between items-center w-full text-left font-medium text-gray-900 focus:outline-none">
                        <span>Üyeliğimi dondurabilir miyim?</span>
                        <i :class="open ? 'fas fa-chevron-up' : 'fas fa-chevron-down'" class="text-gray-500"></i>
                    </button>
                    <div x-show="open" class="mt-2 text-sm text-gray-600">
                        <p>Evet, bazı durumlarda üyeliğinizi dondurabilirsiniz. Bunun için resepsiyona başvurmanız ve gerekli belgeleri sunmanız gerekmektedir. Dondurma süresi, üyelik tipinize bağlı olarak değişebilir.</p>
                    </div>
                </div>
                
                <div x-data="{ open: false }">
                    <button @click="open = !open" class="flex justify-between items-center w-full text-left font-medium text-gray-900 focus:outline-none">
                        <span>Ödeme geçmişimi nasıl görebilirim?</span>
                        <i :class="open ? 'fas fa-chevron-up' : 'fas fa-chevron-down'" class="text-gray-500"></i>
                    </button>
                    <div x-show="open" class="mt-2 text-sm text-gray-600">
                        <p>Ödeme geçmişinizi görmek için üyelik detaylarına tıklayabilirsiniz. Her üyelik için yapılan ödemeler ilgili üyelik sayfasında listelenmektedir. Ayrıca, profilinizden de tüm ödeme geçmişinize erişebilirsiniz.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Destek Bilgileri -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Yardıma mı ihtiyacınız var?</h2>
            <p class="text-gray-600 mb-4">Üyelik işlemleriyle ilgili herhangi bir sorunuz varsa, aşağıdaki iletişim kanallarından bize ulaşabilirsiniz:</p>
            
            <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-phone-alt text-blue-500 text-lg"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-gray-900">Telefon</h3>
                        <p class="text-sm text-gray-500">+90 (212) 555 7890</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-envelope text-blue-500 text-lg"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-gray-900">E-posta</h3>
                        <p class="text-sm text-gray-500">info@e-salon.com</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-map-marker-alt text-blue-500 text-lg"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-gray-900">Resepsiyon</h3>
                        <p class="text-sm text-gray-500">Pazartesi-Cumartesi: 08:00 - 22:00</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'views/partials/footer.php'; ?> 