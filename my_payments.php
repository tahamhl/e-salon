<?php
/**
 * Ödemelerim Sayfası
 * Bu sayfa kullanıcının ödeme geçmişini görüntüler
 */
require_once 'config/init.php';

// Oturum kontrolü
if (!isset($_SESSION['user_id'])) {
    setError('Bu sayfayı görüntülemek için giriş yapmalısınız.');
    redirect('login.php');
    exit;
}

// PaymentController sınıfını yükle
require_once 'controllers/PaymentController.php';
$paymentController = new PaymentController();

// Kullanıcının ödemelerini al
$payments = $paymentController->getUserPayments($_SESSION['user_id']);

// Filtre değerlerini al
$dateFrom = isset($_GET['date_from']) ? sanitize($_GET['date_from']) : '';
$dateTo = isset($_GET['date_to']) ? sanitize($_GET['date_to']) : '';
$paymentType = isset($_GET['payment_type']) ? sanitize($_GET['payment_type']) : '';

// Filtreleme yapılmışsa
if (!empty($dateFrom) || !empty($dateTo) || !empty($paymentType)) {
    $payments = $paymentController->filterUserPayments($_SESSION['user_id'], $dateFrom, $dateTo, $paymentType);
}

// Sayfa başlığı
$pageTitle = "Ödemelerim";

// Üst kısmı dahil et
include_once 'views/partials/header.php';
include_once 'views/partials/navbar.php';
?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Ödeme Geçmişim</h1>
    
    <!-- Filtre Formu -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Ödeme Filtreleri</h2>
        
        <form action="my_payments.php" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Başlangıç Tarihi</label>
                    <input type="date" id="date_from" name="date_from" value="<?php echo $dateFrom; ?>" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                </div>
                
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Bitiş Tarihi</label>
                    <input type="date" id="date_to" name="date_to" value="<?php echo $dateTo; ?>" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                </div>
                
                <div>
                    <label for="payment_type" class="block text-sm font-medium text-gray-700 mb-1">Ödeme Tipi</label>
                    <select id="payment_type" name="payment_type" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        <option value="">Tümü</option>
                        <option value="cash" <?php echo $paymentType === 'cash' ? 'selected' : ''; ?>>Nakit</option>
                        <option value="credit_card" <?php echo $paymentType === 'credit_card' ? 'selected' : ''; ?>>Kredi Kartı</option>
                        <option value="bank_transfer" <?php echo $paymentType === 'bank_transfer' ? 'selected' : ''; ?>>Banka Transferi</option>
                        <option value="online" <?php echo $paymentType === 'online' ? 'selected' : ''; ?>>Online Ödeme</option>
                    </select>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3">
                <a href="my_payments.php" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Filtreleri Temizle
                </a>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Filtrele
                </button>
            </div>
        </form>
    </div>
    
    <!-- Ödeme Listesi -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <?php if (!empty($payments)): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tutar</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ödeme Türü</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Üyelik</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detay</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?php echo date('d.m.Y', strtotime($payment['payment_date'])); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?php echo formatMoney($payment['amount']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <?php
                                        switch ($payment['payment_method']) {
                                            case 'cash':
                                                echo '<span class="inline-flex items-center"><i class="fas fa-money-bill-wave text-green-500 mr-1"></i> Nakit</span>';
                                                break;
                                            case 'credit_card':
                                                echo '<span class="inline-flex items-center"><i class="fas fa-credit-card text-blue-500 mr-1"></i> Kredi Kartı</span>';
                                                break;
                                            case 'bank_transfer':
                                                echo '<span class="inline-flex items-center"><i class="fas fa-university text-purple-500 mr-1"></i> Banka Transferi</span>';
                                                break;
                                            case 'online':
                                                echo '<span class="inline-flex items-center"><i class="fas fa-globe text-indigo-500 mr-1"></i> Online Ödeme</span>';
                                                break;
                                            default:
                                                echo htmlspecialchars($payment['payment_method']);
                                        }
                                        ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <?php echo !empty($payment['membership_id']) ? 
                                            '<a href="memberships.php?action=show&id=' . $payment['membership_id'] . '" class="text-blue-600 hover:text-blue-900">' . 
                                            'Üyelik #' . $payment['membership_id'] . '</a>' : 
                                            '<span class="text-gray-500">-</span>'; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($payment['confirmed']): ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Onaylandı
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Beklemede
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="payments.php?action=show&id=<?php echo $payment['id']; ?>" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye mr-1"></i> Görüntüle
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="p-8 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 text-gray-400 mb-4">
                    <i class="fas fa-receipt text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Ödeme kaydı bulunamadı</h3>
                <p class="text-gray-500 max-w-md mx-auto">
                    <?php if (!empty($dateFrom) || !empty($dateTo) || !empty($paymentType)): ?>
                        Seçtiğiniz filtre kriterlerine uyan ödeme kaydı bulunamadı. Lütfen farklı bir tarih aralığı veya ödeme türü seçin.
                    <?php else: ?>
                        Henüz kayıtlı bir ödeme işleminiz bulunmuyor.
                    <?php endif; ?>
                </p>
                <div class="mt-6">
                    <a href="packages.php" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-shopping-cart mr-2"></i> Paketleri İncele
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Bilgilendirme Kartı -->
    <div class="mt-8 bg-white rounded-lg shadow-md overflow-hidden border-t-4 border-blue-500">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-2">Ödeme Bilgilendirmesi</h2>
            <p class="text-gray-600 mb-4">
                Tüm ödemeleriniz bu sayfada listelenecektir. Ödeme ile ilgili herhangi bir sorun yaşarsanız, lütfen resepsiyona başvurun veya aşağıdaki iletişim bilgilerinden bize ulaşın.
            </p>
            <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-6">
                <div class="flex items-center text-sm">
                    <i class="fas fa-phone-alt text-blue-500 mr-2"></i>
                    <span>+90 (212) 555 7890</span>
                </div>
                <div class="flex items-center text-sm">
                    <i class="fas fa-envelope text-blue-500 mr-2"></i>
                    <span>finans@e-salon.com</span>
                </div>
                <div class="flex items-center text-sm">
                    <i class="fas fa-clock text-blue-500 mr-2"></i>
                    <span>Pazartesi-Cuma: 09:00 - 18:00</span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'views/partials/footer.php'; ?> 