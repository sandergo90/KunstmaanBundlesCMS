import {each} from 'underscore';
import 'rangy/lib/rangy-core.js'
import 'rangy/lib/rangy-classapplier.js'
import 'undo.js'
import Medium from 'medium.js';

const CLASSES = {};

function InlineTrans() {
    each(document.querySelectorAll('inline-trans'),
        (el) => {
            const medium = new Medium({
                element: el,
                mode: Medium.inlineRichMode,
                placeholder: 'Your Article',
                attributes: null,
                tags: null,
                pasteAsText: false
            });

            el.highlight = function() {
                if (document.activeElement !== article) {
                    medium.select();
                }
            };
        });
}

export default InlineTrans;
