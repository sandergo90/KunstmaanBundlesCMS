import legalCookieBar from './legalCookieBar';
import legalToggleSubscriber from './legalToggleSubscriber';
import notifications from './notification';
import scroll from './scroll';

document.addEventListener('DOMContentLoaded', () => {
    legalCookieBar.init();
    legalToggleSubscriber.init();
    notifications.init();
    scroll.init();
});
