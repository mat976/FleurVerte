/**
 * Effets de parallaxe pour les éléments de la page
 */

document.addEventListener('DOMContentLoaded', function() {
    // Sélection de tous les éléments avec effet parallaxe
    const parallaxElements = document.querySelectorAll('.parallax');
    let ticking = false;
    
    // Fonction pour mettre à jour l'effet parallaxe
    const updateParallax = () => {
        const scrollPosition = window.scrollY;
        parallaxElements.forEach(element => {
            const speed = element.dataset.speed || 0.5;
            const yPos = -(scrollPosition * speed);
            element.style.transform = `translate3d(0px, ${yPos}px, 0px)`;
        });
        ticking = false;
    };

    // Throttle avec requestAnimationFrame pour optimiser les performances
    const onScroll = () => {
        if (!ticking) {
            window.requestAnimationFrame(updateParallax);
            ticking = true;
        }
    };

    // Mise à jour de l'effet parallaxe lors du défilement (avec throttle)
    window.addEventListener('scroll', onScroll, { passive: true });
    
    // Initialiser immédiatement
    onScroll();
});

export default {};
