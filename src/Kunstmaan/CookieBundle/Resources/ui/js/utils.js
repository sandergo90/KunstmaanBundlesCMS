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
        if (identifier) {
            return identifier.classList.contains(className);
        }
        return false;
    } else {
        if (document.querySelector(identifier)) {
            return document.querySelector(identifier).classList.contains(className);
        }
        return false;
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