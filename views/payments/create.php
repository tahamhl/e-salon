<?php
// views/payments/create.php
// Başlık ve navigasyon dahil et
include_once 'views/partials/header.php';
include_once 'views/partials/navbar.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="flex items-center mb-6">
        <a href="payments.php" class="text-blue-500 hover:text-blue-700 mr-2 transition duration-300">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Yeni Ödeme Ekle</h1>
    </div>

    <?php if (isset($error)): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
            <p><?php echo $error; ?></p>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow-md overflow-hidden max-w-3xl mx-auto">
        <div class="p-6">
            <form action="payments.php" method="POST" class="space-y-6">
                <input type="hidden" name="action" value="store">

                <!-- Üye Seçimi -->
                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Üye Seçimi</label>
                    <div class="relative">
                        <select id="user_id" name="user_id" required 
                                class="w-full border border-gray-300 rounded-md py-2 px-3 appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Üye Seçiniz</option>
                            <?php if (isset($users) && is_array($users)): ?>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?php echo $user['id']; ?>" 
                                            <?php echo (isset($_POST['user_id']) && $_POST['user_id'] == $user['id']) ? 'selected' : ''; ?>>
                                        <?php echo $user['name'] . ' ' . $user['surname']; ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                            <i class="fas fa-chevron-down"></i>
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
                            <option value="cash" <?php echo (isset($_POST['payment_type']) && $_POST['payment_type'] == 'cash') ? 'selected' : ''; ?>>Nakit</option>
                            <option value="credit_card" <?php echo (isset($_POST['payment_type']) && $_POST['payment_type'] == 'credit_card') ? 'selected' : ''; ?>>Kredi Kartı</option>
                            <option value="bank_transfer" <?php echo (isset($_POST['payment_type']) && $_POST['payment_type'] == 'bank_transfer') ? 'selected' : ''; ?>>Banka Havalesi</option>
                            <option value="online" <?php echo (isset($_POST['payment_type']) && $_POST['payment_type'] == 'online') ? 'selected' : ''; ?>>Online Ödeme</option>
                        </select>
                    </div>

                    <!-- Ödeme Tarihi -->
                    <div>
                        <label for="payment_date" class="block text-sm font-medium text-gray-700 mb-1">Ödeme Tarihi</label>
                        <input type="datetime-local" id="payment_date" name="payment_date" required
                               value="<?php echo isset($_POST['payment_date']) ? $_POST['payment_date'] : date('Y-m-d\TH:i'); ?>"
                               class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Tutar -->
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Tutar (₺)</label>
                        <input type="number" id="amount" name="amount" step="0.01" required
                               value="<?php echo isset($_POST['amount']) ? $_POST['amount'] : ''; ?>"
                               class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Üyelik/Paket Seçimi -->
                    <div>
                        <label for="membership_id" class="block text-sm font-medium text-gray-700 mb-1">İlişkili Üyelik/Paket</label>
                        <select id="membership_id" name="membership_id"
                                class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Seçiniz (Opsiyonel)</option>
                            <!-- Üye seçildikten sonra Ajax ile yüklenecek -->
                        </select>
                    </div>
                </div>

                <!-- Onay Durumu -->
                <div class="mt-4">
                    <div class="flex items-center">
                        <input type="checkbox" id="is_confirmed" name="is_confirmed" value="1" 
                               <?php echo (isset($_POST['is_confirmed']) && $_POST['is_confirmed']) ? 'checked' : 'checked'; ?>
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
                              class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"><?php echo isset($_POST['notes']) ? htmlspecialchars($_POST['notes']) : ''; ?></textarea>
                </div>

                <!-- Düğmeler -->
                <div class="flex justify-end space-x-3 mt-6">
                    <a href="payments.php" 
                       class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md transition duration-300">
                        İptal
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md transition duration-300">
                        Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const userSelect = document.getElementById('user_id');
        userSelect.addEventListener('change', function() {
            const userId = this.value;
            if (userId) {
                loadMemberships(userId);
            } else {
                document.getElementById('membership_id').innerHTML = '<option value="">Seçiniz (Opsiyonel)</option>';
            }
        });
        
        // Sayfa yüklendiğinde üye seçili ise üyelikleri yükle
        if (userSelect.value) {
            loadMemberships(userSelect.value);
        }
    });
    
    function loadMemberships(userId) {
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
                            membershipSelect.appendChild(option);
                        });
                    } else {
                        // Eğer üyelik yoksa bilgilendirme mesajı göster
                        const option = document.createElement('option');
                        option.disabled = true;
                        option.textContent = 'Bu üyenin aktif üyeliği bulunmamaktadır';
                        membershipSelect.appendChild(option);
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