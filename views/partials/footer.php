    <footer class="bg-gray-900 py-12 text-white">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="mb-6 md:mb-0">
                    <a href="index.php" class="flex items-center mb-4">
                        <i class="fas fa-dumbbell text-blue-500 text-2xl mr-2"></i>
                        <span class="text-xl font-bold text-white"><?php echo APP_NAME; ?></span>
                    </a>
                    <p class="text-gray-400 mb-4">Spor salonumuza hoş geldiniz. Size özel programlar ve profesyonel eğitmenlerle hedeflerinize ulaşmanıza yardımcı oluyoruz.</p>
                    <div class="flex mt-4 space-x-4">
                        <a href="#" class="text-gray-400 hover:text-blue-500 transition duration-300">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-blue-500 transition duration-300">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-blue-500 transition duration-300">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-blue-500 transition duration-300">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
                <div class="mb-6 md:mb-0">
                    <h3 class="text-lg font-semibold mb-4 text-white">Hızlı Erişim</h3>
                    <ul class="space-y-2">
                        <li>
                            <a href="index.php" class="text-gray-400 hover:text-blue-500 transition duration-300">Ana Sayfa</a>
                        </li>
                        <li>
                            <a href="program.php" class="text-gray-400 hover:text-blue-500 transition duration-300">Program</a>
                        </li>
                        <li>
                            <a href="packages.php" class="text-gray-400 hover:text-blue-500 transition duration-300">Paketler</a>
                        </li>
                        <li>
                            <a href="trainers.php" class="text-gray-400 hover:text-blue-500 transition duration-300">Eğitmenler</a>
                        </li>
                        <li>
                            <a href="about.php" class="text-gray-400 hover:text-blue-500 transition duration-300">Hakkımızda</a>
                        </li>
                        <li>
                            <a href="contact.php" class="text-gray-400 hover:text-blue-500 transition duration-300">İletişim</a>
                        </li>
                    </ul>
                </div>
                <div class="mb-6 md:mb-0">
                    <h3 class="text-lg font-semibold mb-4 text-white">İletişim</h3>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-3 text-blue-500"></i>
                            <span class="text-gray-400">Örnek Mahallesi, 123. Cadde No:45, Ankara, Türkiye</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone-alt mr-3 text-blue-500"></i>
                            <span class="text-gray-400">+90 312 123 45 67</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-3 text-blue-500"></i>
                            <span class="text-gray-400">info@e-salon.com</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-clock mr-3 text-blue-500"></i>
                            <span class="text-gray-400">Pazartesi-Cumartesi: 08:00 - 22:00</span>
                        </li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4 text-white">Bültenimize Abone Olun</h3>
                    <p class="text-gray-400 mb-4">En son haberler, özel teklifler ve indirimler hakkında bilgi almak için abone olun.</p>
                    <form id="newsletterForm" class="flex flex-col space-y-3">
                        <input type="email" id="newsletterEmail" placeholder="E-posta adresiniz" required class="bg-gray-700 border border-gray-600 rounded py-2 px-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 rounded py-2 px-4 text-white font-medium transition duration-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                            Abone Ol
                        </button>
                    </form>
                    <p id="subscriptionMessage" class="mt-2 text-sm hidden"></p>
                </div>
            </div>
            <hr class="my-8 border-gray-700">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-400 text-sm mb-4 md:mb-0">
                    &copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. Tüm hakları saklıdır.
                </p>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-400 hover:text-blue-500 text-sm transition duration-300">Gizlilik Politikası</a>
                    <a href="#" class="text-gray-400 hover:text-blue-500 text-sm transition duration-300">Kullanım Şartları</a>
                </div>
            </div>
        </div>
        <button id="backToTop" class="fixed bottom-6 right-6 bg-blue-600 hover:bg-blue-700 text-white rounded-full p-3 shadow-lg hidden transition duration-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
            <i class="fas fa-arrow-up"></i>
        </button>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const backToTopButton = document.getElementById('backToTop');
            
            window.addEventListener('scroll', function() {
                if (window.pageYOffset > 300) {
                    backToTopButton.classList.remove('hidden');
                } else {
                    backToTopButton.classList.add('hidden');
                }
            });
            
            backToTopButton.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
            
            const newsletterForm = document.getElementById('newsletterForm');
            const subscriptionMessage = document.getElementById('subscriptionMessage');
            
            newsletterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const email = document.getElementById('newsletterEmail').value;
                
                setTimeout(function() {
                    subscriptionMessage.textContent = `Teşekkürler! ${email} adresi bültenimize başarıyla abone oldu.`;
                    subscriptionMessage.classList.remove('hidden', 'text-red-500');
                    subscriptionMessage.classList.add('text-green-500');
                    newsletterForm.reset();
                    
                    setTimeout(function() {
                        subscriptionMessage.classList.add('hidden');
                    }, 5000);
                }, 1000);
            });
        });
    </script>
</body>
</html> 