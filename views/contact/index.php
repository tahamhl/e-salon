<?php
// İletişim formu görünümü
?>

<div class="bg-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:text-center mb-12">
            <h2 class="text-base text-indigo-600 font-semibold tracking-wide uppercase">Bizimle İletişime Geçin</h2>
            <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                Sorularınız için buradayız
            </p>
            <p class="mt-4 max-w-2xl text-xl text-gray-500 lg:mx-auto">
                E-Salon ekibi olarak size yardımcı olmaktan mutluluk duyarız. Aşağıdaki formu doldurarak bize ulaşabilirsiniz.
            </p>
        </div>

        <div class="lg:grid lg:grid-cols-2 lg:gap-8">
            <!-- İletişim Formu -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">İletişim Formu</h3>
                
                <?php if (isset($_SESSION['flash_message'])): ?>
                    <div class="mb-4 p-4 rounded 
                        <?php
                        $type = $_SESSION['flash_type'] ?? 'info';
                        echo match($type) {
                            'success' => 'bg-green-100 text-green-800',
                            'error' => 'bg-red-100 text-red-800',
                            'warning' => 'bg-yellow-100 text-yellow-800',
                            default => 'bg-blue-100 text-blue-800'
                        };
                        ?>">
                        <?= $_SESSION['flash_message'] ?>
                    </div>
                    <?php
                    // Mesajı gösterdikten sonra sil
                    unset($_SESSION['flash_message']);
                    unset($_SESSION['flash_type']);
                    ?>
                <?php endif; ?>
                
                <form action="/contact.php" method="POST" class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">İsim Soyisim</label>
                        <input type="text" name="name" id="name" required 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">E-posta</label>
                        <input type="email" name="email" id="email" required 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Telefon</label>
                        <input type="tel" name="phone" id="phone" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700">Konu</label>
                        <input type="text" name="subject" id="subject" required 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700">Mesajınız</label>
                        <textarea name="message" id="message" rows="4" required 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>
                    
                    <div>
                        <button type="submit" 
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Gönder
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- İletişim Bilgileri -->
            <div class="mt-10 lg:mt-0">
                <div class="bg-white rounded-lg shadow-lg p-6 h-full">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">İletişim Bilgilerimiz</h3>
                    
                    <div class="space-y-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-lg font-medium text-gray-900">Telefon</p>
                                <p class="text-gray-600">+90 212 555 44 33</p>
                            </div>
                        </div>
                        
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-lg font-medium text-gray-900">E-posta</p>
                                <p class="text-gray-600">info@e-salon.com</p>
                            </div>
                        </div>
                        
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-lg font-medium text-gray-900">Adres</p>
                                <p class="text-gray-600">Merkez Mahallesi, Spor Caddesi No:42<br>Beşiktaş, İstanbul</p>
                            </div>
                        </div>
                        
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-lg font-medium text-gray-900">Çalışma Saatleri</p>
                                <p class="text-gray-600">Hafta içi: 08:00 - 22:00<br>Hafta sonu: 09:00 - 20:00</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-8">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Bizi Takip Edin</h4>
                        <div class="flex space-x-4">
                            <a href="#" class="text-gray-500 hover:text-indigo-600">
                                <span class="sr-only">Instagram</span>
                                <i class="fab fa-instagram text-2xl"></i>
                            </a>
                            <a href="#" class="text-gray-500 hover:text-indigo-600">
                                <span class="sr-only">Facebook</span>
                                <i class="fab fa-facebook text-2xl"></i>
                            </a>
                            <a href="#" class="text-gray-500 hover:text-indigo-600">
                                <span class="sr-only">Twitter</span>
                                <i class="fab fa-twitter text-2xl"></i>
                            </a>
                            <a href="#" class="text-gray-500 hover:text-indigo-600">
                                <span class="sr-only">YouTube</span>
                                <i class="fab fa-youtube text-2xl"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Harita -->
        <div class="mt-12">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Konum</h3>
                <div class="aspect-w-16 aspect-h-9">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d48173.50951182669!2d28.979589086725166!3d41.04128487888758!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14cab7a2a2c3b959%3A0x7671d1b9042a809!2zQmXFn2lrdGHFny_EsHN0YW5idWw!5e0!3m2!1str!2str!4v1620123456789!5m2!1str!2str" 
                        width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" class="rounded-md"></iframe>
                </div>
            </div>
        </div>
    </div>
</div> 