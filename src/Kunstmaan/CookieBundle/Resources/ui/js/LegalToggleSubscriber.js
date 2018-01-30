import xhr from './Xhr';
import querySelectorAll from './querySelectorAll';

const CLASSES = {
    TOGGLES: 'js-legal-toggle',
    TRIGGER: {
        TOGGLE: 'js-legal-toggle-subscriber',
        TOGGLE_ALL: 'js-legal-toggle-all-subscriber'
    }
};

export default class LegalToggleSubscriber {
    constructor() {
        this.addToggleEventListener();
        this.addToggleAllEventListener();
        triggerCookieEvent();
    }

    addToggleEventListener() {
        querySelectorAll(`.${CLASSES.TRIGGER.TOGGLE}`).forEach((el) => {
            el.addEventListener('click', this.toggleCookies);
        });
    }

    addToggleAllEventListener() {
        querySelectorAll(`.${CLASSES.TRIGGER.TOGGLE_ALL}`).forEach((el) => {
            el.addEventListener('click', this.toggleAllCookies);
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
                return triggerCookieEvent();
            });
        }
    }

    toggleAllCookies(event) {
        event.preventDefault();

        const element = event.target;
        const url = element.dataset.href;

        if (url) {
            // Build data params
            xhr.post(url).then((request) => {
                return triggerCookieEvent();
            });
        }
    }
}

function triggerCookieEvent() {
    const cookiearray = document.cookie.split(';');

    for (let i = 0; i < cookiearray.length; i++) {
        const name = cookiearray[i].split('=')[0].trim();
        const value = decodeURIComponent(cookiearray[i].split('=')[1]);

        if (name === 'legal_cookie') {
            const parsed = JSON.parse(value);

            if (parsed.cookies !== undefined) {
                const cookies = parsed.cookies
                ;

                for (let k in cookies) {
                    if (cookies.hasOwnProperty(k)) {
                        dataLayer.push({
                            'event': 'enableCookie',
                            'attributes': {
                                'cookieName': k,
                                'cookieValue': cookies[k]
                            }
                        });
                    }
                }
            }
        }
    }
}
