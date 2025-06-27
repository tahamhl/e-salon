<?php include_once 'views/partials/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <nav class="flex mb-6" aria-label="breadcrumb">
        <ol class="flex text-gray-600 text-sm">
            <li class="hover:text-blue-600"><a href="index.php">Ana Sayfa</a></li>
            <li class="mx-2">/</li>
            <li class="hover:text-blue-600"><a href="trainers.php">Eğitmenler</a></li>
            <li class="mx-2">/</li>
            <li class="text-gray-800" aria-current="page"><?php echo $trainer['name']; ?></li>
        </ol>
    </nav>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-1">
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                <?php if (!empty($trainer['profile_image'])): ?>
                <div class="h-64 overflow-hidden">
                    <img src="uploads/trainers/<?php echo $trainer['profile_image']; ?>" class="w-full h-full object-cover" alt="<?php echo $trainer['name']; ?>">
                </div>
                <?php else: ?>
                <div class="h-64 flex items-center justify-center bg-gray-100">
                    <i class="fas fa-user-circle text-7xl text-gray-400"></i>
                </div>
                <?php endif; ?>
                
                <div class="p-6">
                    <h4 class="text-xl font-semibold text-gray-800"><?php echo $trainer['name']; ?></h4>
                    <h6 class="text-sm text-gray-500 mt-1 mb-4"><?php echo $trainer['specialization']; ?></h6>
                    
                    <?php if (!empty($trainer['email'])): ?>
                    <p class="flex items-center mb-2 text-gray-600">
                        <i class="fas fa-envelope mr-3 text-blue-500"></i>
                        <a href="mailto:<?php echo $trainer['email']; ?>" class="hover:text-blue-600"><?php echo $trainer['email']; ?></a>
                    </p>
                    <?php endif; ?>
                    
                    <?php if (!empty($trainer['phone'])): ?>
                    <p class="flex items-center mb-2 text-gray-600">
                        <i class="fas fa-phone mr-3 text-blue-500"></i>
                        <?php echo $trainer['phone']; ?>
                    </p>
                    <?php endif; ?>
                    
                    <div class="mt-6">
                        <a href="contact.php?trainer_id=<?php echo $trainer['id']; ?>" class="w-full block text-center py-2 px-4 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-300">
                            <i class="fas fa-calendar-check mr-2"></i> Randevu Al
                        </a>
                    </div>
                </div>
            </div>
            
            <?php if (!empty($trainer['certifications'])): ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                <div class="bg-gray-50 px-6 py-3 border-b border-gray-200">
                    <h5 class="font-semibold text-gray-700 flex items-center">
                        <i class="fas fa-award text-yellow-500 mr-2"></i> Sertifikalar
                    </h5>
                </div>
                <div class="p-6">
                    <ul class="space-y-3">
                        <?php 
                        $certifications = explode("\n", $trainer['certifications']);
                        foreach ($certifications as $cert): 
                            if (trim($cert)):
                        ?>
                        <li class="flex items-start">
                            <i class="fas fa-certificate text-yellow-500 mt-1 mr-3"></i> 
                            <span class="text-gray-700"><?php echo trim($cert); ?></span>
                        </li>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </ul>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="md:col-span-2">
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                <div class="bg-gray-50 px-6 py-3 border-b border-gray-200">
                    <h5 class="font-semibold text-gray-700 flex items-center">
                        <i class="fas fa-user text-blue-500 mr-2"></i> Hakkında
                    </h5>
                </div>
                <div class="p-6">
                    <?php if (!empty($trainer['bio'])): ?>
                    <p class="text-gray-600"><?php echo nl2br($trainer['bio']); ?></p>
                    <?php else: ?>
                    <p class="text-gray-500 italic">Henüz biyografi bilgisi eklenmemiş.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                <div class="bg-gray-50 px-6 py-3 border-b border-gray-200">
                    <h5 class="font-semibold text-gray-700 flex items-center">
                        <i class="fas fa-dumbbell text-blue-500 mr-2"></i> Uzmanlık Alanları
                    </h5>
                </div>
                <div class="p-6">
                    <?php if (!empty($trainer['expertise'])): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php 
                        $expertise = explode("\n", $trainer['expertise']);
                        foreach ($expertise as $area): 
                            if (trim($area)):
                        ?>
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <span class="text-gray-700"><?php echo trim($area); ?></span>
                        </div>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                    <?php else: ?>
                    <p class="text-gray-500 italic">Henüz uzmanlık alanları eklenmemiş.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if (!empty($classes)): ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                <div class="bg-gray-50 px-6 py-3 border-b border-gray-200">
                    <h5 class="font-semibold text-gray-700 flex items-center">
                        <i class="fas fa-calendar-alt text-blue-500 mr-2"></i> Ders Programı
                    </h5>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gün</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Saat</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ders</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Salon</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($classes as $class): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo date('d.m.Y', strtotime($class['class_date'])); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        <?php 
                                        $days = ['Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi', 'Pazar'];
                                        $dayIndex = date('N', strtotime($class['class_date'])) - 1;
                                        echo $days[$dayIndex];
                                        ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo substr($class['start_time'], 0, 5); ?> - <?php echo substr($class['end_time'], 0, 5); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo ucfirst($class['class_type']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo $class['room_name']; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="text-center mt-6">
                        <a href="program.php" class="inline-flex items-center py-2 px-4 border border-blue-500 text-blue-600 rounded-md hover:bg-blue-50 transition duration-300">
                            <i class="fas fa-calendar-week mr-2"></i> Tüm Program
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gray-50 px-6 py-3 border-b border-gray-200">
                    <h5 class="font-semibold text-gray-700 flex items-center">
                        <i class="fas fa-info-circle text-blue-500 mr-2"></i> Özel Ders Bilgileri
                    </h5>
                </div>
                <div class="p-6">
                    <p class="text-gray-600">
                        <?php echo $trainer['name']; ?> ile özel ders alarak, kişisel fitness hedeflerinize daha hızlı ve etkili bir şekilde ulaşabilirsiniz.
                        Özel dersler tamamen sizin ihtiyaçlarınıza göre planlanır ve uygulanır.
                    </p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div class="border border-gray-200 rounded-md p-4 hover:shadow-md transition duration-300">
                            <div class="text-lg font-medium mb-1 text-gray-700">Tek Seans</div>
                            <div class="text-2xl font-bold text-gray-800 mb-2">₺200</div>
                            <p class="text-gray-600">60 dakikalık birebir eğitim seansı</p>
                        </div>
                        <div class="border border-gray-200 rounded-md p-4 hover:shadow-md transition duration-300">
                            <div class="text-lg font-medium mb-1 text-gray-700">5 Seans Paketi</div>
                            <div class="text-2xl font-bold text-gray-800 mb-2">₺900</div>
                            <p class="text-gray-600">5 adet 60 dakikalık birebir eğitim seansı</p>
                        </div>
                    </div>
                    
                    <div class="bg-blue-50 text-blue-700 p-4 rounded-md mt-6">
                        <div class="flex">
                            <i class="fas fa-info-circle mt-1 mr-3"></i>
                            <div>
                                Özel ders programı ve müsaitlik durumu için eğitmenimizle doğrudan iletişime geçebilir veya resepsiyondan bilgi alabilirsiniz.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 text-center">
                    <a href="contact.php?trainer_id=<?php echo $trainer['id']; ?>" class="inline-flex items-center py-2 px-4 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-300">
                        <i class="fas fa-envelope mr-2"></i> İletişime Geç
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'views/partials/footer.php'; ?> 