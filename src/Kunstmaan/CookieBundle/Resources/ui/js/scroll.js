import querySelectorAll from './querySelectorAll';

const scroll = {
    init
};

const CLASSES = {
    CONTAINER: 'js-legal__aside__navigation',
    CONTENT: 'legal-modal__content',
    TOTOP: 'totop-pp',
    TOTOPVISIBLE: 'totop-pp--shown'
};

let anchorLists;
let content;
let totop;

/**
 * Init scrolling
 */
function init() {
    anchorLists = querySelectorAll(`.${CLASSES.CONTAINER}`);
    content = document.querySelector(`.${CLASSES.CONTENT}`);
    totop = content.querySelector(`.${CLASSES.TOTOP}`);
    totop.addEventListener('click', toTopHandler);

    anchorLists.forEach((anchorList) => {
        const anchors = Array.prototype.slice.call(anchorList.querySelectorAll('li > a'));

        anchors.forEach((anchor) => {
            anchor.addEventListener('click', clickHandler);
        });

    });

    content.addEventListener('scroll', scrollHandler);
}

/**
 * Scroll handler
 */
function scrollHandler() {
    if (content.scrollTop >= 500) {
        totop.classList.add(`${CLASSES.TOTOPVISIBLE}`);
    } else {
        totop.classList.remove(`${CLASSES.TOTOPVISIBLE}`);
    }
}

/**
 * Click handler for anchors
 * @param event
 */
function clickHandler(event) {
    event.preventDefault();

    const targetOffset = document.querySelector(event.currentTarget.getAttribute('href')).offsetTop;

    smooth_scroll_to(content, targetOffset, 500);
}

/**
 * Handler for toTop on click
 */
function toTopHandler() {
    smooth_scroll_to(content, 0, 500);
}

/**
 * Smooth scroll (based on http://en.wikipedia.org/wiki/Smoothstep)
 * @param element
 * @param target
 * @param duration
 */
function smooth_scroll_to(element, target, duration) {
    target = Math.round(target);
    duration = Math.round(duration);

    if (duration < 0) {
        return Promise.reject("bad duration");
    }

    if (duration === 0) {
        element.scrollTop = target;
        return Promise.resolve();
    }

    const start_time = Date.now();
    const end_time = start_time + duration;

    const start_top = element.scrollTop;
    const distance = target - start_top;

    return new Promise(function(resolve, reject) {
        let previous_top = element.scrollTop;

        let scroll_frame = function() {
            if (element.scrollTop != previous_top) {
                reject("interrupted");
                return;
            }

            const now = Date.now();
            const point = smooth_step(start_time, end_time, now);
            const frameTop = Math.round(start_top + (distance * point));

            element.scrollTop = frameTop;

            if (now >= end_time) {
                resolve();
                return;
            }

            if (element.scrollTop === previous_top && element.scrollTop !== frameTop) {
                resolve();
                return;
            }

            previous_top = element.scrollTop;

            // schedule next frame for execution
            setTimeout(scroll_frame, 0);
        };

        // bootstrap the animation process
        setTimeout(scroll_frame, 0);
    });
}

/**
 * Smooth step (based on http://en.wikipedia.org/wiki/Smoothstep)
 * @param start
 * @param end
 * @param point
 */
function smooth_step(start, end, point) {
    if (point <= start) {
        return 0;
    }

    if (point >= end) {
        return 1;
    }

    const x = (point - start) / (end - start); // interpolation

    return x * x * (3 - 2 * x);
}

export default scroll;