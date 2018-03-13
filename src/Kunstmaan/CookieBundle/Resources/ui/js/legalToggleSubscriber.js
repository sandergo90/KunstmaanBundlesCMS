import xhr from './Xhr';
import querySelectorAll from './querySelectorAll';
import notifications from './notification';
import legalCookieBar from './legalCookieBar';

const legalToggleSubscriber = {
    init,
    initSingleCookieToggles
};

const CLASSES = {
    TOGGLES: 'js-legal-toggle',
    TRIGGER: {
        TOGGLE: 'js-legal-toggle-subscriber',
        TOGGLE_ALL: 'js-legal-toggle-all-subscriber'
    }
};

let singleCookieToggles;
let allCookieToggles;

/**
 * Init cookie toggles
 */
function init() {
    initAllCookieToggles();
    initSingleCookieToggles();
    triggerCookieEvent();
}

/**
 * Init all cookies toggles
 */
function initAllCookieToggles() {
    allCookieToggles = querySelectorAll(`.${CLASSES.TRIGGER.TOGGLE_ALL}`);

    allCookieToggles.forEach((el) => {
        el.addEventListener('click', toggleAllCookiesHandler);
    });
}

/**
 * Init single cookies toggles
 */
function initSingleCookieToggles() {
    singleCookieToggles = querySelectorAll(`.${CLASSES.TRIGGER.TOGGLE}`);

    singleCookieToggles.forEach((el) => {
        el.addEventListener('click', toggleCookiesHandler);
    });
}

/**
 * Cookie event trigger
 */
function triggerCookieEvent() {
    const cookieArray = document.cookie.split(';');

    for (let i = 0; i < cookieArray.length; i++) {
        const name = cookieArray[i].split('=')[0].trim();
        const value = decodeURIComponent(cookieArray[i].split('=')[1]);

        if (name === 'legal_cookie') {
            const parsed = JSON.parse(value);

            if (parsed.cookies !== undefined) {
                const cookies = parsed.cookies;

                for (let j in cookies) {
                    if (cookies.hasOwnProperty(j)) {
                        if (dataLayer) {
                            dataLayer.push({
                                'event': 'enableCookie',
                                'attributes': {
                                    'cookieName': j,
                                    'cookieValue': cookies[j]
                                }
                            });
                        }
                    }
                }
            }
        }
    }
}

/**
 * Single cookie Handler on click
 * @param event
 */
function toggleCookiesHandler(event) {
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
            notifications.showNotification();
            return triggerCookieEvent();
        });
    }
}

/**
 * All cookies Handler on click
 * @param event
 */
function toggleAllCookiesHandler(event) {
    event.preventDefault();

    const element = event.target;
    const url = element.dataset.href;

    if (url) {
        // Build data params
        xhr.post(url).then((request) => {
            notifications.showNotification();
            return triggerCookieEvent();
        });
    }

    legalCookieBar.toggleCookieBar();
}

export default legalToggleSubscriber;