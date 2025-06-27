// Sadece admin ve müşteri erişebilir
$allowedRoles = ['admin', 'staff', 'customer'];
if (!checkUserRole($allowedRoles)) {
    redirect('index.php');
}

// Aktif sayfa kontrolü
$page = isset($_GET['page']) ? sanitize($_GET['page']) : 'home';

// Başlık ayarla
$pageTitle = 'Kontrol Paneli';

include_once 'views/partials/header.php';
include_once 'views/partials/navbar.php';
?>

<div class="container mx-auto px-4 py-8">
    
    <?php if (hasRole('admin') || hasRole('staff')): ?>
    <div class="bg-blue-50 dark:bg-slate-800 rounded-lg shadow-md p-6 mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">
            <?php echo $pageTitle; ?>
        </h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- İstatistik Kartları -->
            <div class="bg-white dark:bg-slate-700 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-2">Toplam Üye</h3>
                <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                    <?php 
                    $userController = new UserController();
                    echo $userController->getTotalUsers();
                    ?>
                </p>
            </div>
            
            <div class="bg-white dark:bg-slate-700 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-2">Aktif Üyelikler</h3>
                <p class="text-3xl font-bold text-green-600 dark:text-green-400">
                    <?php 
                    $membershipController = new MembershipController();
                    echo $membershipController->getActiveMemberships();
                    ?>
                </p>
            </div>
            
            <div class="bg-white dark:bg-slate-700 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-2">Toplam Gelir</h3>
                <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">
                    <?php 
                    $paymentController = new PaymentController();
                    echo formatMoney($paymentController->getTotalRevenue());
                    ?>
                </p>
            </div>
            
            <div class="bg-white dark:bg-slate-700 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-2">Bu Ay</h3>
                <p class="text-3xl font-bold text-amber-600 dark:text-amber-400">
                    <?php echo formatMoney($paymentController->getCurrentMonthRevenue()); ?>
                </p>
            </div>
        </div>
    </div>
    
    <div class="flex flex-col md:flex-row gap-6">
        <div class="w-full md:w-3/4">
            <div class="bg-white dark:bg-slate-700 rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Son İşlemler</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white dark:bg-slate-800 border dark:border-slate-700">
                        <thead>
                            <tr class="bg-gray-100 dark:bg-slate-600 text-gray-700 dark:text-gray-300">
                                <th class="py-2 px-4 border-b text-left">Tarih</th>
                                <th class="py-2 px-4 border-b text-left">Üye</th>
                                <th class="py-2 px-4 border-b text-right">Tutar</th>
                                <th class="py-2 px-4 border-b text-center">İşlem Türü</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $recentTransactions = $paymentController->getRecentTransactions(5);
                            foreach($recentTransactions as $transaction): 
                            ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-750">
                                <td class="py-2 px-4 border-b dark:border-slate-700"><?php echo formatDate($transaction['payment_date']); ?></td>
                                <td class="py-2 px-4 border-b dark:border-slate-700"><?php echo htmlspecialchars($transaction['user_name']); ?></td>
                                <td class="py-2 px-4 border-b dark:border-slate-700 text-right"><?php echo formatMoney($transaction['amount']); ?></td>
                                <td class="py-2 px-4 border-b dark:border-slate-700 text-center">
                                    <?php if($transaction['payment_type'] == 'membership'): ?>
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-100 rounded-full text-xs">Üyelik</span>
                                    <?php else: ?>
                                    <span class="px-2 py-1 bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-100 rounded-full text-xs">Diğer</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 text-right">
                    <a href="payments.php" class="text-blue-600 dark:text-blue-400 hover:underline">Tüm İşlemler &rarr;</a>
                </div>
            </div>
        </div>
        
        <div class="w-full md:w-1/4">
            <div class="bg-white dark:bg-slate-700 rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Hızlı İşlemler</h2>
                <div class="flex flex-col space-y-3">
                    <a href="users.php?action=create" class="py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded flex items-center justify-center transition duration-200">
                        <i class="fas fa-user-plus mr-2"></i> Yeni Üye Ekle
                    </a>
                    <a href="memberships.php?action=create" class="py-2 px-4 bg-green-600 hover:bg-green-700 text-white rounded flex items-center justify-center transition duration-200">
                        <i class="fas fa-id-card mr-2"></i> Yeni Üyelik
                    </a>
                    <a href="payments.php?action=create" class="py-2 px-4 bg-purple-600 hover:bg-purple-700 text-white rounded flex items-center justify-center transition duration-200">
                        <i class="fas fa-money-bill-wave mr-2"></i> Ödeme Al
                    </a>
                    <a href="reports.php" class="py-2 px-4 bg-amber-600 hover:bg-amber-700 text-white rounded flex items-center justify-center transition duration-200">
                        <i class="fas fa-chart-bar mr-2"></i> Raporlar
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <?php elseif ($page === 'member' && hasRole('customer')): ?>
    
    <!-- ÜYE KONTROL PANELİ -->
    <div class="bg-blue-50 dark:bg-slate-800 rounded-lg shadow-md p-6 mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">
            Üye Kontrol Paneli
        </h1>
        
        <?php
        $userId = $_SESSION['user_id'];
        $userController = new UserController();
        $membershipController = new MembershipController();
        $user = $userController->getUserById($userId);
        $activeMembership = $membershipController->getActiveMembership($userId);
        ?>
        
        <div class="bg-white dark:bg-slate-700 rounded-lg shadow p-6 mb-6">
            <div class="flex flex-col md:flex-row items-start gap-6">
                <div class="flex-shrink-0">
                    <div class="w-24 h-24 bg-gray-200 dark:bg-slate-600 rounded-full flex items-center justify-center text-3xl text-gray-500 dark:text-gray-400">
                        <?php echo strtoupper(substr($user['name'], 0, 1) . substr($user['surname'], 0, 1)); ?>
                    </div>
                </div>
                
                <div class="flex-grow">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
                        <?php echo htmlspecialchars($user['name'] . ' ' . $user['surname']); ?>
                    </h2>
                    <p class="text-gray-600 dark:text-gray-300 mb-2">
                        <i class="fas fa-envelope mr-2"></i><?php echo htmlspecialchars($user['email']); ?>
                    </p>
                    <p class="text-gray-600 dark:text-gray-300 mb-2">
                        <i class="fas fa-phone mr-2"></i><?php echo htmlspecialchars($user['phone']); ?>
                    </p>
                    
                    <div class="mt-4 flex flex-wrap gap-3">
                        <a href="profile.php" class="py-1 px-4 bg-blue-100 hover:bg-blue-200 text-blue-800 dark:bg-blue-900 dark:hover:bg-blue-800 dark:text-blue-100 rounded-full text-sm transition duration-200">
                            <i class="fas fa-user-edit mr-1"></i> Profili Düzenle
                        </a>
                        <a href="my_memberships.php" class="py-1 px-4 bg-green-100 hover:bg-green-200 text-green-800 dark:bg-green-900 dark:hover:bg-green-800 dark:text-green-100 rounded-full text-sm transition duration-200">
                            <i class="fas fa-id-card mr-1"></i> Üyelik Bilgilerim
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Aktif Üyelik Kartı -->
            <div class="bg-white dark:bg-slate-700 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Aktif Üyelik Durumu</h3>
                
                <?php if ($activeMembership): ?>
                    <div class="border-l-4 border-green-500 pl-4 py-2 mb-4">
                        <p class="font-medium text-green-600 dark:text-green-400">
                            <?php echo htmlspecialchars($activeMembership['package_name']); ?> Paketi Aktif
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            Bitiş: <?php echo formatDate($activeMembership['end_date']); ?>
                        </p>
                    </div>
                    
                    <?php
                    // Üyelik süresi hesaplama
                    $startDate = new DateTime($activeMembership['start_date']);
                    $endDate = new DateTime($activeMembership['end_date']);
                    $today = new DateTime();
                    
                    $totalDays = $startDate->diff($endDate)->days;
                    $daysUsed = $startDate->diff($today)->days;
                    
                    // Eğer bugün bitiş tarihinden sonraysa, kullanılan gün sayısını toplam gün sayısına eşitle
                    if ($today > $endDate) {
                        $daysUsed = $totalDays;
                    }
                    
                    // İlerleme yüzdesi
                    $progressPercent = ($daysUsed / $totalDays) * 100;
                    $progressPercent = min(100, $progressPercent); // 100'den fazla olmamasını sağla
                    ?>
                    
                    <div class="mb-2 flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-300">İlerleme</span>
                        <span class="text-gray-800 dark:text-gray-200 font-medium"><?php echo round($progressPercent); ?>%</span>
                    </div>
                    
                    <div class="w-full bg-gray-200 dark:bg-slate-600 rounded-full h-2.5 mb-4">
                        <div class="bg-green-600 dark:bg-green-500 h-2.5 rounded-full" style="width: <?php echo $progressPercent; ?>%"></div>
                    </div>
                    
                    <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                        <span><?php echo formatDate($activeMembership['start_date']); ?></span>
                        <span><?php echo formatDate($activeMembership['end_date']); ?></span>
                    </div>
                <?php else: ?>
                    <div class="border-l-4 border-yellow-500 pl-4 py-2">
                        <p class="font-medium text-yellow-600 dark:text-yellow-400">
                            Aktif üyeliğiniz bulunmuyor
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            E-Salon'un avantajlarından yararlanmak için üyelik satın alabilirsiniz.
                        </p>
                    </div>
                    <a href="memberships.php" class="mt-4 inline-block py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm transition duration-200">
                        <i class="fas fa-id-card mr-1"></i> Üyelik Paketlerini İncele
                    </a>
                <?php endif; ?>
            </div>
            
            <!-- Hızlı Erişim Kartı -->
            <div class="bg-white dark:bg-slate-700 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Hızlı Erişim</h3>
                <div class="grid grid-cols-2 gap-3">
                    <a href="schedule.php" class="flex flex-col items-center p-4 border dark:border-slate-600 rounded-lg hover:bg-blue-50 dark:hover:bg-slate-600 transition duration-200">
                        <i class="fas fa-calendar-alt text-2xl text-blue-500 mb-2"></i>
                        <span class="text-sm text-center text-gray-800 dark:text-gray-200">Antrenman Programım</span>
                    </a>
                    
                    <a href="nutrition.php" class="flex flex-col items-center p-4 border dark:border-slate-600 rounded-lg hover:bg-blue-50 dark:hover:bg-slate-600 transition duration-200">
                        <i class="fas fa-apple-alt text-2xl text-green-500 mb-2"></i>
                        <span class="text-sm text-center text-gray-800 dark:text-gray-200">Beslenme Rehberi</span>
                    </a>
                    
                    <a href="progress.php" class="flex flex-col items-center p-4 border dark:border-slate-600 rounded-lg hover:bg-blue-50 dark:hover:bg-slate-600 transition duration-200">
                        <i class="fas fa-chart-line text-2xl text-purple-500 mb-2"></i>
                        <span class="text-sm text-center text-gray-800 dark:text-gray-200">İlerleme Takibi</span>
                    </a>
                    
                    <a href="contact.php" class="flex flex-col items-center p-4 border dark:border-slate-600 rounded-lg hover:bg-blue-50 dark:hover:bg-slate-600 transition duration-200">
                        <i class="fas fa-headset text-2xl text-amber-500 mb-2"></i>
                        <span class="text-sm text-center text-gray-800 dark:text-gray-200">Destek Al</span>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Yaklaşan Etkinlikler ve Duyurular -->
        <div class="bg-white dark:bg-slate-700 rounded-lg shadow p-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Duyurular ve Etkinlikler</h3>
            
            <div class="space-y-4">
                <div class="border-l-4 border-blue-500 pl-4 py-2">
                    <h4 class="font-medium text-gray-800 dark:text-white">Yeni Grup Dersleri Eklendi</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Pazartesi ve Çarşamba günleri 18:00'de yeni Pilates dersleri başlıyor.
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">2 gün önce</p>
                </div>
                
                <div class="border-l-4 border-purple-500 pl-4 py-2">
                    <h4 class="font-medium text-gray-800 dark:text-white">Beslenme Semineri</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Bu hafta Cumartesi 15:00'de "Sağlıklı Beslenme İpuçları" semineri düzenlenecektir.
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">1 hafta önce</p>
                </div>
                
                <div class="border-l-4 border-amber-500 pl-4 py-2">
                    <h4 class="font-medium text-gray-800 dark:text-white">Çalışma Saatleri Değişikliği</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Bayram haftasında tesisimiz 09:00-22:00 saatleri arasında hizmet verecektir.
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">2 hafta önce</p>
                </div>
            </div>
        </div>
    </div>
    
    <?php else: ?>
    
    <!-- Varsayılan Dashboard İçeriği -->
    <div class="bg-blue-50 dark:bg-slate-800 rounded-lg shadow-md p-6 mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">
            Üyelik Sistemi
        </h1>
        
        <p class="text-gray-600 dark:text-gray-300 mb-6">
            Üyelik yönetim paneline hoş geldiniz. Lütfen sol menüden yapmak istediğiniz işlemi seçin.
        </p>
        
        <div class="flex flex-wrap gap-4">
            <a href="dashboard.php?page=member" class="py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded transition duration-200">
                <i class="fas fa-user mr-2"></i> Üye Paneli
            </a>
            
            <a href="profile.php" class="py-2 px-4 bg-gray-600 hover:bg-gray-700 text-white rounded transition duration-200">
                <i class="fas fa-user-cog mr-2"></i> Profil Ayarları
            </a>
        </div>
    </div>
    
    <?php endif; ?>
    
</div>

<?php include_once 'views/partials/footer.php'; ?> 