import Lightbox from '@stimulus-components/lightbox';
import 'lightgallery/css/lightgallery.css';

/* stimulusFetch: 'lazy' */
/**
 * Opens the <a href> children of the element in a lightGallery lightbox.
 *
 * Usage: <div data-controller="lightbox"><a href="big.jpg"><img src="thumb.jpg"></a>…</div>
 * Extra lightGallery options can be passed with data-lightbox-options-value='{"…": …}'.
 */
export default class extends Lightbox {
    get defaultOptions() {
        return {
            download: false,
        };
    }
}
