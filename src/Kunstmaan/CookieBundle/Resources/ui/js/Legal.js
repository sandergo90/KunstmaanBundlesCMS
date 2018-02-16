import legalCookieBar from './legalCookieBar';
import legalToggleSubscriber from './legalToggleSubscriber';
import notifications from './notification';

document.addEventListener('DOMContentLoaded', () => {
    legalCookieBar.init();
    legalToggleSubscriber.init();
    notifications.init();
});
