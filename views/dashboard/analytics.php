<?php
// Üst kısmı dahil et
include_once 'views/partials/header.php';
include_once 'views/partials/navbar.php';

// Yetki kontrolü
if (!isAdmin() && !isStaff()) {
    setError('Bu sayfaya erişim izniniz yok.');
    redirect('index.php');
}

// MembershipController'ı yükle
require_once 'controllers/MembershipController.php';
$membershipController = new MembershipController();

// Aktif üyelikler
$activeMemberships = $membershipController->getActiveMemberships();

// Son 6 aya ait üyelik verileri
$monthlyData = [];
$labels = [];

for ($i = 5; $i >= 0; $i--) {
    $month = date('m', strtotime("-$i months"));
    $year = date('Y', strtotime("-$i months"));
    $labels[] = date('M Y', strtotime("$year-$month-01"));
    
    // Bu ayda aktif olan üyelikler
    $stmt = $GLOBALS['db']->prepare("SELECT COUNT(*) FROM memberships 
                               WHERE status = 'active' 
                                 AND (
                                     (YEAR(start_date) < :year OR (YEAR(start_date) = :year AND MONTH(start_date) <= :month))
                                     AND 
                                     (YEAR(end_date) > :year OR (YEAR(end_date) = :year AND MONTH(end_date) >= :month))
                                 )");
    $stmt->execute([':month' => $month, ':year' => $year]);
    $monthlyData[] = $stmt->fetchColumn();
}

// Son 30 gün içinde yeni eklenen üyelikler
$stmt = $GLOBALS['db']->query("SELECT COUNT(*) FROM memberships WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
$newMemberships = $stmt->fetchColumn();

// Son 30 gün içinde sona eren üyelikler
$stmt = $GLOBALS['db']->query("SELECT COUNT(*) FROM memberships WHERE end_date BETWEEN DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND CURDATE()");
$expiredMemberships = $stmt->fetchColumn();

// Paketlere göre üyelik dağılımı
$stmt = $GLOBALS['db']->query("SELECT p.name, COUNT(m.id) as count
                            FROM memberships m
                            JOIN packages p ON m.package_id = p.id
                            WHERE m.status = 'active' AND m.end_date >= CURDATE()
                            GROUP BY p.id
                            ORDER BY count DESC");
$packageData = $stmt->fetchAll();
?>

<div class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-semibold text-gray-800 mb-6">Üyelik Analizleri</h1>
        
        <!-- İstatistik kartları -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Toplam aktif üyelik -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Aktif Üyelikler</p>
                        <h2 class="text-3xl font-bold text-gray-800"><?php echo $activeMemberships; ?></h2>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                </div>
                
                <?php if ($activeMemberships > 0): ?>
                <div class="mt-4 flex items-center">
                    <span class="text-green-500 flex items-center text-sm font-medium">
                        <i class="fas fa-arrow-up mr-1"></i> <?php echo round(($newMemberships / $activeMemberships) * 100, 1); ?>%
                    </span>
                    <span class="text-xs text-gray-500 ml-2">son 30 günde</span>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Yeni üyelikler -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Yeni Üyelikler</p>
                        <h2 class="text-3xl font-bold text-gray-800"><?php echo $newMemberships; ?></h2>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                        <i class="fas fa-user-plus text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 text-xs text-gray-500">Son 30 günde</div>
            </div>
            
            <!-- Sona eren üyelikler -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Sona Eren Üyelikler</p>
                        <h2 class="text-3xl font-bold text-gray-800"><?php echo $expiredMemberships; ?></h2>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                        <i class="fas fa-user-minus text-red-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 text-xs text-gray-500">Son 30 günde</div>
            </div>
        </div>
        
        <!-- Grafikler -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Aylık üyelik sayısı grafiği -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="font-semibold text-gray-700 mb-4">Aylık Üyelik Sayısı</h3>
                <div class="h-80">
                    <canvas id="membershipChart"></canvas>
                </div>
            </div>
            
            <!-- Paket dağılımı grafiği -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="font-semibold text-gray-700 mb-4">Paket Dağılımı</h3>
                <div class="h-80">
                    <canvas id="packageChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Üyelik dağılımı tablosu -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="font-semibold text-gray-700 mb-4">Üyelik Paketi Dağılımı</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paket</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aktif Üyelik</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Yüzde</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($packageData as $package): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <?php echo $package['name']; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo $package['count']; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo round(($package['count'] / $activeMemberships) * 100, 1); ?>%
                                <div class="w-full bg-gray-200 rounded-full h-2.5 mt-2">
                                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: <?php echo ($package['count'] / $activeMemberships) * 100; ?>%"></div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Üyelik grafiği
const ctxMembership = document.getElementById('membershipChart').getContext('2d');
new Chart(ctxMembership, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($labels); ?>,
        datasets: [{
            label: 'Aktif Üyelikler',
            data: <?php echo json_encode($monthlyData); ?>,
            backgroundColor: 'rgba(59, 130, 246, 0.2)',
            borderColor: 'rgba(59, 130, 246, 1)',
            borderWidth: 2,
            tension: 0.3
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

// Paket dağılımı grafiği
const ctxPackage = document.getElementById('packageChart').getContext('2d');
new Chart(ctxPackage, {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode(array_column($packageData, 'name')); ?>,
        datasets: [{
            data: <?php echo json_encode(array_column($packageData, 'count')); ?>,
            backgroundColor: [
                'rgba(59, 130, 246, 0.8)', // blue
                'rgba(16, 185, 129, 0.8)', // green
                'rgba(245, 158, 11, 0.8)', // yellow
                'rgba(239, 68, 68, 0.8)',  // red
                'rgba(139, 92, 246, 0.8)',  // purple
                'rgba(236, 72, 153, 0.8)'  // pink
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'right'
            }
        }
    }
});
</script>

<?php include_once 'views/partials/footer.php'; ?> 