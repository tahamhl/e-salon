<?php
// Üst kısmı dahil et
include_once 'views/partials/header.php';

// Gerekli değişkenleri ayarla
$user = $dashboardData['user'];
$membership = $dashboardData['membership'] ?? null;
$visits = $dashboardData['visits'] ?? [];
$payments = $dashboardData['payments'] ?? [];
?>

<div class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Üst bilgi kartları -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Üyelik durumu -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-700 mb-2">Üyelik Durumu</h2>
                <?php if (isset($membership) && $membership): ?>
                    <div class="flex items-center mb-2">
                        <div class="w-3 h-3 rounded-full <?php echo strtotime($membership['end_date']) >= time() ? 'bg-green-500' : 'bg-red-500'; ?> mr-2"></div>
                        <span class="text-sm font-medium"><?php echo strtotime($membership['end_date']) >= time() ? 'Aktif' : 'Süresi Dolmuş'; ?></span>
                    </div>
                    <div class="text-sm text-gray-600 mb-1">
                        <span class="font-medium">Paket:</span> <?php echo $membership['package_name']; ?>
                    </div>
                    <div class="text-sm text-gray-600 mb-1">
                        <span class="font-medium">Başlangıç:</span> <?php echo formatDate($membership['start_date']); ?>
                    </div>
                    <div class="text-sm text-gray-600 mb-4">
                        <span class="font-medium">Bitiş:</span> <?php echo formatDate($membership['end_date']); ?>
                    </div>
                    
                    <?php if (strtotime($membership['end_date']) < time()): ?>
                        <a href="packages.php" class="block text-center bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md text-sm transition duration-300">
                            Üyeliğini Yenile
                        </a>
                    <?php else: ?>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 mb-1">
                            <?php 
                            $startDate = strtotime($membership['start_date']);
                            $endDate = strtotime($membership['end_date']);
                            $today = time();
                            $progress = ($today - $startDate) / ($endDate - $startDate) * 100;
                            $progress = min(100, max(0, $progress));
                            ?>
                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: <?php echo $progress; ?>%"></div>
                        </div>
                        <p class="text-xs text-gray-500 text-right">
                            <?php echo ceil(($endDate - $today) / 86400); ?> gün kaldı
                        </p>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="text-sm text-gray-600">Aktif üyeliğiniz bulunmuyor.</p>
                    <a href="packages.php" class="block text-center mt-4 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md text-sm transition duration-300">
                        Paketleri İncele
                    </a>
                <?php endif; ?>
            </div>
            
            <!-- Son ziyaretler -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-700 mb-4">Son Ziyaretlerim</h2>
                <?php if (isset($visits) && count($visits) > 0): ?>
                    <ul class="space-y-3">
                        <?php foreach ($visits as $visit): ?>
                            <li class="flex items-center text-sm">
                                <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full bg-blue-100 text-blue-600 mr-3">
                                    <i class="fas fa-calendar-check"></i>
                                </span>
                                <div>
                                    <p class="font-medium"><?php echo date('d.m.Y', strtotime($visit['check_in_time'])); ?></p>
                                    <p class="text-gray-500"><?php echo date('H:i', strtotime($visit['check_in_time'])); ?></p>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-sm text-gray-600">Henüz ziyaret kaydınız bulunmuyor.</p>
                <?php endif; ?>
                <a href="visits.php" class="block text-sm text-blue-600 hover:text-blue-800 mt-4 text-right">
                    Tüm ziyaretleri gör <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            
            <!-- Profil kartı -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 rounded-full bg-blue-500 flex items-center justify-center text-white text-xl font-bold mr-4">
                        <?php echo substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1); ?>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold"><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></h2>
                        <p class="text-sm text-gray-600"><?php echo ucfirst($user['role']); ?></p>
                    </div>
                </div>
                <div class="space-y-2 mb-4">
                    <div class="flex items-center text-sm">
                        <i class="fas fa-envelope w-5 text-gray-500 mr-2"></i>
                        <span><?php echo $user['email']; ?></span>
                    </div>
                    <div class="flex items-center text-sm">
                        <i class="fas fa-phone w-5 text-gray-500 mr-2"></i>
                        <span><?php echo $user['phone']; ?></span>
                    </div>
                    <div class="flex items-center text-sm">
                        <i class="fas fa-calendar w-5 text-gray-500 mr-2"></i>
                        <span>Üyelik: <?php echo formatDate($user['created_at']); ?></span>
                    </div>
                </div>
                <a href="profile.php" class="block text-center bg-gray-200 hover:bg-gray-300 text-gray-700 py-2 px-4 rounded-md text-sm transition duration-300">
                    Profili Düzenle
                </a>
            </div>
        </div>
        
        <!-- Alt bölüm -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Son ödemeler -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-700 mb-4">Son Ödemelerim</h2>
                <?php if (isset($payments) && count($payments) > 0): ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tutar</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ödeme Tipi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($payments as $payment): ?>
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo formatDate($payment['payment_date'], 'd.m.Y H:i'); ?>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <?php echo formatMoney($payment['amount']); ?>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                            <?php 
                                            $paymentTypes = [
                                                'cash' => 'Nakit',
                                                'credit_card' => 'Kredi Kartı',
                                                'bank_transfer' => 'Havale'
                                            ];
                                            echo isset($paymentTypes[$payment['payment_type']]) ? $paymentTypes[$payment['payment_type']] : $payment['payment_type']; 
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <a href="payments.php" class="block text-sm text-blue-600 hover:text-blue-800 mt-4 text-right">
                        Tüm ödemeleri gör <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                <?php else: ?>
                    <p class="text-sm text-gray-600">Henüz ödeme kaydınız bulunmuyor.</p>
                <?php endif; ?>
            </div>
            
            <!-- Hızlı işlemler -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-700 mb-4">Hızlı İşlemler</h2>
                <div class="grid grid-cols-2 gap-4">
                    <a href="packages.php" class="flex flex-col items-center justify-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition duration-300">
                        <div class="w-12 h-12 flex items-center justify-center rounded-full bg-blue-100 text-blue-600 mb-2">
                            <i class="fas fa-box-open text-lg"></i>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Paketleri İncele</span>
                    </a>
                    
                    <a href="trainers.php" class="flex flex-col items-center justify-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition duration-300">
                        <div class="w-12 h-12 flex items-center justify-center rounded-full bg-green-100 text-green-600 mb-2">
                            <i class="fas fa-user-tie text-lg"></i>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Eğitmenler</span>
                    </a>
                    
                    <a href="schedule.php" class="flex flex-col items-center justify-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition duration-300">
                        <div class="w-12 h-12 flex items-center justify-center rounded-full bg-purple-100 text-purple-600 mb-2">
                            <i class="fas fa-calendar-alt text-lg"></i>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Program</span>
                    </a>
                    
                    <a href="nutrition.php" class="flex flex-col items-center justify-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition duration-300">
                        <div class="w-12 h-12 flex items-center justify-center rounded-full bg-yellow-100 text-yellow-600 mb-2">
                            <i class="fas fa-apple-alt text-lg"></i>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Beslenme</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
// Alt kısmı dahil et
include_once 'views/partials/footer.php'; 
?> 