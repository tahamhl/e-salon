<?php
require_once 'views/partials/header.php';
require_once 'views/partials/navbar.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Ödemeler</h1>
        <?php if (isAdmin() || isStaff()): ?>
            <a href="index.php?page=payments&action=create" class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-md text-sm transition duration-300">
                <i class="fas fa-plus mr-1"></i> Yeni Ödeme
            </a>
        <?php endif; ?>
    </div>

    <!-- Filtreleme Seçenekleri -->
    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
        <form action="index.php" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <input type="hidden" name="page" value="payments">
            
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Ara</label>
                <input type="text" id="search" name="search" value="<?= $_GET['search'] ?? '' ?>" 
                       placeholder="Müşteri adı, e-posta..." 
                       class="w-full rounded-md border border-gray-300 py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Ödeme Tipi</label>
                <select id="payment_method" name="payment_method" class="w-full rounded-md border border-gray-300 py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tümü</option>
                    <option value="cash" <?= isset($_GET['payment_method']) && $_GET['payment_method'] === 'cash' ? 'selected' : '' ?>>Nakit</option>
                    <option value="credit_card" <?= isset($_GET['payment_method']) && $_GET['payment_method'] === 'credit_card' ? 'selected' : '' ?>>Kredi Kartı</option>
                    <option value="debit_card" <?= isset($_GET['payment_method']) && $_GET['payment_method'] === 'debit_card' ? 'selected' : '' ?>>Banka Kartı</option>
                    <option value="bank_transfer" <?= isset($_GET['payment_method']) && $_GET['payment_method'] === 'bank_transfer' ? 'selected' : '' ?>>Banka Havalesi</option>
                    <option value="other" <?= isset($_GET['payment_method']) && $_GET['payment_method'] === 'other' ? 'selected' : '' ?>>Diğer</option>
                </select>
            </div>
            
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Başlangıç Tarihi</label>
                <input type="date" id="date_from" name="date_from" value="<?= $_GET['date_from'] ?? '' ?>" 
                       class="w-full rounded-md border border-gray-300 py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Bitiş Tarihi</label>
                <input type="date" id="date_to" name="date_to" value="<?= $_GET['date_to'] ?? '' ?>" 
                       class="w-full rounded-md border border-gray-300 py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-md text-sm transition duration-300">
                    <i class="fas fa-search mr-1"></i> Filtrele
                </button>
            </div>
        </form>
    </div>

    <!-- Ödemeler Tablosu -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <?php if (count($payments) > 0): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Müşteri</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tutar</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ödeme Tipi</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($payments as $payment): ?>
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $payment['id'] ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($payment['user_name']) ?></div>
                                    <?php if (isset($payment['user_email'])): ?>
                                        <div class="text-sm text-gray-500"><?= htmlspecialchars($payment['user_email']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= formatDate($payment['payment_date']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">
                                    <?= formatMoney($payment['amount']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php 
                                    $paymentTypes = [
                                        'cash' => 'Nakit',
                                        'credit_card' => 'Kredi Kartı',
                                        'debit_card' => 'Banka Kartı',
                                        'bank_transfer' => 'Banka Havalesi',
                                        'other' => 'Diğer'
                                    ];
                                    echo $paymentTypes[$payment['payment_method']] ?? $payment['payment_method'];
                                    ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($payment['is_confirmed']): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i> Onaylandı
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock mr-1"></i> Beklemede
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="index.php?page=payments&action=show&id=<?= $payment['id'] ?>" class="text-blue-500 hover:text-blue-700 transition duration-300">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if (isAdmin()): ?>
                                            <a href="index.php?page=payments&action=edit&id=<?= $payment['id'] ?>" class="text-yellow-500 hover:text-yellow-700 transition duration-300">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="javascript:void(0)" onclick="confirmDelete(<?= $payment['id'] ?>)" class="text-red-500 hover:text-red-700 transition duration-300">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Sayfalama -->
            <?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
                <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                <span class="font-medium"><?= $pagination['page'] ?></span> / <span class="font-medium"><?= $pagination['total_pages'] ?></span>
                                (toplam <?= $pagination['total_records'] ?> kayıt)
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <?php 
                                $query_params = $_GET;
                                // İlk sayfa
                                if ($pagination['page'] > 1):
                                    $query_params['p'] = 1;
                                    $first_page_url = 'index.php?' . http_build_query($query_params);
                                ?>
                                    <a href="<?= $first_page_url ?>" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        <i class="fas fa-angle-double-left"></i>
                                    </a>
                                <?php endif; ?>
                                
                                <!-- Önceki sayfa -->
                                <?php 
                                if ($pagination['page'] > 1):
                                    $query_params['p'] = $pagination['page'] - 1;
                                    $prev_page_url = 'index.php?' . http_build_query($query_params);
                                ?>
                                    <a href="<?= $prev_page_url ?>" class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        <i class="fas fa-angle-left"></i>
                                    </a>
                                <?php endif; ?>
                                
                                <!-- Sayfa numaraları -->
                                <?php
                                $start_page = max(1, $pagination['page'] - 2);
                                $end_page = min($pagination['total_pages'], $pagination['page'] + 2);
                                
                                for ($i = $start_page; $i <= $end_page; $i++):
                                    $query_params['p'] = $i;
                                    $page_url = 'index.php?' . http_build_query($query_params);
                                    $active = $i === $pagination['page'];
                                ?>
                                    <a href="<?= $page_url ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium <?= $active ? 'bg-blue-50 text-blue-600 z-10' : 'text-gray-500 hover:bg-gray-50' ?>">
                                        <?= $i ?>
                                    </a>
                                <?php endfor; ?>
                                
                                <!-- Sonraki sayfa -->
                                <?php 
                                if ($pagination['page'] < $pagination['total_pages']):
                                    $query_params['p'] = $pagination['page'] + 1;
                                    $next_page_url = 'index.php?' . http_build_query($query_params);
                                ?>
                                    <a href="<?= $next_page_url ?>" class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        <i class="fas fa-angle-right"></i>
                                    </a>
                                <?php endif; ?>
                                
                                <!-- Son sayfa -->
                                <?php 
                                if ($pagination['page'] < $pagination['total_pages']):
                                    $query_params['p'] = $pagination['total_pages'];
                                    $last_page_url = 'index.php?' . http_build_query($query_params);
                                ?>
                                    <a href="<?= $last_page_url ?>" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        <i class="fas fa-angle-double-right"></i>
                                    </a>
                                <?php endif; ?>
                            </nav>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            <div class="p-8 text-center">
                <div class="inline-block p-4 rounded-full bg-blue-50 text-blue-500 mb-4">
                    <i class="fas fa-info-circle text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Kayıt Bulunamadı</h3>
                <p class="text-gray-500 mb-6">Belirtilen kriterlere uygun hiçbir ödeme kaydı bulunamadı.</p>
                <a href="index.php?page=payments" class="text-blue-500 hover:text-blue-700 transition duration-300">
                    <i class="fas fa-redo mr-1"></i> Filtreleri Temizle
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function confirmDelete(id) {
    if (confirm('Bu ödeme kaydını silmek istediğinizden emin misiniz?')) {
        window.location.href = `index.php?page=payments&action=delete&id=${id}`;
    }
}
</script>

<?php require_once 'views/partials/footer.php'; ?>