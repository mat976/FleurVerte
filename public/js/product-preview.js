document.addEventListener('DOMContentLoaded', function () {
    var modal = document.getElementById('product-preview-modal');
    var card = document.getElementById('product-preview-card');
    if (!modal || !card) return;

    var items = document.querySelectorAll('[data-preview]');
    var hideTimeout = null;
    var showTimeout = null;
    var isModalHovered = false;

    function populateModal(el) {
        modal.querySelector('[data-preview-image]').src = el.dataset.previewImage || '';
        modal.querySelector('[data-preview-image]').alt = el.dataset.previewName || '';
        modal.querySelector('[data-preview-name]').textContent = el.dataset.previewName || '';
        modal.querySelector('[data-preview-desc]').textContent = el.dataset.previewDesc || '';
        modal.querySelector('[data-preview-stock]').textContent = el.dataset.previewStock || '';
        modal.querySelector('[data-preview-rating]').textContent = el.dataset.previewRating || '';
        modal.querySelector('[data-preview-link]').href = el.dataset.previewUrl || '#';

        var priceEl = modal.querySelector('[data-preview-price]');
        var promoEl = modal.querySelector('[data-preview-promo]');
        var oldPriceEl = modal.querySelector('[data-preview-old-price]');
        var badgeEl = modal.querySelector('[data-preview-badge]');

        if (el.dataset.previewPromo === 'true') {
            oldPriceEl.textContent = el.dataset.previewOldPrice + '€';
            oldPriceEl.classList.remove('hidden');
            priceEl.textContent = el.dataset.previewPrice + '€';
            badgeEl.textContent = '-' + el.dataset.previewPercent + '%';
            badgeEl.classList.remove('hidden');
            promoEl.classList.remove('hidden');
        } else {
            oldPriceEl.classList.add('hidden');
            priceEl.textContent = el.dataset.previewPrice + '€';
            badgeEl.classList.add('hidden');
            promoEl.classList.add('hidden');
        }

        var tagsContainer = modal.querySelector('[data-preview-tags]');
        tagsContainer.innerHTML = '';
        try {
            var tags = JSON.parse(el.dataset.previewTags || '[]');
            tags.forEach(function (tag) {
                var span = document.createElement('span');
                span.className = 'px-2 py-0.5 rounded-full text-xs font-medium';
                span.style.backgroundColor = tag.color;
                span.style.color = tag.textColor;
                span.textContent = tag.name;
                tagsContainer.appendChild(span);
            });
        } catch (e) {}
    }

    function showModal() {
        clearTimeout(hideTimeout);
        modal.classList.remove('opacity-0', 'scale-95');
        modal.classList.add('opacity-100', 'scale-100');
        modal.style.pointerEvents = 'auto';
    }

    function scheduleHide() {
        hideTimeout = setTimeout(function () {
            if (!isModalHovered) {
                modal.classList.add('opacity-0', 'scale-95');
                modal.classList.remove('opacity-100', 'scale-100');
                modal.style.pointerEvents = 'none';
            }
        }, 300);
    }

    items.forEach(function (el) {
        el.addEventListener('mouseenter', function () {
            clearTimeout(showTimeout);
            showTimeout = setTimeout(function () {
                populateModal(el);
                showModal();
            }, 4500);
        });
        el.addEventListener('mouseleave', function () {
            clearTimeout(showTimeout);
            scheduleHide();
        });
    });

    card.addEventListener('mouseenter', function () {
        isModalHovered = true;
        clearTimeout(hideTimeout);
    });
    card.addEventListener('mouseleave', function () {
        isModalHovered = false;
        scheduleHide();
    });
});
