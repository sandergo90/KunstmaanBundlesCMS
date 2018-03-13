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
        try {
            return  document.querySelector(identifier).classList.contains(className);
        } catch (err) {
            throw Error('Identifier "' + identifier + '" has an invalid format');
        }
    }
}

/**
 * Get ancestor with ancestorClass of element
 * @param identifier
 * @param ancestorClass
 * @returns HTMLElement
 */
function getAncestor(identifier, ancestorClass) {
    let element = identifier;

    if (!(identifier instanceof HTMLElement)) {
        try {
            element = document.querySelector(identifier);
        } catch (err) {
            throw Error('Identifier "' + identifier + '" has an invalid format');
        }
    }

    let target = element;

    if (element instanceof HTMLElement) {
        while (element.parentElement) {
            if (hasClass(element, ancestorClass)) {
                target = element;
            }
            element = element.parentElement;
        }
    }

    return target;
}

export default utils;