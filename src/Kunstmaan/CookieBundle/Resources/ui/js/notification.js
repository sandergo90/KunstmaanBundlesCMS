import querySelectorAll from './querySelectorAll';
import utils from './utils';

const notifications = {
    init,
    showNotification
};

const CLASSES = {
    CONTAINER: 'js-notification',
    SHOWN: 'notification--shown'
};

let notification;

/**
 * Init scrolling
 */
function init() {
    notification = querySelectorAll(`.${CLASSES.CONTAINER}`)[0];
}

/**
 * Toggle notification visibility and animation
 */
function showNotification() {
    if (notification instanceof HTMLElement) {
        if (!utils.hasClass(notification, `${CLASSES.SHOWN}`)) {
            notification.classList.add(`${CLASSES.SHOWN}`);
        }

        setTimeout(function() {
            if (utils.hasClass(notification, `${CLASSES.SHOWN}`)) {
                notification.classList.remove(`${CLASSES.SHOWN}`);
            }
        }, 3000);
    }
}

export default notifications;