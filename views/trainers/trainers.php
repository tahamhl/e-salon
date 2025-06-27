<?php
// Üst bilgi dahil edilir
include_once 'views/partials/header.php';

// Veritabanı bağlantısı
$db = connectDB();

// Eğitmenleri getir
try {
    $query = "
        SELECT u.id, u.name, u.email, u.profile_image, 
               t.specialty, t.bio, t.experience_years, t.hourly_rate
        FROM users u
        JOIN trainers t ON u.id = t.user_id
        WHERE u.role = 'trainer' AND u.status = 'active'
        ORDER BY u.name ASC
    ";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    $trainers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $_SESSION['flash_message'] = 'Eğitmen listesi yüklenirken bir hata oluştu.';
    $_SESSION['flash_type'] = 'error';
    error_log('Eğitmenler sayfası hatası: ' . $e->getMessage());
    header('Location: index.php');
    exit;
}

// Hizmet türlerini getir (filtreleme için)
try {
    $query = "
        SELECT DISTINCT specialty 
        FROM trainers 
        WHERE specialty IS NOT NULL AND specialty != ''
        ORDER BY specialty ASC
    ";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    $specialties = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
} catch (PDOException $e) {
    $specialties = [];
    error_log('Uzmanlık alanları hatası: ' . $e->getMessage());
}
?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-center mb-8">Eğitmenlerimiz</h1>
    
    <!-- Filtreleme Bölümü -->
    <div class="bg-gray-100 p-4 rounded-lg mb-8">
        <h2 class="text-xl font-semibold mb-4">Filtrele</h2>
        <form id="filterForm" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="specialty" class="block text-sm font-medium text-gray-700 mb-1">Uzmanlık Alanı</label>
                <select id="specialty" name="specialty" class="w-full p-2 border border-gray-300 rounded">
                    <option value="">Tümü</option>
                    <?php foreach ($specialties as $specialty): ?>
                        <option value="<?= htmlspecialchars($specialty) ?>"><?= htmlspecialchars($specialty) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label for="experience" class="block text-sm font-medium text-gray-700 mb-1">Minimum Deneyim (Yıl)</label>
                <input type="number" id="experience" name="experience" min="0" max="50" class="w-full p-2 border border-gray-300 rounded">
            </div>
            
            <div>
                <label for="searchName" class="block text-sm font-medium text-gray-700 mb-1">İsme Göre Ara</label>
                <input type="text" id="searchName" name="searchName" class="w-full p-2 border border-gray-300 rounded">
            </div>
        </form>
    </div>
    
    <!-- Eğitmenler Listesi -->
    <div id="trainersContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if (empty($trainers)): ?>
            <div class="col-span-full text-center py-8">
                <p class="text-lg text-gray-600">Şu anda gösterilecek eğitmen bulunmamaktadır.</p>
            </div>
        <?php else: ?>
            <?php foreach ($trainers as $trainer): ?>
                <div class="trainer-card bg-white rounded-lg shadow-md overflow-hidden" 
                     data-specialty="<?= htmlspecialchars($trainer['specialty']) ?>" 
                     data-experience="<?= intval($trainer['experience_years']) ?>">
                    <div class="aspect-w-16 aspect-h-9 bg-gray-200">
                        <?php if (!empty($trainer['profile_image'])): ?>
                            <img src="<?= htmlspecialchars($trainer['profile_image']) ?>" 
                                 alt="<?= htmlspecialchars($trainer['name']) ?>" 
                                 class="w-full h-64 object-cover">
                        <?php else: ?>
                            <div class="flex items-center justify-center h-64 bg-gray-300">
                                <span class="text-4xl text-gray-500">
                                    <i class="fas fa-user-circle"></i>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="p-4">
                        <h3 class="text-xl font-bold mb-2"><?= htmlspecialchars($trainer['name']) ?></h3>
                        
                        <div class="flex items-center mb-3">
                            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                                <?= htmlspecialchars($trainer['specialty']) ?>
                            </span>
                            <span class="ml-2 text-sm text-gray-600">
                                <?= intval($trainer['experience_years']) ?> yıl deneyim
                            </span>
                        </div>
                        
                        <p class="text-gray-700 mb-4 line-clamp-3">
                            <?= htmlspecialchars($trainer['bio'] ?? 'Bilgi bulunmuyor.') ?>
                        </p>
                        
                        <div class="flex items-center justify-between mt-4">
                            <span class="text-lg font-bold text-green-600">
                                <?= number_format($trainer['hourly_rate'], 2) ?> ₺/saat
                            </span>
                            <a href="contact.php?trainer_id=<?= $trainer['id'] ?>" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 active:bg-blue-600 transition">
                                Randevu Al
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    const specialtySelect = document.getElementById('specialty');
    const experienceInput = document.getElementById('experience');
    const searchNameInput = document.getElementById('searchName');
    const trainersContainer = document.getElementById('trainersContainer');
    const trainerCards = document.querySelectorAll('.trainer-card');
    
    // Filtreleme fonksiyonu
    function filterTrainers() {
        const selectedSpecialty = specialtySelect.value.toLowerCase();
        const minExperience = parseInt(experienceInput.value) || 0;
        const searchName = searchNameInput.value.toLowerCase();
        
        let visibleCount = 0;
        
        trainerCards.forEach(card => {
            const trainerName = card.querySelector('h3').textContent.toLowerCase();
            const specialty = card.getAttribute('data-specialty').toLowerCase();
            const experience = parseInt(card.getAttribute('data-experience')) || 0;
            
            // Filtreleme kriterleri
            const matchesSpecialty = selectedSpecialty === '' || specialty.includes(selectedSpecialty);
            const matchesExperience = experience >= minExperience;
            const matchesName = searchName === '' || trainerName.includes(searchName);
            
            // Tüm kriterlere uyuyor mu?
            const isVisible = matchesSpecialty && matchesExperience && matchesName;
            
            card.style.display = isVisible ? 'block' : 'none';
            
            if (isVisible) {
                visibleCount++;
            }
        });
        
        // Eğer görünen eğitmen yoksa mesaj göster
        if (visibleCount === 0) {
            if (!document.getElementById('noResultsMessage')) {
                const noResultsMessage = document.createElement('div');
                noResultsMessage.id = 'noResultsMessage';
                noResultsMessage.className = 'col-span-full text-center py-8';
                noResultsMessage.innerHTML = '<p class="text-lg text-gray-600">Arama kriterlerine uygun eğitmen bulunamadı.</p>';
                trainersContainer.appendChild(noResultsMessage);
            }
        } else {
            const noResultsMessage = document.getElementById('noResultsMessage');
            if (noResultsMessage) {
                noResultsMessage.remove();
            }
        }
    }
    
    // Filtre değişikliklerini dinle
    specialtySelect.addEventListener('change', filterTrainers);
    experienceInput.addEventListener('input', filterTrainers);
    searchNameInput.addEventListener('input', filterTrainers);
});
</script>

<?php
// Alt bilgi dahil edilir
include_once 'views/partials/footer.php';
?> 