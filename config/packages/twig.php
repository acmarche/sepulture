<?php

declare(strict_types=1);

use Symfony\Config\TwigConfig;

return static function (TwigConfig $twig) {
    $twig
        ->formThemes(['bootstrap_5_layout.html.twig'])
        ->path('%kernel.project_dir%/src/AcMarche/Sepulture/templates', 'AcMarcheSepulture')
        ->global('bootcdn')->value('https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css');
    $twig->global('ga_tracking')->value('%env(GA_TRACKING)%');
    $twig->global('sepulture_ville')->value('%env(SEPULTURE_VILLE)%');
};