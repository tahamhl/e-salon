<?php
// views/payments/edit.php
include_once 'views/partials/header.php';
include_once 'views/partials/navbar.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="flex items-center mb-6">
        <a href="payments.php" class="text-blue-500 hover:text-blue-700 mr-2 transition duration-300">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Ödeme Düzenle</h1>
    </div>

    <?php if (isset($error)): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
            <p><?php echo $error; ?></p>
        </div>
    <?php endif; ?>

    <?php if ($payment): ?>
        <div class="bg-white rounded-lg shadow-md overflow-hidden max-w-3xl mx-auto">
            <div class="p-6">
                <form action="payments.php" method="POST" class="space-y-6">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?php echo $payment['id']; ?>">

                    <!-- Üye Bilgileri (salt okunur) -->
                    <div class="rounded-lg bg-gray-50 p-4 mb-6">
                        <h3 class="font-semibold text-gray-700 mb-3 border-b pb-2">Üye Bilgileri</h3>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <i class="fas fa-user text-gray-500 w-6"></i>
                                <a href="users.php?action=show&id=<?php echo $payment['user_id']; ?>" 
                                   class="ml-2 text-blue-500 hover:text-blue-700 transition duration-300">
                                    <?php echo $payment['user_name'] . ' ' . $payment['user_surname']; ?>
                                </a>
                                <input type="hidden" name="user_id" value="<?php echo $payment['user_id']; ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Ödeme Detayları -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Ödeme Tipi -->
                        <div>
                            <label for="payment_type" class="block text-sm font-medium text-gray-700 mb-1">Ödeme Yöntemi</label>
                            <select id="payment_type" name="payment_type" required 
                                    class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="cash" <?php echo $payment['payment_type'] == 'cash' ? 'selected' : ''; ?>>Nakit</option>
                                <option value="credit_card" <?php echo $payment['payment_type'] == 'credit_card' ? 'selected' : ''; ?>>Kredi Kartı</option>
                                <option value="bank_transfer" <?php echo $payment['payment_type'] == 'bank_transfer' ? 'selected' : ''; ?>>Banka Havalesi</option>
                                <option value="online" <?php echo $payment['payment_type'] == 'online' ? 'selected' : ''; ?>>Online Ödeme</option>
                            </select>
                        </div>

                        <!-- Ödeme Tarihi -->
                        <div>
                            <label for="payment_date" class="block text-sm font-medium text-gray-700 mb-1">Ödeme Tarihi</label>
                            <input type="datetime-local" id="payment_date" name="payment_date" required
                                   value="<?php echo date('Y-m-d\TH:i', strtotime($payment['payment_date'])); ?>"
                                   class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Tutar -->
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Tutar (₺)</label>
                            <input type="number" id="amount" name="amount" step="0.01" required
                                   value="<?php echo $payment['amount']; ?>"
                                   class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Üyelik/Paket Seçimi -->
                        <div>
                            <label for="membership_id" class="block text-sm font-medium text-gray-700 mb-1">İlişkili Üyelik/Paket</label>
                            <select id="membership_id" name="membership_id"
                                    class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Seçiniz (Opsiyonel)</option>
                                <?php if (isset($memberships) && is_array($memberships)): ?>
                                    <?php foreach ($memberships as $membership): ?>
                                        <option value="<?php echo $membership['id']; ?>" 
                                                <?php echo $payment['membership_id'] == $membership['id'] ? 'selected' : ''; ?>>
                                            <?php echo $membership['package_name']; ?> 
                                            (<?php echo date('d.m.Y', strtotime($membership['start_date'])); ?> - 
                                            <?php echo date('d.m.Y', strtotime($membership['end_date'])); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Onay Durumu -->
                    <div class="mt-4">
                        <div class="flex items-center">
                            <input type="checkbox" id="is_confirmed" name="is_confirmed" value="1" 
                                   <?php echo $payment['is_confirmed'] ? 'checked' : ''; ?>
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="is_confirmed" class="ml-2 block text-sm text-gray-900">
                                Ödeme onaylandı
                            </label>
                        </div>
                    </div>

                    <!-- Notlar -->
                    <div class="mt-4">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notlar</label>
                        <textarea id="notes" name="notes" rows="3"
                                  class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"><?php echo htmlspecialchars($payment['notes']); ?></textarea>
                    </div>

                    <!-- Düğmeler -->
                    <div class="flex justify-end space-x-3 mt-6">
                        <a href="payments.php?action=show&id=<?php echo $payment['id']; ?>" 
                           class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md transition duration-300">
                            İptal
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md transition duration-300">
                            Güncelle
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php else: ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 max-w-3xl mx-auto">
            <p>Ödeme bulunamadı veya düzenleme izniniz yok.</p>
            <a href="payments.php" class="mt-2 inline-block text-blue-500 hover:text-blue-700 transition duration-300">
                Ödemeler listesine dön
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
    // Gerekirse AJAX ile memberships güncelleme
    document.addEventListener('DOMContentLoaded', function() {
        const userId = <?php echo json_encode($payment['user_id']); ?>;
        const currentMembershipId = <?php echo json_encode($payment['membership_id']); ?>;
        
        // Sayfa yüklendiğinde üye için mevcut üyelikleri otomatik olarak yükleme
        if (userId) {
            loadMemberships(userId, currentMembershipId);
        }
    });
    
    function loadMemberships(userId, selectedId) {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', `payments.php?action=getUserMemberships&user_id=${userId}`, true);
        
        xhr.onload = function() {
            if (this.status === 200) {
                try {
                    const membershipSelect = document.getElementById('membership_id');
                    membershipSelect.innerHTML = '<option value="">Seçiniz (Opsiyonel)</option>';
                    
                    const response = JSON.parse(this.responseText);
                    
                    if (response.success && response.data.length > 0) {
                        response.data.forEach(function(membership) {
                            const option = document.createElement('option');
                            option.value = membership.id;
                            option.textContent = `${membership.package_name} (${formatDate(membership.start_date)} - ${formatDate(membership.end_date)})`;
                            
                            if (selectedId && membership.id == selectedId) {
                                option.selected = true;
                            }
                            
                            membershipSelect.appendChild(option);
                        });
                    }
                } catch (e) {
                    console.error('JSON parsing error:', e);
                }
            }
        };
        
        xhr.send();
    }
    
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('tr-TR');
    }
</script>

<?php include_once 'views/partials/footer.php'; ?> 