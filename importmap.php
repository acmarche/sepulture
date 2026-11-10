<?php

/**
 * Returns the importmap for this application.
 *
 * - "path" is a path inside the asset mapper system. Use the
 *     "debug:asset-map" command to see the full list of paths.
 *
 * - "entrypoint" (JavaScript only) set to true for any module that will
 *     be used as an "entrypoint" (and passed to the importmap() Twig function).
 *
 * The "importmap:require" command can be used to add new entries to this file.
 */
return [
    'app' => ['path' => './assets/app.js', 'entrypoint' => true],
    '@hotwired/stimulus' => ['version' => '3.2.2'],
    '@symfony/stimulus-bundle' => ['path' => './vendor/symfony/stimulus-bundle/assets/dist/loader.js'],
    '@hotwired/turbo' => ['version' => '8.0.23'],
    'bootstrap' => ['version' => '5.3.2'],
    '@popperjs/core' => ['version' => '2.11.8'],
    'bootstrap/dist/css/bootstrap.min.css' => ['version' => '5.3.2', 'type' => 'css'],
    '@tabler/icons-webfont/tabler-icons.min.css' => ['version' => '2.47.0', 'type' => 'css'],
    'stimulus-use' => ['version' => '0.52.2'],
    '@stimulus-components/lightbox' => ['version' => '4.0.0'],
    'stimulus-checkbox-select-all' => ['version' => '5.3.0'],
    'lightgallery/css/lightgallery.css' => ['version' => '2.8.2', 'type' => 'css'],
    'lightgallery' => ['version' => '2.8.2'],
    '@mati365/ckeditor5-symfony' => ['path' => '@mati365/ckeditor5-symfony/index.mjs'],
    'ckeditor5' => ['path' => './assets/vendor/ckeditor5/dist/browser/ckeditor5.js'],
    'ckeditor5/translations/fr.js' => ['path' => './assets/vendor/ckeditor5/dist/translations/fr.js'],
];
