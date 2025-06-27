<nav class="bg-white shadow">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center py-4">
            <a href="index.php" class="flex items-center">
                <i class="fas fa-dumbbell text-blue-600 text-2xl mr-2"></i>
                <span class="text-xl font-bold text-gray-800"><?php echo APP_NAME; ?></span>
            </a>
            
            <div class="hidden md:flex items-center space-x-6">
                <a href="index.php" class="text-gray-700 hover:text-blue-600 transition duration-300">Ana Sayfa</a>
                <a href="program.php" class="text-gray-700 hover:text-blue-600 transition duration-300">Program</a>
                <a href="packages.php" class="text-gray-700 hover:text-blue-600 transition duration-300">Paketler</a>
                <a href="trainers.php" class="text-gray-700 hover:text-blue-600 transition duration-300">Eğitmenler</a>
                <a href="about.php" class="text-gray-700 hover:text-blue-600 transition duration-300">Hakkımızda</a>
                <a href="contact.php" class="text-gray-700 hover:text-blue-600 transition duration-300">İletişim</a>
                
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="login.php" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition duration-300">Giriş Yap</a>
                <?php else: ?>
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center text-gray-700 hover:text-blue-600 focus:outline-none transition duration-300">
                            <span class="mr-2"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 py-2 w-48 bg-white rounded-md shadow-xl z-20">
                            <?php if ($_SESSION['user_role'] == 'admin'): ?>
                                <a href="dashboard.php?page=admin" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 transition duration-300">
                                    <i class="fas fa-tachometer-alt mr-2"></i> Yönetim Paneli
                                </a>
                            <?php elseif ($_SESSION['user_role'] == 'staff'): ?>
                                <a href="dashboard.php?page=staff" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 transition duration-300">
                                    <i class="fas fa-tachometer-alt mr-2"></i> Personel Paneli
                                </a>
                            <?php else: ?>
                                <a href="dashboard.php?page=member" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 transition duration-300">
                                    <i class="fas fa-tachometer-alt mr-2"></i> Üye Paneli
                                </a>
                            <?php endif; ?>
                            
                            <a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 transition duration-300">
                                <i class="fas fa-user mr-2"></i> Profil
                            </a>
                            
                            <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 transition duration-300">
                                <i class="fas fa-sign-out-alt mr-2"></i> Çıkış Yap
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <button class="md:hidden focus:outline-none focus:ring-2 focus:ring-blue-500 p-2 rounded-md" id="menuBtn">
                <i class="fas fa-bars text-gray-700 text-xl"></i>
            </button>
        </div>
        
        <!-- Mobil Menü -->
        <div class="md:hidden hidden transition-all duration-300 ease-in-out" id="mobileMenu">
            <div class="py-4 space-y-4">
                <a href="index.php" class="block py-2 text-gray-700 hover:text-blue-600 transition duration-300">Ana Sayfa</a>
                <a href="program.php" class="block py-2 text-gray-700 hover:text-blue-600 transition duration-300">Program</a>
                <a href="packages.php" class="block py-2 text-gray-700 hover:text-blue-600 transition duration-300">Paketler</a>
                <a href="trainers.php" class="block py-2 text-gray-700 hover:text-blue-600 transition duration-300">Eğitmenler</a>
                <a href="about.php" class="block py-2 text-gray-700 hover:text-blue-600 transition duration-300">Hakkımızda</a>
                <a href="contact.php" class="block py-2 text-gray-700 hover:text-blue-600 transition duration-300">İletişim</a>
                
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="login.php" class="block w-full text-center py-2 mt-2 rounded bg-blue-600 text-white hover:bg-blue-700 transition duration-300">Giriş Yap</a>
                <?php else: ?>
                    <div class="border-t border-gray-200 pt-4 mt-4">
                        <p class="text-gray-500 mb-2">Merhaba, <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
                        
                        <?php if ($_SESSION['user_role'] == 'admin'): ?>
                            <a href="dashboard.php?page=admin" class="block py-2 text-gray-700 hover:text-blue-600 transition duration-300">
                                <i class="fas fa-tachometer-alt mr-2"></i> Yönetim Paneli
                            </a>
                        <?php elseif ($_SESSION['user_role'] == 'staff'): ?>
                            <a href="dashboard.php?page=staff" class="block py-2 text-gray-700 hover:text-blue-600 transition duration-300">
                                <i class="fas fa-tachometer-alt mr-2"></i> Personel Paneli
                            </a>
                        <?php else: ?>
                            <a href="dashboard.php?page=member" class="block py-2 text-gray-700 hover:text-blue-600 transition duration-300">
                                <i class="fas fa-tachometer-alt mr-2"></i> Üye Paneli
                            </a>
                        <?php endif; ?>
                        
                        <a href="profile.php" class="block py-2 text-gray-700 hover:text-blue-600 transition duration-300">
                            <i class="fas fa-user mr-2"></i> Profil
                        </a>
                        
                        <a href="logout.php" class="block py-2 text-gray-700 hover:text-blue-600 transition duration-300">
                            <i class="fas fa-sign-out-alt mr-2"></i> Çıkış Yap
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Mobil menü açma kapama
        document.addEventListener('DOMContentLoaded', function() {
            const menuBtn = document.getElementById('menuBtn');
            const mobileMenu = document.getElementById('mobileMenu');
            
            menuBtn.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
                
                // Animasyon için sınıfları ekle/kaldır
                if (!mobileMenu.classList.contains('hidden')) {
                    setTimeout(() => {
                        mobileMenu.classList.add('opacity-100');
                        mobileMenu.classList.remove('opacity-0');
                    }, 10);
                } else {
                    mobileMenu.classList.add('opacity-0');
                    mobileMenu.classList.remove('opacity-100');
                }
            });
        });
    </script>
</nav> 