/**
 * AutoPartage - Application JS
 */

document.addEventListener('DOMContentLoaded', () => {
    // Gestion des onglets sur la page détail
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContent = document.querySelector('.tab-content');

    if (tabBtns.length > 0 && tabContent) {
        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                tabBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                
                // Simulation de changement de contenu
                const type = btn.textContent.toLowerCase();
                if (type === 'description') {
                    // Contenu par défaut
                } else if (type === 'caractéristiques') {
                    tabContent.innerHTML = "<ul><li>Climatisation bi-zone</li><li>Régulateur de vitesse adaptatif</li><li>Système audio Premium</li><li>Toit ouvrant panoramique</li><li>Aide au stationnement 360°</li></ul>";
                } else if (type === 'conditions') {
                    tabContent.innerHTML = "<p>Âge minimum : 21 ans. Permis de conduire de plus de 2 ans. Caution de 500 000 FCFA requise par empreinte bancaire. Kilométrage illimité inclus.</p>";
                }
            });
        });
    }

    // Auto-hide des messages flash après 5 secondes
    const flashes = document.querySelectorAll('.flash');
    flashes.forEach(flash => {
        setTimeout(() => {
            flash.style.opacity = '0';
            flash.style.transform = 'translateY(-10px)';
            flash.style.transition = 'all 0.5s ease';
            setTimeout(() => flash.remove(), 500);
        }, 5000);
    });

    // Animation au défilement simple
    const observerOptions = {
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.vehicle-card, .stat-card, .feature-item').forEach(el => {
        observer.observe(el);
    });
});
