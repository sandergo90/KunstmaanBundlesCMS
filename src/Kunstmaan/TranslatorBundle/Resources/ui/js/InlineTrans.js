import 'rangy/lib/rangy-core.js'
import 'rangy/lib/rangy-classapplier.js'
import 'undo.js'
import xhr from './xhr';
import Medium from 'medium.js';

const TAG_NAME = 'inline-trans';

const CLASSES = {
    EDIT_WRAPPER: 'js-inline-trans-edit-wrapper',
    TRANS_BTN: 'js-inline-trans-btn'
}

let mediumInstances = [];

function InlineTrans() {
    const editMode = location.search.indexOf('inline-trans=true') >= 0;

    if (editMode) {
        enableInlineEditing();
    }

    // Wait for the toolbar to be visible
    if ('MutationObserver' in window) {
        var observer = new MutationObserver(function(mutations, me) {
            const wrapper = document.querySelectorAll(`.${CLASSES.EDIT_WRAPPER}`)[0];
            if (wrapper) {
                const transBtn = document.querySelectorAll(`.${CLASSES.TRANS_BTN}`)[0];

                transBtn.addEventListener('click', (e) => {
                    e.preventDefault();

                    if (editMode) {
                        saveTranslations(transBtn.dataset.href).then(done => {
                            toggleInlineMode(false);
                        });
                    }
                    else {
                        toggleInlineMode(!editMode);
                    }
                })

                me.disconnect(); // stop observing
                return;
            }
        });

        observer.observe(document, {
            childList: true,
            subtree: true
        });
    }
}

function toggleInlineMode(inline) {
    const currPage = window.location.href;
    window.location = updateUrlParameter(currPage, 'inline-trans', inline);
}

function enableInlineEditing() {
    const elements = document.querySelectorAll(TAG_NAME);

    elements.forEach((el) => {
        const medium = new Medium({
            element: el,
            mode: Medium.partialMode,
            attributes: null,
            tags: null,
            pasteAsText: false
        });

        mediumInstances.push(medium);

        // Prevent enter because it's creating a new inline-trans element.
        el.addEventListener('keypress', function(e) {
            if (e.which === 13 && !e.shiftKey) {
                e.preventDefault();
            }
        });
    });
}

function saveTranslations(url) {
    return new Promise((resolve, reject) => {
        if (mediumInstances.length > 0) {
            let translations = {};

            mediumInstances.forEach((instance) => {
                const value = instance.value();
                const keyword = instance.element.dataset.keyword;

                translations[keyword] = value;
                instance.destroy();

            });

            xhr.post(url, 'translations=' + JSON.stringify(translations)).then(request => {
                mediumInstances = [];

                resolve(1);
            });

        }
    });
}

// Add / Update a key-value pair in the URL query parameters
function updateUrlParameter(uri, key, value) {
    // remove the hash part before operating on the uri
    var i = uri.indexOf('#');
    var hash = i === -1 ? '' : uri.substr(i);
    uri = i === -1 ? uri : uri.substr(0, i);

    var re = new RegExp('([?&])' + key + '=.*?(&|$)', 'i');
    var separator = uri.indexOf('?') !== -1 ? '&' : '?';
    if (uri.match(re)) {
        uri = uri.replace(re, '$1' + key + '=' + value + '$2');
    } else {
        uri = uri + separator + key + '=' + value;
    }
    return uri + hash;  // finally append the hash as well
}

export default InlineTrans;
