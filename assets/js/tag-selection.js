/**
 * Script pour améliorer l'interaction avec les tags dans la recherche
 */
document.addEventListener('DOMContentLoaded', function () {
    // Sélectionner tous les boutons radio de tags
    const tagRadios = document.querySelectorAll('input[name="tag"]');

    // Ajouter un écouteur d'événement à chaque bouton radio
    tagRadios.forEach(radio => {
        radio.addEventListener('change', function () {
            // Retirer la classe tag-selected de tous les spans de tags
            document.querySelectorAll('.inline-block.px-2.py-1.rounded-full').forEach(span => {
                span.classList.remove('tag-selected');
            });

            // Si un tag est sélectionné (pas "Tous les tags")
            if (this.value) {
                // Trouver le span associé au tag sélectionné et ajouter la classe
                const label = document.querySelector(`label[for="tag_${this.value}"]`);
                if (label) {
                    const span = label.querySelector('span');
                    if (span) {
                        span.classList.add('tag-selected');

                        // Animation simple pour attirer l'attention
                        span.animate([
                            { transform: 'scale(1)' },
                            { transform: 'scale(1.1)' },
                            { transform: 'scale(1)' }
                        ], {
                            duration: 300,
                            iterations: 1
                        });
                    }
                }
            }
        });
    });

    // Vérifier si un tag est déjà sélectionné au chargement de la page
    const checkedRadio = document.querySelector('input[name="tag"]:checked');
    if (checkedRadio && checkedRadio.value) {
        const label = document.querySelector(`label[for="tag_${checkedRadio.value}"]`);
        if (label) {
            const span = label.querySelector('span');
            if (span) {
                span.classList.add('tag-selected');
            }
        }
    }
});