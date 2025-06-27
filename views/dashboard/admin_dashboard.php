<?php
// Üst kısmı dahil et
include_once 'views/partials/header.php';
include_once 'views/partials/navbar.php';
?>

<div class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- İstatistik kartları -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            <!-- Toplam üye sayısı -->
            <div class="bg-white rounded-lg shadow-md p-6 flex items-center">
                <div class="mr-4 bg-blue-100 rounded-full p-3">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-sm text-gray-500 uppercase">Toplam Üye</h2>
                    <p class="text-2xl font-bold"><?php echo $totalMembers; ?></p>
                </div>
            </div>
            
            <!-- Aktif paket sayısı -->
            <div class="bg-white rounded-lg shadow-md p-6 flex items-center">
                <div class="mr-4 bg-green-100 rounded-full p-3">
                    <i class="fas fa-box-open text-green-600 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-sm text-gray-500 uppercase">Aktif Paket</h2>
                    <p class="text-2xl font-bold"><?php echo $activePackages; ?></p>
                </div>
            </div>
            
            <!-- Günlük gelir -->
            <div class="bg-white rounded-lg shadow-md p-6 flex items-center">
                <div class="mr-4 bg-yellow-100 rounded-full p-3">
                    <i class="fas fa-coins text-yellow-600 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-sm text-gray-500 uppercase">Bugünkü Gelir</h2>
                    <p class="text-2xl font-bold"><?php echo formatMoney($todayIncome); ?></p>
                </div>
            </div>
            
            <!-- Aylık gelir -->
            <div class="bg-white rounded-lg shadow-md p-6 flex items-center">
                <div class="mr-4 bg-purple-100 rounded-full p-3">
                    <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-sm text-gray-500 uppercase">Aylık Gelir</h2>
                    <p class="text-2xl font-bold"><?php echo formatMoney($monthlyIncome); ?></p>
                </div>
            </div>
            
            <!-- Bugünkü ziyaretler -->
            <div class="bg-white rounded-lg shadow-md p-6 flex items-center">
                <div class="mr-4 bg-red-100 rounded-full p-3">
                    <i class="fas fa-calendar-check text-red-600 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-sm text-gray-500 uppercase">Bugünkü Ziyaret</h2>
                    <p class="text-2xl font-bold"><?php echo $todayVisits; ?></p>
                </div>
            </div>
        </div>
        
        <!-- Hızlı işlemler -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Hızlı İşlemler</h2>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <a href="users.php" class="flex flex-col items-center justify-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition duration-300">
                    <div class="w-12 h-12 flex items-center justify-center rounded-full bg-blue-100 text-blue-600 mb-2">
                        <i class="fas fa-user-plus text-lg"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Yeni Üye</span>
                </a>
                
                <a href="payments.php?action=new" class="flex flex-col items-center justify-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition duration-300">
                    <div class="w-12 h-12 flex items-center justify-center rounded-full bg-green-100 text-green-600 mb-2">
                        <i class="fas fa-money-bill-wave text-lg"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Ödeme Al</span>
                </a>
                
                <a href="packages.php?action=manage" class="flex flex-col items-center justify-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition duration-300">
                    <div class="w-12 h-12 flex items-center justify-center rounded-full bg-yellow-100 text-yellow-600 mb-2">
                        <i class="fas fa-box text-lg"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Paketler</span>
                </a>
                
                <a href="trainers.php?action=manage" class="flex flex-col items-center justify-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition duration-300">
                    <div class="w-12 h-12 flex items-center justify-center rounded-full bg-purple-100 text-purple-600 mb-2">
                        <i class="fas fa-user-tie text-lg"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Eğitmenler</span>
                </a>
                
                <a href="reports.php" class="flex flex-col items-center justify-center p-4 bg-red-50 rounded-lg hover:bg-red-100 transition duration-300">
                    <div class="w-12 h-12 flex items-center justify-center rounded-full bg-red-100 text-red-600 mb-2">
                        <i class="fas fa-chart-bar text-lg"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Raporlar</span>
                </a>
            </div>
        </div>
        
        <!-- Alt bölüm -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Son üyeler -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-700">Son Üyelik Kayıtları</h2>
                    <a href="users.php" class="text-sm text-blue-600 hover:text-blue-800">
                        Tümünü Gör <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Üye</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İşlem</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($recentMembers as $member): ?>
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-sm font-medium">
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
                                        <?php echo $member['email']; ?>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo formatDate($member['created_at']); ?>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        <a href="users.php?action=edit&id=<?php echo $member['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="users.php?action=view&id=<?php echo $member['id']; ?>" class="text-green-600 hover:text-green-900">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Son ödemeler -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-700">Son Ödemeler</h2>
                    <a href="payments.php" class="text-sm text-blue-600 hover:text-blue-800">
                        Tümünü Gör <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
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
            </div>
        </div>
    </div>
</div>

<?php 
// Alt kısmı dahil et
include_once 'views/partials/footer.php'; 
?> 