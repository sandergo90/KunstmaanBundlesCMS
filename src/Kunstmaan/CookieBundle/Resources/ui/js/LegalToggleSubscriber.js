import xhr from './Xhr';
import querySelectorAll from './querySelectorAll';

const CLASSES = {
    TOGGLES: 'js-legal-toggle',
    TRIGGER: {
        CLICK: 'js-legal-toggle-subscriber'
    }
};

export default class LegalToggleSubscriber {
    constructor() {
        this.addToggleEventListener();
    }

    addToggleEventListener() {
        querySelectorAll(`.${CLASSES.TRIGGER.CLICK}`).forEach((el) => {
            el.addEventListener('click', this.toggleCookies);
        });
    }

    toggleCookies(event) {
        event.preventDefault();

        const element = event.target;
        const url = element.dataset.href;

        let data = '';
        querySelectorAll(`.${CLASSES.TOGGLES}`).forEach((el) => {
            data += `${el.attributes.rel.value}=${el.checked}&`;
        });

        if (url) {
            // Build data params
            xhr.post(url, data).then((request) => {
                alert('Saved');
            });
        }
    }
}
