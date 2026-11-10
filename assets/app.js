import './stimulus_bootstrap.js';

// Bootstrap: CSS + JS (modals, collapse, dropdowns, dismissible alerts used by the templates)
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';

// Tabler icons webfont (<i class="ti ti-…">)
import '@tabler/icons-webfont/tabler-icons.min.css';

import './styles/app.css';

// CKEditor 5: loaded on demand, only on pages that render a <cke5-*> element, so a failure
// to load it (e.g. a bad MIME type for .mjs) can't break Bootstrap/Stimulus on every page.
// Checked on turbo:load too, because Turbo Drive doesn't re-run this module on navigation.
let ckeditorLoading = null;
const loadCkeditorIfNeeded = () => {
    if (ckeditorLoading || !document.querySelector('cke5-editor, cke5-context, cke5-editable, cke5-ui-part')) {
        return;
    }
    ckeditorLoading = import('@mati365/ckeditor5-symfony').catch((error) => {
        ckeditorLoading = null;
        console.error('CKEditor 5 failed to load', error);
    });
};
loadCkeditorIfNeeded();
document.addEventListener('turbo:load', loadCkeditorIfNeeded);
document.addEventListener('turbo:frame-load', loadCkeditorIfNeeded);
