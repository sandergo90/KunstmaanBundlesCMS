import querySelectorAll from './querySelectorAll';
import xhr from './Xhr';
import scroll from './scroll';
import sidebar from './sidebar';
import toggles from './legalToggleSubscriber';
import utils from './utils';

const legalCookieBar = {
    init
};

const KEYCODE_ESCAPE = 27;
const mq = window.matchMedia( "(max-width: 479px)" );
const mqModal = window.matchMedia( "(max-width: 767px)" );

const CLASSES = {
    HOOK: 'js-legal-modal-handle',
    CONTENT_WRAPPER: 'js-legal-modal__content-wrapper',
    MODAL: {
        DEFAULT: 'legal-modal',
        VISIBLE: 'legal-modal--visible',
        AUTO_OPEN: 'legal-modal--autoopen',
        CLOSE_BUTTON: 'js-legal-modal-close',
        DETAIL: 'legal-modal--detail'
    },
    BACKDROP: {
        VISIBLE: 'legal-modal__backdrop--visible',
        DEFAULT: 'legal-modal__backdrop'
    },
    BAR: 'kumacookiebar',
    BAR_VISIBLE: 'kumacookiebar--show',
    COLLAPSIBLE: {
        TITLE: 'js-collapsible-title',
        CONTENT: 'js-collapsible-content',
        OPEN: 'open'
    },
    TOGGLELINK: 'js-toggle__link'
};

let titles;
let modalTitles;
let resizeTimer;
let isHidden;
let targetModal;
let urlModal;
let handles;
let autoOpenModals;
let backdrop;
let toggleLinks;

/**
 * Init cookie consent
 */
function init() {
    // modals
    handles = querySelectorAll(`.${CLASSES.HOOK}`);
    autoOpenModals = querySelectorAll(`.${CLASSES.MODAL.AUTO_OPEN}`);
    // titles on cookiebar
    titles = querySelectorAll(`.${CLASSES.COLLAPSIBLE.TITLE}`);
    modalTitles = querySelectorAll(`.${CLASSES.COLLAPSIBLE.TITLE}`);
    // toggle buttons in page mode
    toggleLinks = querySelectorAll(`.${CLASSES.TOGGLELINK}`);

    isHidden = true;

    handles.forEach((handle) => {
        handle.addEventListener('click', clickHandler);
    });

    renderBackdrop();

    window.addEventListener('resize', resizeHandler);

    titles.forEach((cookieBarTitle) => {
        collapseResizeHandler(mq, titles);
        cookieBarTitle.addEventListener('click', collapseClickHandler.bind(this, mq));
    });

    modalTitles.forEach((modalTitle) => {
        collapseResizeHandler(mqModal, modalTitles);
        modalTitle.addEventListener('click', collapseClickHandler.bind(this, mqModal));
    });

    toggleLinks.forEach((handle) => {
        handle.addEventListener('click', toggleLinkHandler);
    });
}

/**
 * Render modal backdrop
 */
function renderBackdrop() {
    const backdropNode = document.createElement('div');
    backdropNode.setAttribute('class', `${CLASSES.BACKDROP.DEFAULT}`);

    document.body.appendChild(backdropNode);

    backdrop = {
        node: backdropNode,
        namespace: 'BACKDROP'
    };
}

/**
 * Click handler for opening modals
 * @param event
 */
function clickHandler(event) {
    event.preventDefault();

    const element = event.currentTarget;
    targetModal = document.querySelector(element.dataset.target);
    urlModal = element.dataset.url;

    modalHandler();
}

/**
 * Keyboard handler closing modals
 * @param event
 * @returns Promise
 */
function keyboardHandler(event) {
    if (event.keyCode === KEYCODE_ESCAPE) {
        modalHandler();
    }
}

/**
 * Modal handler
 */
function modalHandler() {
    const closeBtn = targetModal.querySelector(`.${CLASSES.MODAL.CLOSE_BUTTON}`);

    if (isHidden) {
        toggleCookieBar();
        // First get page content.
        xhr.get(urlModal).then((request) => {
            const response = request.responseText;

            const wrapper = querySelectorAll(`.${CLASSES.CONTENT_WRAPPER}`)[0];

            if (typeof wrapper !== 'undefined') {
                wrapper.outerHTML = response;
            }
        }).then(() => {
            targetModal.classList.add(CLASSES.MODAL.VISIBLE);
            backdrop.node.classList.add(CLASSES.BACKDROP.VISIBLE);

            window.addEventListener('keyup', keyboardHandler);
            backdrop.node.addEventListener('click', modalHandler);
            closeBtn.addEventListener('click', modalHandler);

            document.getElementById('html').classList.add('html--modal--open'); // needed for input in modal bug on iOS

            isHidden = false;

            scroll.init();
            sidebar.init();
            toggles.initSingleCookieToggles();

            // inside modal

            modalTitles = querySelectorAll(`.${CLASSES.COLLAPSIBLE.TITLE}`);

            modalTitles.forEach((modalTitle) => {
                collapseResizeHandler(mqModal, modalTitles);
                modalTitle.addEventListener('click', collapseClickHandler.bind(this, mqModal));
            });
        });
    } else {
        targetModal.classList.remove(CLASSES.MODAL.VISIBLE);
        backdrop.node.classList.remove(CLASSES.BACKDROP.VISIBLE);

        window.removeEventListener('keyup', keyboardHandler);
        backdrop.node.removeEventListener('click', modalHandler);
        closeBtn.removeEventListener('click', modalHandler);

        document.getElementById('html').classList.remove('html--modal--open'); // needed for input in modal bug on iOS

        isHidden = true;

        toggleCookieBar();
    }
}

/**
 * Resize handler
 */
function resizeHandler() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function() {
        collapseResizeHandler(mq, titles);
        collapseResizeHandler(mqModal, modalTitles);
    }, 250);
}

/**
 * Collapse handler on resize
 * @param media
 * @param collection
 */
function collapseResizeHandler(media, collection) {
    if (collection) {
        collection.forEach((handle) => {
            const content = handle.parentElement.querySelector(`.${CLASSES.COLLAPSIBLE.CONTENT}`);

            if (media.matches) {
                if (handle.className.indexOf(`${CLASSES.COLLAPSIBLE.OPEN}`) > -1) {
                    content.style.marginTop = 0;
                } else {
                    content.style.marginTop = `${-content.offsetHeight -25}px`;
                }
            } else {
                content.style.marginTop = 0;
            }
        });
    }
}

/**
 * Collapse handler on click
 * @param media
 * @param event
 */
function collapseClickHandler(media, event) {
    event.preventDefault();
    if (media.matches) {
        const title = event.currentTarget.parentElement.querySelector(`.${CLASSES.COLLAPSIBLE.TITLE}`);
        const content = event.currentTarget.parentElement.querySelector(`.${CLASSES.COLLAPSIBLE.CONTENT}`);

        if (parseInt(content.style.marginTop) < 0) {
            content.style.marginTop = 0;
            if (title.className.indexOf(`${CLASSES.COLLAPSIBLE.OPEN}`) < 0) {
                title.classList.add(`${CLASSES.COLLAPSIBLE.OPEN}`);
            }
        } else {
            content.style.marginTop = `${-content.offsetHeight  -25}px`;
            if (title.className.indexOf(`${CLASSES.COLLAPSIBLE.OPEN}`) > -1) {
                title.classList.remove(`${CLASSES.COLLAPSIBLE.OPEN}`);
            }
        }
    }
}

/**
 * Handler for toggles on page mode
 * @param event
 */
function toggleLinkHandler(event) {
    event.preventDefault();

    const element = event.currentTarget;
    targetModal = document.querySelector(element.dataset.target);
    urlModal = element.href;

    targetModal.classList.add(`${CLASSES.MODAL.DETAIL}`);

    modalHandler();


}

/**
 * Toggles visibility of cookiebar
 */
function toggleCookieBar() {
    const bar = document.querySelector(`.${CLASSES.BAR}`);

    if (bar instanceof HTMLElement) {
        if (utils.hasClass(bar, `${CLASSES.BAR_VISIBLE}`)) {
            bar.classList.remove(`${CLASSES.BAR_VISIBLE}`);
        } else {
            bar.classList.add(`${CLASSES.BAR_VISIBLE}`);
        }
    }
}

export default legalCookieBar;
