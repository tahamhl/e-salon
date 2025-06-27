-- E-Salon Veritabanı Tabloları

-- Kullanıcı tablosu
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    name VARCHAR(100) GENERATED ALWAYS AS (CONCAT(first_name, ' ', last_name)) STORED,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'staff', 'member', 'trainer') NOT NULL DEFAULT 'member',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    profile_image VARCHAR(255),
    specialization VARCHAR(100),
    bio TEXT,
    certifications TEXT,
    expertise TEXT,
    created_at DATETIME NOT NULL,
    updated_at DATETIME
);

-- Paketler tablosu
CREATE TABLE IF NOT EXISTS packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    duration INT NOT NULL,
    duration_unit ENUM('day', 'month', 'year') NOT NULL DEFAULT 'month',
    session_limit INT,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    is_highlighted TINYINT(1) NOT NULL DEFAULT 0,
    features TEXT,
    display_order INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME
);

-- Üyelikler tablosu
CREATE TABLE IF NOT EXISTS memberships (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    package_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('active', 'suspended', 'expired', 'cancelled') NOT NULL DEFAULT 'active',
    discount_amount DECIMAL(10, 2) DEFAULT 0,
    remaining_sessions INT,
    created_by INT NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (package_id) REFERENCES packages(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Ödemeler tablosu
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    membership_id INT,
    amount DECIMAL(10, 2) NOT NULL,
    payment_type ENUM('cash', 'credit_card', 'bank_transfer', 'online') NOT NULL,
    payment_date DATETIME NOT NULL,
    description TEXT,
    discount_amount DECIMAL(10, 2) DEFAULT 0,
    created_by INT NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (membership_id) REFERENCES memberships(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Salonlar tablosu
CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    capacity INT NOT NULL,
    description TEXT,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME
);

-- Dersler tablosu
CREATE TABLE IF NOT EXISTS classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_name VARCHAR(100) NOT NULL,
    class_type ENUM('yoga', 'pilates', 'fitness', 'zumba', 'other') NOT NULL,
    trainer_id INT,
    room_id INT NOT NULL,
    capacity INT NOT NULL,
    description TEXT,
    class_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME,
    FOREIGN KEY (trainer_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (room_id) REFERENCES rooms(id)
);

-- Ders Rezervasyonları tablosu
CREATE TABLE IF NOT EXISTS class_bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_id INT NOT NULL,
    user_id INT NOT NULL,
    status ENUM('booked', 'attended', 'cancelled', 'no_show') NOT NULL DEFAULT 'booked',
    created_at DATETIME NOT NULL,
    updated_at DATETIME,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Ziyaretler tablosu
CREATE TABLE IF NOT EXISTS visits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    check_in_time DATETIME NOT NULL,
    check_out_time DATETIME,
    duration INT, -- dakika cinsinden
    notes TEXT,
    created_by INT NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- İletişim mesajları tablosu
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(255),
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied', 'closed') NOT NULL DEFAULT 'new',
    replied_by INT,
    replied_at DATETIME,
    created_at DATETIME NOT NULL,
    updated_at DATETIME,
    FOREIGN KEY (replied_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Ayarlar tablosu
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(50) NOT NULL UNIQUE,
    setting_value TEXT NOT NULL,
    description VARCHAR(255),
    is_public TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME
);

-- Varsayılan kullanıcı
INSERT INTO users (first_name, last_name, email, phone, password, role, is_active, created_at)
VALUES ('Admin', 'User', 'admin@e-salon.com', '05551234567', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, NOW());

-- Örnek paketler
INSERT INTO packages (name, description, price, duration, duration_unit, is_active, is_highlighted, features, display_order, created_at)
VALUES 
('Aylık Paket', 'Bir aylık tam erişim paketi', 299.99, 1, 'month', 1, 0, 'Sınırsız salon kullanımı\nGrup derslerine erişim\nSauna ve buhar odası kullanımı', 1, NOW()),
('3 Aylık Paket', 'Üç aylık tam erişim paketi, aylık pakete göre %10 indirimli', 809.99, 3, 'month', 1, 1, 'Sınırsız salon kullanımı\nGrup derslerine erişim\nSauna ve buhar odası kullanımı\nBir ücretsiz kişisel eğitmen seansı', 2, NOW()),
('Yıllık Paket', 'Bir yıllık tam erişim paketi, aylık pakete göre %20 indirimli', 2879.99, 12, 'month', 1, 0, 'Sınırsız salon kullanımı\nGrup derslerine erişim\nSauna ve buhar odası kullanımı\nDört ücretsiz kişisel eğitmen seansı\nÜcretsiz otopark', 3, NOW()),
('10 Ders Paketi', 'İstediğiniz zaman kullanabileceğiniz 10 dersten oluşan esnek paket', 499.99, 3, 'month', 1, 0, '10 seans kullanım hakkı\nGrup derslerine erişim\nSauna ve buhar odası kullanımı', 4, NOW());

-- Örnek salonlar
INSERT INTO rooms (name, capacity, description, is_active, created_at)
VALUES 
('Ana Salon', 50, 'Fitness ekipmanları ve serbest ağırlıklar içeren ana salon', 1, NOW()),
('Pilates Stüdyosu', 20, 'Pilates ekipmanları ile donatılmış özel stüdyo', 1, NOW()),
('Yoga Salonu', 25, 'Yoga ve meditasyon için özel tasarlanmış salon', 1, NOW()),
('Grup Dersleri Salonu', 30, 'Grup dersleri için tasarlanmış geniş salon', 1, NOW()),
('Spinning Salonu', 15, 'Spinning bisikletleri ile donatılmış salon', 1, NOW());

-- Örnek eğitmen
INSERT INTO users (first_name, last_name, email, phone, password, role, is_active, specialization, bio, certifications, expertise, created_at)
VALUES 
('Ayşe', 'Yılmaz', 'ayse@e-salon.com', '05551235678', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'trainer', 1, 'Yoga ve Pilates Eğitmeni', 'Yoga ve pilates alanında 10 yıllık deneyime sahip sertifikalı eğitmen. Her seviyede dersler vermektedir.', 'Yoga Alliance RYT-200\nStott Pilates Level 1 & 2\nAnatomik Pilates Eğitmenliği', 'Hatha Yoga\nVinyasa Yoga\nMat Pilates\nReformer Pilates\nGerme ve Esneme Teknikleri', NOW()),
('Mehmet', 'Demir', 'mehmet@e-salon.com', '05551236789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'trainer', 1, 'Fitness ve Beslenme Koçu', 'Profesyonel vücut geliştirme ve fitness eğitmeni. Beslenme danışmanlığı da vermektedir.', 'NASM Sertifikalı Kişisel Eğitmen\nACE Beslenme Uzmanlığı\nCrossFit Level 2 Eğitmenliği', 'Kuvvet Antrenmanı\nKardiyovasküler Fitness\nBeslenme Programları\nFit Kalma Stratejileri\nKilo Verme ve Kas Kazanma', NOW());

-- Örnek dersler (gelecek hafta için)
INSERT INTO classes (class_name, class_type, trainer_id, room_id, capacity, description, class_date, start_time, end_time, is_active, created_at)
VALUES 
('Sabah Yogası', 'yoga', 1, 3, 20, 'Güne enerjik başlamak için sabah yoga dersi', DATE_ADD(CURDATE(), INTERVAL 1 DAY), '09:00:00', '10:00:00', 1, NOW()),
('Vinyasa Akışı', 'yoga', 1, 3, 20, 'Orta seviye vinyasa yoga dersi', DATE_ADD(CURDATE(), INTERVAL 1 DAY), '18:00:00', '19:00:00', 1, NOW()),
('HIIT Antrenmanı', 'fitness', 2, 4, 25, 'Yüksek yoğunluklu interval antrenman', DATE_ADD(CURDATE(), INTERVAL 2 DAY), '10:00:00', '11:00:00', 1, NOW()),
('Temel Pilates', 'pilates', 1, 2, 15, 'Yeni başlayanlar için pilates dersi', DATE_ADD(CURDATE(), INTERVAL 2 DAY), '14:00:00', '15:00:00', 1, NOW()),
('Akşam Fitness', 'fitness', 2, 1, 30, 'İş çıkışı için fitness dersi', DATE_ADD(CURDATE(), INTERVAL 3 DAY), '19:00:00', '20:00:00', 1, NOW()),
('Zumba Partisi', 'zumba', 1, 4, 25, 'Eğlenceli zumba dans dersi', DATE_ADD(CURDATE(), INTERVAL 4 DAY), '17:00:00', '18:00:00', 1, NOW()),
('Sabah Pilates', 'pilates', 1, 2, 15, 'Güne başlamak için pilates dersi', DATE_ADD(CURDATE(), INTERVAL 5 DAY), '09:00:00', '10:00:00', 1, NOW()),
('Fonksiyonel Antrenman', 'fitness', 2, 1, 20, 'Günlük yaşam için fonksiyonel egzersizler', DATE_ADD(CURDATE(), INTERVAL 6 DAY), '11:00:00', '12:00:00', 1, NOW());

-- Uygulama ayarları
INSERT INTO settings (setting_key, setting_value, description, is_public, created_at)
VALUES 
('site_name', 'E-Salon', 'Site adı', 1, NOW()),
('site_description', 'Profesyonel spor salonu yönetim sistemi', 'Site açıklaması', 1, NOW()),
('contact_email', 'info@e-salon.com', 'İletişim e-posta adresi', 1, NOW()),
('contact_phone', '+90 312 123 45 67', 'İletişim telefon numarası', 1, NOW()),
('opening_hours_weekday', '07:00 - 22:00', 'Hafta içi çalışma saatleri', 1, NOW()),
('opening_hours_weekend', '09:00 - 20:00', 'Hafta sonu çalışma saatleri', 1, NOW()),
('address', 'Atatürk Cad. No:123 Merkez/Ankara', 'Fiziksel adres', 1, NOW()),
('sms_api_key', 'your-sms-api-key', 'SMS API anahtarı', 0, NOW()),
('payment_gateway', 'iyzico', 'Ödeme altyapısı sağlayıcısı', 0, NOW()); 