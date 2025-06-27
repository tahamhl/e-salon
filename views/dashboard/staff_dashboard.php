<?php
// Üst kısmı dahil et
include_once 'views/partials/header.php';
include_once 'views/partials/navbar.php';
?>

<div class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Hızlı işlemler -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Hızlı İşlemler</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="check_in.php" class="flex flex-col items-center justify-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition duration-300">
                    <div class="w-12 h-12 flex items-center justify-center rounded-full bg-blue-100 text-blue-600 mb-2">
                        <i class="fas fa-clipboard-check text-lg"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Giriş İşlemi</span>
                </a>
                
                <a href="users.php?action=new" class="flex flex-col items-center justify-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition duration-300">
                    <div class="w-12 h-12 flex items-center justify-center rounded-full bg-green-100 text-green-600 mb-2">
                        <i class="fas fa-user-plus text-lg"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Yeni Üye</span>
                </a>
                
                <a href="payments.php?action=new" class="flex flex-col items-center justify-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition duration-300">
                    <div class="w-12 h-12 flex items-center justify-center rounded-full bg-yellow-100 text-yellow-600 mb-2">
                        <i class="fas fa-money-bill-wave text-lg"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Ödeme Al</span>
                </a>
                
                <a href="schedule.php" class="flex flex-col items-center justify-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition duration-300">
                    <div class="w-12 h-12 flex items-center justify-center rounded-full bg-purple-100 text-purple-600 mb-2">
                        <i class="fas fa-calendar-alt text-lg"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Program</span>
                </a>
            </div>
        </div>
        
        <!-- Bugünkü Ziyaretler -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-700">Bugünkü Ziyaretler</h2>
                <a href="check_in.php" class="text-sm text-blue-600 hover:text-blue-800">
                    Giriş İşlemi <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            
            <?php if (count($todayVisits) > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Üye</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giriş Saati</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İşlem</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($todayVisits as $visit): ?>
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-sm font-medium">
                                                <?php echo substr($visit['first_name'], 0, 1) . substr($visit['last_name'], 0, 1); ?>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-gray-900">
                                                    <?php echo $visit['first_name'] . ' ' . $visit['last_name']; ?>
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo date('H:i', strtotime($visit['check_in_time'])); ?>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        <a href="users.php?action=view&id=<?php echo $visit['id']; ?>" class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye"></i> Görüntüle
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="py-8 text-center">
                    <i class="fas fa-calendar-times text-gray-400 text-4xl mb-3"></i>
                    <p class="text-gray-500">Bugün henüz ziyaret kaydı bulunmuyor.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Alt bölüm -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Aktif Üyelikler -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-700">Aktif Üyelikler</h2>
                    <a href="memberships.php" class="text-sm text-blue-600 hover:text-blue-800">
                        Tümünü Gör <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                <?php if (count($activeMembers) > 0): ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Üye</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paket</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bitiş Tarihi</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kalan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($activeMembers as $member): ?>
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8 rounded-full bg-green-500 flex items-center justify-center text-white text-sm font-medium">
                                                    <?php echo substr($member['first_name'], 0, 1) . substr($member['last_name'], 0, 1); ?>
                                                </div>
                                                <div class="ml-3">
                                                    <p class="text-sm font-medium text-gray-900">
                                                        <?php echo $member['first_name'] . ' ' . $member['last_name']; ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo $member['package_name']; ?>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo formatDate($member['end_date']); ?>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                            <?php 
                                            $endDate = strtotime($member['end_date']);
                                            $today = time();
                                            $daysLeft = ceil(($endDate - $today) / 86400);
                                            
                                            if ($daysLeft <= 7) {
                                                echo "<span class='text-red-600 font-medium'>{$daysLeft} gün</span>";
                                            } else {
                                                echo "{$daysLeft} gün";
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="py-8 text-center">
                        <i class="fas fa-user-slash text-gray-400 text-4xl mb-3"></i>
                        <p class="text-gray-500">Aktif üyelik bulunmuyor.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Son Ödemeler -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-700">Son Ödemeler</h2>
                    <a href="payments.php" class="text-sm text-blue-600 hover:text-blue-800">
                        Tümünü Gör <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                <?php if (count($recentPayments) > 0): ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Üye</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tutar</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Yöntem</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($recentPayments as $payment): ?>
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 text-sm font-medium">
                                                    <?php echo substr($payment['first_name'], 0, 1) . substr($payment['last_name'], 0, 1); ?>
                                                </div>
                                                <div class="ml-3">
                                                    <p class="text-sm font-medium text-gray-900">
                                                        <?php echo $payment['first_name'] . ' ' . $payment['last_name']; ?>
                                                    </p>
                                                </div>
                                            </div>
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
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo formatDate($payment['payment_date'], 'd.m.Y H:i'); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="py-8 text-center">
                        <i class="fas fa-money-bill-wave text-gray-400 text-4xl mb-3"></i>
                        <p class="text-gray-500">Henüz ödeme kaydı bulunmuyor.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php 
// Alt kısmı dahil et
include_once 'views/partials/footer.php'; 
?>