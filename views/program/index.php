<?php include_once 'views/partials/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold mb-2 text-gray-800">Haftalık Program</h1>
            <p class="text-lg text-gray-600">
                <?php echo date('d.m.Y', strtotime($startOfWeek)); ?> - 
                <?php echo date('d.m.Y', strtotime($endOfWeek)); ?> haftası
            </p>
        </div>
        <div class="mt-4 md:mt-0">
            <div class="flex space-x-2">
                <a href="program.php?week=prev" class="inline-flex items-center py-2 px-4 bg-white border border-gray-300 rounded text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-chevron-left mr-2"></i> Önceki Hafta
                </a>
                <a href="program.php" class="inline-flex items-center py-2 px-4 bg-gray-100 border border-gray-300 rounded text-gray-700 hover:bg-gray-200">
                    Bu Hafta
                </a>
                <a href="program.php?week=next" class="inline-flex items-center py-2 px-4 bg-white border border-gray-300 rounded text-gray-700 hover:bg-gray-50">
                    Sonraki Hafta <i class="fas fa-chevron-right ml-2"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="overflow-auto mb-8">
        <div class="min-w-max">
            <table class="w-full border-collapse">
                <thead>
                    <tr>
                        <th class="border bg-gray-100 px-4 py-2 text-left text-sm font-medium text-gray-600">Gün</th>
                        <th class="border bg-gray-100 px-4 py-2 text-left text-sm font-medium text-gray-600">Tarih</th>
                        <th class="border bg-gray-100 px-4 py-2 text-center text-sm font-medium text-gray-600">09:00 - 10:00</th>
                        <th class="border bg-gray-100 px-4 py-2 text-center text-sm font-medium text-gray-600">10:00 - 11:00</th>
                        <th class="border bg-gray-100 px-4 py-2 text-center text-sm font-medium text-gray-600">11:00 - 12:00</th>
                        <th class="border bg-gray-100 px-4 py-2 text-center text-sm font-medium text-gray-600">12:00 - 13:00</th>
                        <th class="border bg-gray-100 px-4 py-2 text-center text-sm font-medium text-gray-600">14:00 - 15:00</th>
                        <th class="border bg-gray-100 px-4 py-2 text-center text-sm font-medium text-gray-600">15:00 - 16:00</th>
                        <th class="border bg-gray-100 px-4 py-2 text-center text-sm font-medium text-gray-600">16:00 - 17:00</th>
                        <th class="border bg-gray-100 px-4 py-2 text-center text-sm font-medium text-gray-600">17:00 - 18:00</th>
                        <th class="border bg-gray-100 px-4 py-2 text-center text-sm font-medium text-gray-600">18:00 - 19:00</th>
                        <th class="border bg-gray-100 px-4 py-2 text-center text-sm font-medium text-gray-600">19:00 - 20:00</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($weekProgram as $date => $dayData): ?>
                    <tr>
                        <td class="border px-4 py-2 font-medium <?php echo (date('Y-m-d') == $date) ? 'bg-blue-50' : ''; ?>">
                            <?php echo $dayData['day_name']; ?>
                        </td>
                        <td class="border px-4 py-2 <?php echo (date('Y-m-d') == $date) ? 'bg-blue-50' : ''; ?>">
                            <?php echo date('d.m.Y', strtotime($date)); ?>
                        </td>
                        
                        <?php 
                        $timeSlots = [
                            '09:00:00', '10:00:00', '11:00:00', '12:00:00', 
                            '14:00:00', '15:00:00', '16:00:00', '17:00:00', 
                            '18:00:00', '19:00:00'
                        ];
                        
                        foreach ($timeSlots as $timeSlot): 
                            $hasClass = false;
                            foreach ($dayData['classes'] as $class) {
                                if ($class['start_time'] == $timeSlot) {
                                    $hasClass = true;
                                    $bgClass = '';
                                    $textClass = '';
                                    
                                    // Ders tipine göre renk sınıfı
                                    switch ($class['class_type']) {
                                        case 'yoga':
                                            $bgClass = 'bg-green-600 text-white';
                                            break;
                                        case 'pilates':
                                            $bgClass = 'bg-blue-600 text-white';
                                            break;
                                        case 'fitness':
                                            $bgClass = 'bg-red-600 text-white';
                                            break;
                                        case 'zumba':
                                            $bgClass = 'bg-yellow-500 text-gray-800';
                                            break;
                                        default:
                                            $bgClass = 'bg-blue-400 text-white';
                                            break;
                                    }
                                    
                                    echo '<td class="border p-0">';
                                    echo '<div class="' . $bgClass . ' p-2 h-full">';
                                    echo '<div class="font-bold">' . ucfirst($class['class_type']) . '</div>';
                                    echo '<div class="text-sm">' . $class['trainer_name'] . '</div>';
                                    echo '<div class="text-xs opacity-80">' . $class['room_name'] . '</div>';
                                    echo '</div>';
                                    echo '</td>';
                                }
                            }
                            
                            if (!$hasClass) {
                                echo '<td class="border"></td>';
                            }
                        endforeach; 
                        ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="bg-white shadow rounded-lg overflow-hidden mb-8">
        <div class="bg-blue-600 text-white px-6 py-3">
            <h2 class="text-lg font-semibold">Ders Tipleri</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-green-600 text-white rounded-md p-3 flex items-center">
                    <div class="mr-2 text-lg"><i class="fas fa-spa"></i></div>
                    <div>
                        <strong>Yoga</strong> 
                        <div class="text-xs opacity-90">Beden ve zihin uyumu için</div>
                    </div>
                </div>
                <div class="bg-blue-600 text-white rounded-md p-3 flex items-center">
                    <div class="mr-2 text-lg"><i class="fas fa-balance-scale"></i></div>
                    <div>
                        <strong>Pilates</strong>
                        <div class="text-xs opacity-90">Core kuvvetlendirme</div>
                    </div>
                </div>
                <div class="bg-red-600 text-white rounded-md p-3 flex items-center">
                    <div class="mr-2 text-lg"><i class="fas fa-dumbbell"></i></div>
                    <div>
                        <strong>Fitness</strong>
                        <div class="text-xs opacity-90">Genel kondisyon</div>
                    </div>
                </div>
                <div class="bg-yellow-500 text-gray-800 rounded-md p-3 flex items-center">
                    <div class="mr-2 text-lg"><i class="fas fa-music"></i></div>
                    <div>
                        <strong>Zumba</strong>
                        <div class="text-xs opacity-90">Dans ve aerobik</div>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 bg-gray-100 p-4 rounded-md">
                <div class="flex items-start">
                    <div class="mr-3 text-blue-600"><i class="fas fa-info-circle text-xl"></i></div>
                    <div>
                        <h3 class="font-medium">Bilgi</h3>
                        <p class="text-sm text-gray-600 mt-1">
                            Derslere katılmak için sağlık durumunuza uygunluk kontrolü yapmanızı öneririz. 
                            Ders rezervasyonlarınızı en geç 2 saat öncesinden iptal edebilirsiniz.
                            Detaylı bilgi için resepsiyonumuzla iletişime geçebilirsiniz.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'views/partials/footer.php'; ?> 