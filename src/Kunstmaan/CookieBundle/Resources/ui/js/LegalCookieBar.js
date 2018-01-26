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
    }
};

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
}
