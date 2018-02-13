const utils = {
    hasClass,
    getAncestor
};

/**
 * Does element have className
 * @param identifier
 * @param className
 * @returns Boolean
 */
function hasClass(identifier, className) {
    if (identifier instanceof HTMLElement) {
        return identifier.classList.contains(className);
    } else {
        return document.querySelector(identifier).classList.contains(className);
    }
}

/**
 * Get ancestor with ancestorClass of element
 * @param element
 * @param ancestorClass
 * @returns HTMLElement
 */
function getAncestor(element, ancestorClass) {
    while (element.parentElement) {
        if (hasClass(element, ancestorClass)) {
            return element;
        }
        element = element.parentElement;
    }
}

export default utils;