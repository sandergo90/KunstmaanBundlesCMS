import xhr from './Xhr';
import utils from './utils';

const sidebar = {
    init
};

const CLASSES = {
    MODAL: 'legal-modal',
    MODALOPEN: 'html--modal--open',
    TOGGLELINK: 'js-toggle__link',
    SIDEBAR: {
        HOOK: 'legal-sidebar',
        OPEN: 'legal-modal--open',
        BACK: 'js-legal-sidebar__back',
        CONTENTWRAPPER: 'legal-sidebar__content-wrapper'
    }
};

let toggleLinks;
let hook;
let modal;
let backButton;

/**
 * Init sidebar
 */
function init() {
    modal = document.querySelector(`.${CLASSES.MODAL}`);
    hook = modal.querySelector(`.${CLASSES.SIDEBAR.HOOK}`);
    backButton = hook.querySelector(`.${CLASSES.SIDEBAR.BACK}`);

    toggleLinks = Array.prototype.slice.call(modal.querySelectorAll(`.${CLASSES.TOGGLELINK}`));

    toggleLinks.forEach((toggleLink) => {
        toggleLink.addEventListener('click', toggleLinkHandler);
    });

    backButton.addEventListener('click', backButtonHandler);
}

/**
 * Handler for getting content
 * @param event
 */
function toggleLinkHandler(event) {
    event.preventDefault();
    
    const detailUrl = event.currentTarget.getAttribute('href');

    xhr.get(detailUrl).then((request) => {
        const response = request.responseText;

        const wrapper = hook.querySelector(`.${CLASSES.SIDEBAR.CONTENTWRAPPER}`);

        if (typeof wrapper !== 'undefined') {
            wrapper.innerHTML = response;
        }
    }).then(() => {
        modal.classList.add(`${CLASSES.SIDEBAR.OPEN}`);
    });
}

/**
 * Handler for back button on click, closing sidebar
 * @param event
 */
function backButtonHandler(event) {
    event.preventDefault();

    modal.classList.remove(`${CLASSES.SIDEBAR.OPEN}`);
}

export default sidebar;