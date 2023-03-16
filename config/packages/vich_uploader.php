<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension(
        'vich_uploader',
        [
            'db_driver' => 'orm',
        ]
    );
    $containerConfigurator->extension(
        'vich_uploader',
        [
            'mappings' => [
                'ossuaire' => [
                    'uri_prefix' => '/images/ossuaires',
                    'upload_destination' => '%kernel.project_dir%/public/images/ossuaires',
                ],
            ],
        ]
    );
};

 //     namer: Vich\UploaderBundle\Naming\SmartUniqueNamer