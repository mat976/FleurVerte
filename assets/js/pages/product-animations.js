/**
 * Animations spécifiques pour les pages de produits
 */

document.addEventListener('DOMContentLoaded', function() {
    // Animation pour la galerie d'images du produit
    const initProductGallery = () => {
        const thumbnails = document.querySelectorAll('.product-thumbnail');
        const mainImage = document.querySelector('.product-main-image img');
        
        if (!mainImage || thumbnails.length === 0) return;
        
        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function() {
                // Désactiver la classe active sur toutes les vignettes
                thumbnails.forEach(thumb => thumb.classList.remove('active'));
                
                // Activer la classe sur la vignette cliquée
                this.classList.add('active');
                
                // Mettre à jour l'image principale
                const newImageSrc = this.getAttribute('data-image');
                if (newImageSrc) {
                    mainImage.style.opacity = '0';
                    
                    setTimeout(() => {
                        mainImage.setAttribute('src', newImageSrc);
                        mainImage.style.opacity = '1';
                    }, 300);
                }
            });
        });
    };
    
    // Zoom sur l'image du produit
    const initImageZoom = () => {
        const zoomContainer = document.querySelector('.product-image-zoom');
        
        if (!zoomContainer) return;
        
        zoomContainer.addEventListener('mousemove', function(e) {
            const { left, top, width, height } = this.getBoundingClientRect();
            const x = (e.clientX - left) / width * 100;
            const y = (e.clientY - top) / height * 100;
            
            this.style.backgroundPosition = `${x}% ${y}%`;
        });
        
        zoomContainer.addEventListener('mouseenter', function() {
            this.style.backgroundSize = '150%';
        });
        
        zoomContainer.addEventListener('mouseleave', function() {
            this.style.backgroundSize = '100%';
            this.style.backgroundPosition = 'center';
        });
    };
    
    // Initialisation des fonctionnalités
    initProductGallery();
    initImageZoom();
});

export default {};
