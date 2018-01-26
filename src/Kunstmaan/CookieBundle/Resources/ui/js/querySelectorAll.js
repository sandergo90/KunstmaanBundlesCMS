export default function(selector) {
    return Array.prototype.slice.call(document.querySelectorAll(selector));
}
