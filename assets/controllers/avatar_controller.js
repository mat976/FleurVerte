import { Controller } from '@hotwired/stimulus';

/**
 * Avatar selection controller
 *
 * This controller manages the avatar selection functionality in the profile edit page.
 * It handles highlighting the selected avatar and clearing the file input when a predefined avatar is selected.
 */
export default class extends Controller {
    static targets = ['avatarRadio', 'fileInput', 'avatarImage'];

    connect() {
        console.log('Avatar controller connected');
        this.addEventListeners();
    }

    addEventListeners() {
        // Add click event listeners to all avatar radio buttons
        if (this.hasAvatarRadioTarget) {
            this.avatarRadioTargets.forEach(radio => {
                radio.addEventListener('change', this.handleAvatarSelection.bind(this));
            });
        }

        // Add change event listener to file input
        if (this.hasFileInputTarget) {
            this.fileInputTarget.addEventListener('change', this.handleFileSelection.bind(this));
        }
    }

    handleAvatarSelection(event) {
        // When a predefined avatar is selected, clear the file input
        if (this.hasFileInputTarget) {
            this.fileInputTarget.value = '';
        }

        // Update visual indication of selected avatar
        if (this.hasAvatarImageTarget) {
            this.avatarImageTargets.forEach(img => {
                // Remove highlight from all avatars
                img.classList.remove('ring-2', 'ring-green-500');
                // Add hover effect to all
                img.classList.add('hover:ring-2', 'hover:ring-green-300');
            });

            // Add highlight to selected avatar
            const selectedLabel = event.target.closest('label');
            if (selectedLabel) {
                const selectedImg = selectedLabel.querySelector('img');
                if (selectedImg) {
                    selectedImg.classList.add('ring-2', 'ring-green-500');
                }
            }
        }
    }

    handleFileSelection(event) {
        // When a file is selected, uncheck all predefined avatars
        if (this.hasAvatarRadioTarget && event.target.value) {
            this.avatarRadioTargets.forEach(radio => {
                radio.checked = false;
            });

            // Remove highlight from all avatars
            if (this.hasAvatarImageTarget) {
                this.avatarImageTargets.forEach(img => {
                    img.classList.remove('ring-2', 'ring-green-500');
                });
            }
        }
    }
}