<?php include_once 'views/partials/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl md:text-3xl font-bold mb-6 text-gray-800">Eğitmenlerimiz</h1>
    
    <div class="mb-8">
        <p class="text-lg text-gray-600 max-w-3xl">
            Uzman eğitmenlerimiz, kişisel hedeflerinize ulaşmanız için size özel programlar hazırlayarak her adımda yanınızda olacaklar.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($trainers as $trainer): ?>
        <div class="bg-white rounded-lg shadow-md overflow-hidden transition-transform duration-300 hover:shadow-lg">
            <?php if (!empty($trainer['profile_image'])): ?>
            <div class="h-64 overflow-hidden">
                <img src="uploads/trainers/<?php echo $trainer['profile_image']; ?>" class="w-full h-full object-cover" alt="<?php echo $trainer['name']; ?>">
            </div>
            <?php else: ?>
            <div class="h-64 flex items-center justify-center bg-gray-100">
                <i class="fas fa-user-circle text-6xl text-gray-400"></i>
            </div>
            <?php endif; ?>
            
            <div class="p-6">
                <h5 class="text-xl font-semibold text-gray-800"><?php echo $trainer['name']; ?></h5>
                <h6 class="text-sm text-gray-500 mt-1"><?php echo $trainer['specialization']; ?></h6>
                
                <?php if (!empty($trainer['bio'])): ?>
                <p class="mt-4 text-gray-600"><?php echo substr($trainer['bio'], 0, 150) . '...'; ?></p>
                <?php endif; ?>
                
                <?php if (!empty($trainer['certifications'])): ?>
                <div class="mt-4">
                    <h6 class="font-medium text-gray-700">Sertifikalar</h6>
                    <ul class="mt-2 space-y-1">
                        <?php 
                        $certifications = explode("\n", $trainer['certifications']);
                        foreach ($certifications as $cert): 
                            if (trim($cert)):
                        ?>
                        <li class="flex items-start">
                            <i class="fas fa-award text-yellow-500 mt-1 mr-2"></i> 
                            <span class="text-gray-600"><?php echo trim($cert); ?></span>
                        </li>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="bg-gray-50 px-6 py-4">
                <a href="trainers.php?action=show&id=<?php echo $trainer['id']; ?>" class="w-full inline-block text-center py-2 px-4 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-300">
                    <i class="fas fa-info-circle mr-2"></i> Detayları Gör
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <div class="mt-12">
        <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <h5 class="text-lg font-semibold text-gray-800">Özel Dersler Hakkında</h5>
            </div>
            <div class="p-6">
                <p class="text-gray-600">
                    Eğitmenlerimizden biriyle özel ders alarak, fitness hedeflerinize daha hızlı ve etkili bir şekilde ulaşabilirsiniz. 
                    Özel dersler, kişisel ihtiyaçlarınıza ve hedeflerinize göre tamamen size özel olarak hazırlanır.
                </p>
                
                <h6 class="mt-6 mb-3 font-medium text-gray-800">Özel Ders Avantajları</h6>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <ul class="space-y-2">
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i> 
                                <span class="text-gray-600">Kişiye özel antrenman programı</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i> 
                                <span class="text-gray-600">Birebir ilgi ve düzeltmeler</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i> 
                                <span class="text-gray-600">Esnek programlama</span>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <ul class="space-y-2">
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i> 
                                <span class="text-gray-600">Hızlı sonuç alma</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i> 
                                <span class="text-gray-600">Motivasyon desteği</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i> 
                                <span class="text-gray-600">İleri seviye teknikler</span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="mt-6 bg-blue-50 text-blue-700 p-4 rounded-md">
                    <div class="flex">
                        <i class="fas fa-info-circle mt-1 mr-3"></i>
                        <div>
                            Özel ders fiyatları ve müsaitlik durumu için eğitmen profillerini inceleyebilir veya resepsiyondan bilgi alabilirsiniz.
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 text-center">
                <a href="contact.php" class="inline-block py-2 px-6 rounded-md border border-blue-500 text-blue-600 hover:bg-blue-50 transition duration-300">
                    <i class="fas fa-envelope mr-2"></i> Bilgi Almak İçin İletişime Geçin
                </a>
            </div>
        </div>
    </div>
</div>

<?php include_once 'views/partials/footer.php'; ?> 