<?php
// Aktif sayfayı belirle
$currentPage = basename($_SERVER['PHP_SELF']);
$activePage = str_replace('.php', '', $currentPage);

// Aktif sayfa sınıfı
function isActive($page) {
    global $activePage;
    return ($activePage == $page) ? 'active' : '';
}
?>

<nav class="bg-white shadow-md">
    <div class="container mx-auto px-4">
        <div class="flex justify-between h-16">
            <!-- Logo ve ana menü -->
            <div class="flex">
                <div class="flex-shrink-0 flex items-center">
                    <a href="index.php" class="text-blue-600 text-xl font-bold">E-Salon</a>
                </div>
                <div class="hidden md:ml-8 md:flex md:space-x-8">
                    <a href="index.php" class="inline-flex items-center h-16 px-1 pt-1 border-b-2 <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?>">
                        <i class="fas fa-home mr-2"></i> Anasayfa
                    </a>
                    
                    <?php if ($_SESSION['user_role'] === 'admin'): ?>
                        <a href="users.php" class="inline-flex items-center h-16 px-1 pt-1 border-b-2 <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?>">
                            <i class="fas fa-users mr-2"></i> Üyeler
                        </a>
                        <a href="packages.php" class="inline-flex items-center h-16 px-1 pt-1 border-b-2 <?php echo basename($_SERVER['PHP_SELF']) == 'packages.php' ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?>">
                            <i class="fas fa-box mr-2"></i> Paketler
                        </a>
                        <a href="trainers.php" class="inline-flex items-center h-16 px-1 pt-1 border-b-2 <?php echo basename($_SERVER['PHP_SELF']) == 'trainers.php' ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?>">
                            <i class="fas fa-user-tie mr-2"></i> Eğitmenler
                        </a>
                        <a href="reports.php" class="inline-flex items-center h-16 px-1 pt-1 border-b-2 <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?>">
                            <i class="fas fa-chart-bar mr-2"></i> Raporlar
                        </a>
                    <?php elseif ($_SESSION['user_role'] === 'staff'): ?>
                        <a href="check_in.php" class="inline-flex items-center h-16 px-1 pt-1 border-b-2 <?php echo basename($_SERVER['PHP_SELF']) == 'check_in.php' ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?>">
                            <i class="fas fa-clipboard-check mr-2"></i> Giriş İşlemleri
                        </a>
                        <a href="memberships.php" class="inline-flex items-center h-16 px-1 pt-1 border-b-2 <?php echo basename($_SERVER['PHP_SELF']) == 'memberships.php' ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?>">
                            <i class="fas fa-id-card mr-2"></i> Üyelikler
                        </a>
                        <a href="schedule.php" class="inline-flex items-center h-16 px-1 pt-1 border-b-2 <?php echo basename($_SERVER['PHP_SELF']) == 'schedule.php' ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?>">
                            <i class="fas fa-calendar-alt mr-2"></i> Program
                        </a>
                    <?php else: ?>
                        <a href="schedule.php" class="inline-flex items-center h-16 px-1 pt-1 border-b-2 <?php echo basename($_SERVER['PHP_SELF']) == 'schedule.php' ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?>">
                            <i class="fas fa-calendar-alt mr-2"></i> Program
                        </a>
                        <a href="packages.php" class="inline-flex items-center h-16 px-1 pt-1 border-b-2 <?php echo basename($_SERVER['PHP_SELF']) == 'packages.php' ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?>">
                            <i class="fas fa-box mr-2"></i> Paketler
                        </a>
                        <a href="trainers.php" class="inline-flex items-center h-16 px-1 pt-1 border-b-2 <?php echo basename($_SERVER['PHP_SELF']) == 'trainers.php' ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?>">
                            <i class="fas fa-user-tie mr-2"></i> Eğitmenler
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Profil menü -->
            <div class="hidden md:flex items-center">
                <div class="ml-3 relative">
                    <div>
                        <button type="button" id="user-menu-button" class="flex items-center max-w-xs rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" 
                            aria-expanded="false" aria-haspopup="true">
                            <span class="sr-only">Menüyü aç</span>
                            <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white">
                                <?php echo substr($_SESSION['user_name'], 0, 1); ?>
                            </div>
                            <span class="ml-2 text-gray-700"><?php echo $_SESSION['user_name']; ?></span>
                            <i class="fas fa-chevron-down text-gray-400 ml-1 text-xs"></i>
                        </button>
                    </div>
                    
                    <div id="user-menu" class="hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10" 
                        role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                        <a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                            <i class="fas fa-user mr-2"></i> Profilim
                        </a>
                        <?php if ($_SESSION['user_role'] === 'admin'): ?>
                            <a href="settings.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                <i class="fas fa-cog mr-2"></i> Sistem Ayarları
                            </a>
                        <?php endif; ?>
                        <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                            <i class="fas fa-sign-out-alt mr-2"></i> Çıkış Yap
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Mobil menü butonu -->
            <div class="flex items-center md:hidden">
                <button type="button" id="mobile-menu-button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500" aria-expanded="false">
                    <span class="sr-only">Menüyü aç</span>
                    <i class="fas fa-bars block h-6 w-6"></i>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Mobil menü -->
    <div class="hidden md:hidden" id="mobile-menu">
        <div class="pt-2 pb-3 space-y-1">
            <a href="index.php" class="block pl-3 pr-4 py-2 border-l-4 <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'border-blue-500 text-blue-700 bg-blue-50' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700'; ?>">
                <i class="fas fa-home mr-2"></i> Anasayfa
            </a>
            
            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <a href="users.php" class="block pl-3 pr-4 py-2 border-l-4 <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'border-blue-500 text-blue-700 bg-blue-50' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700'; ?>">
                    <i class="fas fa-users mr-2"></i> Üyeler
                </a>
                <a href="packages.php" class="block pl-3 pr-4 py-2 border-l-4 <?php echo basename($_SERVER['PHP_SELF']) == 'packages.php' ? 'border-blue-500 text-blue-700 bg-blue-50' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700'; ?>">
                    <i class="fas fa-box mr-2"></i> Paketler
                </a>
                <a href="trainers.php" class="block pl-3 pr-4 py-2 border-l-4 <?php echo basename($_SERVER['PHP_SELF']) == 'trainers.php' ? 'border-blue-500 text-blue-700 bg-blue-50' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700'; ?>">
                    <i class="fas fa-user-tie mr-2"></i> Eğitmenler
                </a>
                <a href="reports.php" class="block pl-3 pr-4 py-2 border-l-4 <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'border-blue-500 text-blue-700 bg-blue-50' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700'; ?>">
                    <i class="fas fa-chart-bar mr-2"></i> Raporlar
                </a>
            <?php elseif ($_SESSION['user_role'] === 'staff'): ?>
                <a href="check_in.php" class="block pl-3 pr-4 py-2 border-l-4 <?php echo basename($_SERVER['PHP_SELF']) == 'check_in.php' ? 'border-blue-500 text-blue-700 bg-blue-50' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700'; ?>">
                    <i class="fas fa-clipboard-check mr-2"></i> Giriş İşlemleri
                </a>
                <a href="memberships.php" class="block pl-3 pr-4 py-2 border-l-4 <?php echo basename($_SERVER['PHP_SELF']) == 'memberships.php' ? 'border-blue-500 text-blue-700 bg-blue-50' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700'; ?>">
                    <i class="fas fa-id-card mr-2"></i> Üyelikler
                </a>
                <a href="schedule.php" class="block pl-3 pr-4 py-2 border-l-4 <?php echo basename($_SERVER['PHP_SELF']) == 'schedule.php' ? 'border-blue-500 text-blue-700 bg-blue-50' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700'; ?>">
                    <i class="fas fa-calendar-alt mr-2"></i> Program
                </a>
            <?php else: ?>
                <a href="schedule.php" class="block pl-3 pr-4 py-2 border-l-4 <?php echo basename($_SERVER['PHP_SELF']) == 'schedule.php' ? 'border-blue-500 text-blue-700 bg-blue-50' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700'; ?>">
                    <i class="fas fa-calendar-alt mr-2"></i> Program
                </a>
                <a href="packages.php" class="block pl-3 pr-4 py-2 border-l-4 <?php echo basename($_SERVER['PHP_SELF']) == 'packages.php' ? 'border-blue-500 text-blue-700 bg-blue-50' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700'; ?>">
                    <i class="fas fa-box mr-2"></i> Paketler
                </a>
                <a href="trainers.php" class="block pl-3 pr-4 py-2 border-l-4 <?php echo basename($_SERVER['PHP_SELF']) == 'trainers.php' ? 'border-blue-500 text-blue-700 bg-blue-50' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700'; ?>">
                    <i class="fas fa-user-tie mr-2"></i> Eğitmenler
                </a>
            <?php endif; ?>
        </div>
        
        <!-- Mobil profil menüsü -->
        <div class="pt-4 pb-3 border-t border-gray-200">
            <div class="flex items-center px-4">
                <div class="flex-shrink-0">
                    <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white text-lg">
                        <?php echo isset($_SESSION['user_name']) ? substr($_SESSION['user_name'], 0, 1) : '?'; ?>
                    </div>
                </div>
                <div class="ml-3">
                    <div class="text-base font-medium text-gray-800"><?php echo $_SESSION['user_name'] ?? 'Kullanıcı'; ?></div>
                    <div class="text-sm font-medium text-gray-500"><?php echo $_SESSION['user_email'] ?? ''; ?></div>
                </div>
            </div>
            <div class="mt-3 space-y-1">
                <a href="profile.php" class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">
                    <i class="fas fa-user mr-2"></i> Profilim
                </a>
                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                    <a href="settings.php" class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">
                        <i class="fas fa-cog mr-2"></i> Sistem Ayarları
                    </a>
                <?php endif; ?>
                <a href="logout.php" class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">
                    <i class="fas fa-sign-out-alt mr-2"></i> Çıkış Yap
                </a>
            </div>
        </div>
    </div>
</nav>

<script>
    // Kullanıcı menüsü açma/kapatma
    const userMenuButton = document.getElementById('user-menu-button');
    const userMenu = document.getElementById('user-menu');
    
    if (userMenuButton && userMenu) {
        userMenuButton.addEventListener('click', () => {
            userMenu.classList.toggle('hidden');
        });
        
        // Menü dışına tıklama
        document.addEventListener('click', (e) => {
            if (!userMenuButton.contains(e.target) && !userMenu.contains(e.target)) {
                userMenu.classList.add('hidden');
            }
        });
    }
    
    // Mobil menü açma/kapatma
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    }
</script> 