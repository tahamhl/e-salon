/**
 * E-Salon Ana JavaScript Dosyası
 */

// DOM hazır olduğunda
document.addEventListener('DOMContentLoaded', function() {
    // Scroll eventi için navbar davranışı
    handleNavbarScroll();
    
    // Form validasyonları
    initFormValidations();
    
    // Bileşen Başlatmaları
    initTooltips();
    initAjaxForms();
    
    // Üyelik paketi seçimi
    initPackageSelection();
    
    // Kullanıcı seçimi değiştiğinde üyeliklerini getir
    initUserMembershipSelector();
});

/**
 * Navbar'ın sayfa kaydırıldıkça davranışını yönetir
 */
function handleNavbarScroll() {
    const navbar = document.querySelector('.navbar');
    
    if (navbar) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 100) {
                navbar.classList.add('navbar-scrolled', 'shadow-sm');
            } else {
                navbar.classList.remove('navbar-scrolled', 'shadow-sm');
            }
        });
    }
}

/**
 * Bootstrap tooltips'i başlatır
 */
function initTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    
    if (tooltipTriggerList.length) {
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
}

/**
 * Form validasyonlarını başlatır
 */
function initFormValidations() {
    const forms = document.querySelectorAll('.needs-validation');
    
    if (forms.length) {
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                
                form.classList.add('was-validated');
            }, false);
        });
    }
}

/**
 * AJAX formları başlatır
 */
function initAjaxForms() {
    const ajaxForms = document.querySelectorAll('.ajax-form');
    
    if (ajaxForms.length) {
        Array.from(ajaxForms).forEach(form => {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                
                const formData = new FormData(form);
                const submitButton = form.querySelector('[type="submit"]');
                const spinner = form.querySelector('.spinner');
                
                // Buton durumunu güncelle
                if (submitButton) {
                    submitButton.disabled = true;
                }
                
                // Spinner göster
                if (spinner) {
                    spinner.classList.remove('d-none');
                }
                
                // AJAX isteği gönder
                fetch(form.action, {
                    method: form.method,
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    // İşlem başarılı
                    if (data.success) {
                        showAlert('success', data.message);
                        
                        // Yönlendirme varsa
                        if (data.redirect) {
                            setTimeout(() => {
                                window.location.href = data.redirect;
                            }, 1500);
                        }
                        
                        // Form sıfırla
                        form.reset();
                        form.classList.remove('was-validated');
                    } else {
                        // Hata mesajı
                        showAlert('danger', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('danger', 'İşlem sırasında bir hata oluştu.');
                })
                .finally(() => {
                    // Buton durumunu geri al
                    if (submitButton) {
                        submitButton.disabled = false;
                    }
                    
                    // Spinner gizle
                    if (spinner) {
                        spinner.classList.add('d-none');
                    }
                });
            });
        });
    }
}

/**
 * Bootstrap uyarı kutusu gösterir
 */
function showAlert(type, message) {
    const alertContainer = document.querySelector('.alert-container');
    
    if (alertContainer) {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        alertContainer.appendChild(alert);
        
        // 5 saniye sonra otomatik kapat
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    }
}

/**
 * Üyelik paketi seçimini başlatır
 */
function initPackageSelection() {
    const packageCards = document.querySelectorAll('.package-card');
    
    if (packageCards.length) {
        packageCards.forEach(card => {
            card.addEventListener('click', function() {
                // Seçili sınıfını kaldır
                packageCards.forEach(c => c.classList.remove('border-primary'));
                
                // Bu kartı seçili yap
                card.classList.add('border-primary');
                
                // Gizli input'a değeri ata
                const packageId = card.dataset.packageId;
                const packageInput = document.querySelector('#selected_package_id');
                
                if (packageInput && packageId) {
                    packageInput.value = packageId;
                }
            });
        });
    }
}

/**
 * Kullanıcı değiştiğinde üyeliklerini getiren fonksiyon
 */
function initUserMembershipSelector() {
    const userSelect = document.querySelector('#user_id');
    const membershipSelect = document.querySelector('#membership_id');
    
    if (userSelect && membershipSelect) {
        userSelect.addEventListener('change', function() {
            const userId = this.value;
            
            if (userId) {
                // Üyelik seçimini devre dışı bırak ve yükleniyor mesajı göster
                membershipSelect.disabled = true;
                membershipSelect.innerHTML = '<option value="">Yükleniyor...</option>';
                
                // AJAX isteği ile üyelikleri getir
                fetch(`payments.php?action=get_memberships&user_id=${userId}`)
                    .then(response => response.json())
                    .then(data => {
                        // Seçim kutusunu doldur
                        membershipSelect.innerHTML = '<option value="">Üyelik Seçin</option>';
                        
                        if (data.length > 0) {
                            data.forEach(membership => {
                                const option = document.createElement('option');
                                option.value = membership.id;
                                option.textContent = `${membership.package_name} (${membership.start_date} - ${membership.end_date})`;
                                membershipSelect.appendChild(option);
                            });
                            
                            membershipSelect.disabled = false;
                        } else {
                            membershipSelect.innerHTML = '<option value="">Aktif üyelik bulunamadı</option>';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        membershipSelect.innerHTML = '<option value="">Hata oluştu</option>';
                    });
            } else {
                // Kullanıcı seçilmediğinde seçim kutusunu sıfırla
                membershipSelect.innerHTML = '<option value="">Önce kullanıcı seçin</option>';
                membershipSelect.disabled = true;
            }
        });
    }
} 