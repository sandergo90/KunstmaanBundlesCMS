import 'element-closest'; // polyfill for closest IE11
import 'ios-inner-height'; // window.innerHeight bug mobile Safari
import 'picturefill'; // polyfill for sourceset IE11
import 'promise-polyfill/src/polyfill'; // polyfill for promise IE11
import LegalCookieBar from './LegalCookieBar';

document.addEventListener('DOMContentLoaded', () => {
    new LegalCookieBar();
});
