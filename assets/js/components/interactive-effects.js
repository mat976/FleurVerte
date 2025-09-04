/**
 * Effets interactifs pour améliorer l'expérience utilisateur
 */

document.addEventListener('DOMContentLoaded', function() {
    // Gestion des popups et modals
    const initModals = () => {
        const modalTriggers = document.querySelectorAll('[data-modal-trigger]');
        const modals = document.querySelectorAll('.modal');
        const closeButtons = document.querySelectorAll('.modal-close');
        
        modalTriggers.forEach(trigger => {
            trigger.addEventListener('click', (e) => {
                e.preventDefault();
                const targetId = trigger.getAttribute('data-modal-target');
                const targetModal = document.getElementById(targetId);
                
                if (targetModal) {
                    targetModal.classList.add('active');
                    document.body.classList.add('modal-open');
                }
            });
        });
        
        closeButtons.forEach(button => {
            button.addEventListener('click', () => {
                const modal = button.closest('.modal');
                modal.classList.remove('active');
                document.body.classList.remove('modal-open');
            });
        });
        
        // Fermer les modals en cliquant à l'extérieur
        modals.forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.remove('active');
                    document.body.classList.remove('modal-open');
                }
            });
        });
    };
    
    // Gestion des accordéons
    const initAccordions = () => {
        const accordionHeaders = document.querySelectorAll('.accordion-header');
        
        accordionHeaders.forEach(header => {
            header.addEventListener('click', () => {
                const accordion = header.parentElement;
                const isActive = accordion.classList.contains('active');
                
                // Fermer tous les accordéons
                document.querySelectorAll('.accordion').forEach(acc => {
                    acc.classList.remove('active');
                });
                
                // Ouvrir l'accordéon cliqué s'il n'était pas déjà ouvert
                if (!isActive) {
                    accordion.classList.add('active');
                }
            });
        });
    };

    // Initialisation
    initModals();
    initAccordions();
});

export default {};
