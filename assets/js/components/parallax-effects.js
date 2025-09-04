/**
 * Effets de parallaxe pour les éléments de la page
 */

document.addEventListener('DOMContentLoaded', function() {
    // Sélection de tous les éléments avec effet parallaxe
    const parallaxElements = document.querySelectorAll('.parallax');
    
    // Fonction pour mettre à jour l'effet parallaxe
    const updateParallax = () => {
        parallaxElements.forEach(element => {
            const scrollPosition = window.scrollY;
            const speed = element.dataset.speed || 0.5;
            const yPos = -(scrollPosition * speed);
            
            element.style.transform = `translate3d(0px, ${yPos}px, 0px)`;
        });
    };

    // Mise à jour de l'effet parallaxe lors du défilement
    window.addEventListener('scroll', updateParallax);
    
    // Initialiser immédiatement
    updateParallax();
});

export default {};
