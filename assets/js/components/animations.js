/**
 * Animations générales pour le site
 * Ce fichier contient les animations principales utilisées sur plusieurs pages
 */

document.addEventListener('DOMContentLoaded', function() {
    // Animation pour les éléments qui entrent dans le viewport
    const animateOnScroll = () => {
        const elements = document.querySelectorAll('.animate-on-scroll');
        
        elements.forEach(element => {
            const elementPosition = element.getBoundingClientRect().top;
            const windowHeight = window.innerHeight;
            
            if (elementPosition < windowHeight - 50) {
                element.classList.add('animated');
            }
        });
    };

    // Animation des cartes de produits
    const initProductCards = () => {
        const cards = document.querySelectorAll('.product-card');
        
        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.classList.add('hover');
            });
            
            card.addEventListener('mouseleave', function() {
                this.classList.remove('hover');
            });
        });
    };

    // Initialisation des animations
    animateOnScroll();
    initProductCards();
    
    // Événement de défilement pour les animations au scroll
    window.addEventListener('scroll', animateOnScroll);
});

export default {};
