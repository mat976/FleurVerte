import { Controller } from '@hotwired/stimulus';

/**
 * Contrôleur de sélection d'avatar
 * 
 * Gère la sélection d'avatars prédéfinis et le téléchargement d'images personnalisées
 * dans la page d'édition de profil.
 */
export default class extends Controller {
    static targets = ['radio', 'file', 'image', 'preview'];

    static classes = ['selected'];

    static values = {
        selectedClass: { type: String, default: 'ring-2 ring-green-500' }
    };

    /**
     * Sélection d'un avatar prédéfini
     */
    selectAvatar(event) {
        const avatarName = event.currentTarget.dataset.avatar;
        
        // Réinitialiser le champ fichier
        if (this.hasFileTarget) {
            this.fileTarget.value = '';
        }

        // Mettre à jour l'état visuel
        this.updateSelection(event.currentTarget);

        // Mettre à jour la prévisualisation si disponible
        if (this.hasPreviewTarget) {
            const img = event.currentTarget.querySelector('img');
            if (img) {
                this.previewTarget.src = img.src;
            }
        }
    }

    /**
     * Gestion du téléchargement d'un fichier
     */
    handleFileUpload(event) {
        const file = event.target.files[0];
        if (!file) return;

        // Désélectionner tous les avatars prédéfinis
        this.clearSelection();

        // Afficher la prévisualisation
        if (this.hasPreviewTarget) {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.previewTarget.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }

    /**
     * Met à jour l'état visuel de la sélection
     */
    updateSelection(selectedElement) {
        // Retirer la sélection de tous les éléments
        this.clearSelection();

        // Ajouter la sélection à l'élément cliqué
        selectedElement.classList.add(...this.selectedClassValue.split(' '));
    }

    /**
     * Réinitialise la sélection visuelle
     */
    clearSelection() {
        this.element.querySelectorAll('[data-avatar]').forEach(el => {
            el.classList.remove(...this.selectedClassValue.split(' '));
        });
    }
}