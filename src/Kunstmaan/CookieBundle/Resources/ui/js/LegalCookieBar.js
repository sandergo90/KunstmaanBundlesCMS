import querySelectorAll from './querySelectorAll';
import xhr from './Xhr';

const KEYCODE_ESCAPE = 27;

const CLASSES = {
    HOOK: 'js-legal-modal-handle',
    CONTENT_WRAPPER: 'js-legal-modal__content-wrapper',
    MODAL: {
        DEFAULT: 'legal-modal',
        VISIBLE: 'legal-modal--visible',
        AUTO_OPEN: 'legal-modal--autoopen',
        CLOSE_BUTTON: 'js-legal-modal-close'
    },
    BACKDROP: {
        VISIBLE: 'legal-modal__backdrop--visible',
        DEFAULT: 'legal-modal__backdrop'
    },
    TITLE: 'legal-title',
    PARAGRAPH: 'legal-paragraph',
    OPEN: 'open'
};

let titles;
let resizeTimer;
const mq = window.matchMedia( "(max-width: 479px)" );

export default class LegalCookieBar {
    constructor() {
        this.isHidden = true;
        this.backdrop = undefined;
        this.targetModal = undefined;
        this.urlModal = undefined;

        const handles = querySelectorAll(`.${CLASSES.HOOK}`);
        const autoOpenModals = querySelectorAll(`.${CLASSES.MODAL.AUTO_OPEN}`);

        this.clickHandler = this.clickHandler.bind(this);
        this.renderBackdrop = this.renderBackdrop.bind(this);
        this.modalHandler = this.modalHandler.bind(this);

        handles.forEach((handle) => {
            handle.addEventListener('click', this.clickHandler);
        });

        this.renderBackdrop();

        autoOpenModals.forEach((handle) => {
            this.targetModal = handle;
            this.modalHandler();
        });

        titles = Array.prototype.slice.call(document.querySelectorAll(`.${CLASSES.TITLE}`));

        window.addEventListener('resize', this.resizeHandler);

        titles.forEach((title) => {
            if (mq.matches) {
                const content = title.parentElement.querySelector(`.${CLASSES.PARAGRAPH}`);
                
                content.style.marginTop = `${-content.offsetHeight}px`;
            }

            title.addEventListener('click', this.titleClickHandler);
        });
    }

    clickHandler(e) {
        e.preventDefault();

        const element = e.currentTarget;
        this.targetModal = document.querySelector(element.dataset.target);
        this.urlModal = element.dataset.url;

        this.modalHandler();
    }

    modalHandler() {
        const closeBtn = this.targetModal.querySelector(`.${CLASSES.MODAL.CLOSE_BUTTON}`);

        if (this.isHidden) {
            // First get page content.
            xhr.get(this.urlModal).then((request) => {
                const response = request.responseText;

                const wrapper = querySelectorAll(`.${CLASSES.CONTENT_WRAPPER}`)[0];

                if (typeof wrapper !== 'undefined') {
                    wrapper.outerHTML = response;
                }
            }).then(() => {
                this.targetModal.classList.add(CLASSES.MODAL.VISIBLE);
                this.backdrop.node.classList.add(CLASSES.BACKDROP.VISIBLE);

                window.addEventListener('keyup', this.keyboardHandler);
                this.backdrop.node.addEventListener('click', this.modalHandler);
                closeBtn.addEventListener('click', this.modalHandler);

                document.getElementById('html').classList.add('html--modal--open'); // needed for input in modal bug on iOS

                this.isHidden = false;
            });
        } else {
            this.targetModal.classList.remove(CLASSES.MODAL.VISIBLE);
            this.backdrop.node.classList.remove(CLASSES.BACKDROP.VISIBLE);

            window.removeEventListener('keyup', this.keyboardHandler);
            this.backdrop.node.removeEventListener('click', this.modalHandler);
            closeBtn.removeEventListener('click', this.modalHandler);

            document.getElementById('html').classList.remove('html--modal--open'); // needed for input in modal bug on iOS

            this.isHidden = true;
        }
    }

    renderBackdrop() {
        const backdropNode = document.createElement('div');
        backdropNode.setAttribute('class', `${CLASSES.BACKDROP.DEFAULT}`);

        document.body.appendChild(backdropNode);

        this.backdrop = {
            node: backdropNode,
            namespace: 'BACKDROP'
        };
    }

    keyboardHandler(e) {
        if (e.keyCode === KEYCODE_ESCAPE) {
            this.modalHandler();
        }
    }

    titleClickHandler(e) {
        if (mq.matches) {
            const title = e.path[1].querySelector(`.${CLASSES.TITLE}`);
            const content = e.path[1].querySelector(`.${CLASSES.PARAGRAPH}`);

            if (parseInt(content.style.marginTop) < 0) {
                content.style.marginTop = 0;
                if (title.className.indexOf(`${CLASSES.OPEN}`) < 0) {
                    title.classList.add(`${CLASSES.OPEN}`);
                }
            } else {
                content.style.marginTop = `${-content.offsetHeight}px`;
                if (title.className.indexOf(`${CLASSES.OPEN}`) > -1) {
                    title.classList.remove(`${CLASSES.OPEN}`);
                }
            }
        }
    }

    resizeHandler() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {

            titles.forEach((title) => {
                const content = title.parentElement.querySelector(`.${CLASSES.PARAGRAPH}`);

                if (mq.matches) {
                    if (title.className.indexOf(`${CLASSES.OPEN}`) > -1) {
                        content.style.marginTop = 0;
                    } else {
                        content.style.marginTop = `${-content.offsetHeight}px`;
                    }
                } else {
                    content.style.marginTop = 0;
                }
            });


        }, 250);
    }

}
