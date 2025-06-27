<?php
// views/payments/show.php
require_once 'views/partials/header.php';
require_once 'views/partials/navbar.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6 max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Ödeme Detayları</h1>
            <div class="flex space-x-2">
                <?php if (isAdmin()): ?>
                <a href="index.php?page=payments&action=edit&id=<?= $payment['id'] ?>" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-md text-sm transition duration-300">
                    <i class="fas fa-edit mr-1"></i> Düzenle
                </a>
                <button onclick="confirmDelete(<?= $payment['id'] ?>)" class="bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-md text-sm transition duration-300">
                    <i class="fas fa-trash-alt mr-1"></i> Sil
                </button>
                <?php endif; ?>
                <a href="index.php?page=payments" class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-md text-sm transition duration-300">
                    <i class="fas fa-arrow-left mr-1"></i> Geri
                </a>
            </div>
        </div>

        <?php if (isset($payment) && $payment): ?>
            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-500">Ödeme ID</p>
                            <p class="font-medium text-gray-800">#<?= $payment['id'] ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Müşteri</p>
                            <p class="font-medium text-gray-800"><?= $payment['user_name'] ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Ödeme Tarihi</p>
                            <p class="font-medium text-gray-800"><?= formatDate($payment['payment_date']) ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Ödeme Tipi</p>
                            <p class="font-medium text-gray-800">
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
                            </p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-500">Tutar</p>
                            <p class="font-bold text-lg text-green-600"><?= formatMoney($payment['amount']) ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Üyelik</p>
                            <p class="font-medium text-gray-800"><?= $payment['membership_name'] ?? 'Belirtilmemiş' ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Onay Durumu</p>
                            <?php if ($payment['is_confirmed']): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i> Onaylandı
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i> Beklemede
                                </span>
                            <?php endif; ?>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Oluşturulma Tarihi</p>
                            <p class="font-medium text-gray-800"><?= formatDate($payment['created_at']) ?></p>
                        </div>
                    </div>
                </div>

                <?php if (!empty($payment['notes'])): ?>
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-md font-medium text-gray-700 mb-2">Notlar</h3>
                        <p class="text-gray-600 whitespace-pre-line"><?= htmlspecialchars($payment['notes']) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">
                            Ödeme bulunamadı veya görüntüleme izniniz yok.
                        </p>
                    </div>
                </div>
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