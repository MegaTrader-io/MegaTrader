<?php
$mt_platforms = $args['platforms'] ?? [];
?>

<div class="modal modal-subcription fade" id="selectPlatformModal" tabindex="-1"
     aria-labelledby="selectPlatformModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"
         style="--bs-modal-width: 450px;--bs-modal-footer-border-color: transparent">
        <div class="modal-content">
            <div class="modal-header w-100 border-0 justify-content-between align-items-start p-0">
                <h5 class="modal-title text-white heading-sm-medium text-uppercase" id="selectPlatformModalLabel">
                    SELECT PLATFORM
                </h5>
                <button type="button" class="p-0 border-0 bg-transparent shadow-none" data-bs-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                             aria-hidden="true" class="text-white w-6 h-6">
                            <path fill-rule="evenodd"
                                  d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z"
                                  clip-rule="evenodd"></path>
                        </svg>
                    </span>
                </button>
            </div>

            <div class="modal-body py-3">
                <div class="subscription-scroll-area px-lg-3 p-0">
                    <!-- Dynamic container -->
                    <div class="subscription-grid"></div>
                </div>
            </div>

            <div class="modal-footer py-0">
                <div class="align-items-center d-flex gap-2 justify-content-end">
                    <div class="mega-btn mega-btn-md rounded-12 text-white" data-bs-dismiss="modal">Cancel</div>
                    <button type="button" id="select-platform-btn"
                            class="mega-btn mega-btn-md mega-btn-secondary-md">
                        Select
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (() => {
        const selectButton = document.querySelector(`#selectPlatformModal #select-platform-btn`);
        const subscriptionGrid = document.querySelector(`#selectPlatformModal .subscription-grid`);

        let items = [];
        let handlerSelect;

        /**
         * Generates the HTML for a single subscription card.
         * @param {Object} item - Platform data.
         * @param {boolean} isActive - Whether the card should appear as active.
         * @returns {string} - Rendered HTML string.
         */
        function createSubscriptionCard(item, isActive = false) {
            const {id, slug, name, thumbnail_url} = item || {};
            const activeClass = isActive ? 'active' : '';
            const safeSlug = slug || id || '';

            return `
            <div class="subscription-card position-relative d-flex flex-column gap-2 ${activeClass}"
                 role="button"
                 data-id="${safeSlug}">

                <div class="checkmark-icon position-absolute" style="top: 10px; right: 10px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                         viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" fill="#FFB34A"></circle>
                        <path d="M10.6 16.6L17.65 9.55L16.25 8.15L10.6 13.8L7.75 10.95L6.35 12.35L10.6 16.6Z"
                              fill="black"></path>
                    </svg>
                </div>

                <div class="subscription-card__header text-center position-relative d-flex flex-column align-items-center">
                    ${thumbnail_url ? `
                        <div class="logo-container position-relative d-inline-block">
                            <img src="${thumbnail_url}" alt="${safeSlug} logo" style="max-height: 40px;">
                        </div>` : ''}
                </div>

                <div class="subscription-card__body text-center">
                    <div class="subscription-card__name fw-medium text-16px text-white">
                        ${name || ''}
                    </div>
                </div>
            </div>
        `;
        }

        /**
         * Clears and renders all cards inside the grid.
         */
        function renderSubscriptionCards(items, selectedId = null) {
            try {
                if (!subscriptionGrid) return;

                // Clear previous content
                subscriptionGrid.innerHTML = '';

                // Build new HTML
                subscriptionGrid.innerHTML = items.map((item, index) => {
                    const isActive = selectedId
                        ? item.slug === selectedId || item.id === selectedId
                        : index === 0;
                    return createSubscriptionCard(item, isActive);
                }).join('');

                updateSelectButtonState();
            } catch (err) {
                console.error('[renderSubscriptionCards] Failed to render platforms:', err);
            }
        }

        function updateSelectButtonState() {
            const activeCard = subscriptionGrid.querySelector('.subscription-card.active');
            const isActive = !!activeCard;
            selectButton.classList.toggle('disabled', !isActive);
            selectButton.style.opacity = isActive ? '1' : '0.5';
            selectButton.style.cursor = isActive ? 'pointer' : 'not-allowed';
        }

        // Handle click on a subscription card
        subscriptionGrid.addEventListener('click', function (e) {
            const card = e.target.closest('.subscription-card');
            if (!card || card.offsetParent === null) return;

            subscriptionGrid.querySelectorAll('.subscription-card.active')
                .forEach(c => c.classList.remove('active'));
            card.classList.add('active');
            updateSelectButtonState();
        });

        // Initialize modal when the custom event is triggered
        document.addEventListener("mt:initializePlatformModal", (e) => {
            items = e?.detail?.items ?? [];
            const selectedId = e?.detail?.selectedId ?? null;
            handlerSelect = e?.detail?.handlerSelect ?? function () {
            };

            renderSubscriptionCards(items, selectedId);
            updateSelectButtonState();
        });

        // Handle Select button click
        selectButton.addEventListener('click', function () {
            const activeCard = subscriptionGrid.querySelector('.subscription-card.active');
            if (!activeCard) return;

            const dataId = activeCard.getAttribute('data-id');
            if (!dataId) return;

            handlerSelect && handlerSelect(dataId);
        });

        updateSelectButtonState();
    })();
</script>
