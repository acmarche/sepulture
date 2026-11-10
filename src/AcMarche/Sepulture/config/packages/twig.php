<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('twig', [
        'form_themes' => ['bootstrap_5_layout.html.twig'],
        'paths' => [
            '%kernel.project_dir%/src/AcMarche/Sepulture/templates' => 'AcMarcheSepulture',
            // Custom error pages: user paths are registered before bundle paths, so these win over TwigBundle's.
            '%kernel.project_dir%/src/AcMarche/Sepulture/templates/bundles/TwigBundle' => 'Twig',
        ],
        'globals' => [
            'bootcdn' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css',
            'ga_tracking' => '%env(GA_TRACKING)%',
            'sepulture_ville' => '%env(SEPULTURE_VILLE)%',
        ],
    ]);
};
