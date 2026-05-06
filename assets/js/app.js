document.addEventListener('DOMContentLoaded', () => {
    // 1. Animation au défilement (Scroll Reveal)
    const revealElements = () => {
        const reveals = document.querySelectorAll('.vehicle-card, .stat-card, .feature-item, .section-title, .detail-image, .detail-info');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                    // Optionnel: arrêter d'observer une fois révélé
                    // observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        reveals.forEach(el => {
            el.classList.add('reveal');
            observer.observe(el);
        });
    };
    revealElements();

    // 2. Gestion du Header au scroll
    const header = document.querySelector('.header');
    if (header) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    }

    // 3. Effet de clic sur les boutons (Ripple effect light)
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(btn => {
        btn.addEventListener('mousedown', function(e) {
            const x = e.clientX - e.target.offsetLeft;
            const y = e.clientY - e.target.offsetTop;
            
            const ripple = document.createElement('span');
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            // Cette partie nécessite du CSS spécifique si on veut un vrai ripple
            // Mais l'effet CSS :active défini plus haut suffit déjà largement
        });
    });

    // 4. Gestion des onglets (Amélioré)
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContent = document.querySelector('.tab-content');

    if (tabBtns.length > 0 && tabContent) {
        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                tabBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                
                // Animation de sortie/entrée
                tabContent.style.opacity = '0';
                tabContent.style.transform = 'translateY(10px)';
                
                setTimeout(() => {
                    const type = btn.textContent.toLowerCase();
                    if (type === 'description') {
                        // Restaurer contenu initial (normalement via PHP mais ici pour la démo)
                    } else if (type === 'caractéristiques') {
                        tabContent.innerHTML = "<ul><li><i class='fas fa-check'></i> Climatisation bi-zone</li><li><i class='fas fa-check'></i> Régulateur de vitesse</li><li><i class='fas fa-check'></i> Bluetooth & USB</li></ul>";
                    } else if (type === 'conditions') {
                        tabContent.innerHTML = "<p><i class='fas fa-info-circle'></i> Caution de 500.000 FCFA. Permis de plus de 2 ans requis.</p>";
                    }
                    tabContent.style.opacity = '1';
                    tabContent.style.transform = 'translateY(0)';
                    tabContent.style.transition = 'all 0.4s ease';
                }, 300);
            });
        });
    }

    // 5. Auto-hide des messages flash
    const flashes = document.querySelectorAll('.flash');
    flashes.forEach(flash => {
        setTimeout(() => {
            flash.style.opacity = '0';
            flash.style.transform = 'translateY(-20px)';
            flash.style.transition = 'all 0.6s cubic-bezier(0.165, 0.84, 0.44, 1)';
            setTimeout(() => flash.remove(), 600);
        }, 5000);
    });

    // 6. Micro-interactivité sur les inputs
    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
        input.addEventListener('focus', () => {
            input.parentElement.classList.add('focused');
        });
        input.addEventListener('blur', () => {
            input.parentElement.classList.remove('focused');
        });
    });
});
