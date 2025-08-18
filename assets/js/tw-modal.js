function lockGlobalScroll() {
    document.body.style.pointerEvents = 'none';
    document.body.dataset.scrollLocked = '1';
}

function unlockGlobalScroll() {
    document.body.style.removeProperty('pointer-events');
    delete document.body.dataset.scrollLocked;
}

function getElementByDialogId(modalId) {
    return document.getElementById(modalId) || null;
}

function toggleOverlayElements(show = true) {
    const displayValue = show ? '' : 'none';

    ['.overlay-header', '.overlay-footer-top', '.overlay-footer-bottom'].forEach(selector => {
        const el = document.querySelector(selector);
        if (el) el.style.display = displayValue;
    });
}

function toggleIntercomDisplay(show = true) {
    const intercomContainer = document.querySelector('.intercom-lightweight-app') || document.getElementById('intercom-container');
    if (intercomContainer) {
        intercomContainer.style.display = show ? 'block' : 'none';
    }
}

function createEscapeKeyHandler(modalId) {
    return function handleKeyDown(event) {
        try {
            if (event.key === 'Escape') {
                const modal = document.querySelector('.mgt-dialog[data-state="open"]');
                if (modal && modal.id === modalId) {
                    hideModal(modalId);
                }
            }
        } catch (error) {
            console.error('Error handling ESC:', error);
        }
    };
}

function createResizeHandler() {
    return function handleResize() {
        const isMobile = window.innerWidth < 640;
        toggleIntercomDisplay(!isMobile);
    };
}

let resizeObserver = null;

function createOption(select, value, text, firstOption = false) {
    const option = document.createElement('option');
    option.value = value;
    if (firstOption) {
        option.disabled = "";
    }
    option.text = text;
    select.appendChild(option);
}

function showModal(modalId) {
    const dialog = getElementByDialogId(modalId);
    if (!dialog) {
        console.info('Modal dialog not found');
        return;
    }

    lockGlobalScroll();
    dialog.style.removeProperty('display');
    dialog.dataset.state = 'open';

    toggleOverlayElements(true);

    const closeButton = dialog.querySelector('.btn-close-dialog');
    if (closeButton) {
        closeButton.addEventListener('click', () => hideModal(modalId), {once: true});
    }

    document.addEventListener('keydown', createEscapeKeyHandler(modalId), {once: true});

    const handleResize = createResizeHandler();
    resizeObserver = new ResizeObserver(handleResize);

    handleResize();
    resizeObserver.observe(document.body);

    document.querySelector('.overlay-footer-top').addEventListener('click', () => hideModal(modalId), {once: true});

    const select = dialog.querySelector('select[name=titles]');
    select.innerHTML = '';

    createOption(select, 0, "Table of contents", true)

    dialog.querySelectorAll('.title-dialog').forEach(element => {
        createOption(select, element.id, element.innerText);
    })

    dialog.querySelector('.overflow-auto').scrollTo({
        top: 0,
        behavior: 'instant'
    });

    select.addEventListener('change', (e) => {
        const scrollContainer = dialog.querySelector('.overflow-auto');
        const targetId = e.target.value;

        if (targetId === "0") {
            scrollContainer.scrollTo({
                top: 0,
                behavior: 'smooth'
            });

            return
        }

        const targetEl = dialog.querySelector(`#${targetId}`);

        if (!targetEl) {
            return
        }

        targetEl.scrollIntoView({behavior: 'smooth', block: 'start'});
    });
}

function hideModal(modalId) {
    const dialog = getElementByDialogId(modalId);
    if (!dialog) {
        console.info('Modal dialog not found');
        return;
    }

    unlockGlobalScroll();
    dialog.style.display = 'none';
    delete dialog.dataset.state;

    toggleOverlayElements(false);

    if (resizeObserver) {
        resizeObserver.disconnect();
        resizeObserver = null;
    }

    toggleIntercomDisplay(true);
}
