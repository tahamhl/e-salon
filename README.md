# e-salon


![chrome_tFXdf1JCnS](https://github.com/user-attachments/assets/4ee53c18-daab-4300-a6ab-eca98a39680d)



------



![chrome_v5tTbkM5Kw](https://github.com/user-attachments/assets/a008fe56-437d-470c-801c-3bb01a148ea4)



e-salon, modern bir salon/spor salonu yönetim sistemidir. Kullanıcılar; üyelik, rezervasyon, ödeme, profil yönetimi gibi işlemleri kolayca gerçekleştirebilir.

## Özellikler

- Kullanıcı kaydı ve girişi
- Profil ve şifre yönetimi
- Üyelik paketleri görüntüleme ve satın alma
- Randevu/rezervasyon sistemi
- Ödeme takibi
- Yönetici ve personel panelleri
- İletişim ve bilgilendirme modülleri

## Kurulum

1. **Projeyi klonlayın:**
   ```bash
   git clone https://github.com/tahamhl/e-salon.git
   ```

2. **Veritabanı oluşturun:**
   - `database/create_database.php` veya `database/create_tables.sql` dosyalarını kullanarak veritabanınızı oluşturun.

3. **Ayarları yapın:**
   - `config/config.php` dosyasındaki veritabanı bağlantı bilgilerini kendi ortamınıza göre düzenleyin.

4. **Gerekli bağımlılıkları yükleyin:**
   - Proje temel PHP ile çalışır, ek bir bağımlılık gerekmemektedir.

5. **Projeyi başlatın:**
   - Proje kök dizinini web sunucunuzun kök dizini olarak ayarlayın (ör. `localhost/e-salon`).

## Kullanım

- Kayıt olarak giriş yapabilir, profilinizi yönetebilir, üyelik satın alabilir ve randevu oluşturabilirsiniz.
- Yönetici ve personel rolleri için farklı paneller mevcuttur.

## Katkı

Katkıda bulunmak isterseniz, lütfen bir pull request gönderin veya issue açın.

---

**Geliştirici:** [tahamhl](https://github.com/tahamhl) 
