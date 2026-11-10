<?php

declare(strict_types=1);

use AcMarche\Sepulture\DoctrineExtensions\AnyValue;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('doctrine', [
        'dbal' => [
            'connections' => [
                'default' => [
                    'url' => '%env(resolve:DATABASE_URL)%',
                    'charset' => 'utf8mb4',
                ],
            ],
        ],
        'orm' => [
            'entity_managers' => [
                'default' => [
                    'connection' => 'default',
                    'mappings' => [
                        'AcMarcheSepulture' => [
                            'is_bundle' => false,
                            'type' => 'attribute',
                            'dir' => '%kernel.project_dir%/src/AcMarche/Sepulture/src/Entity',
                            'prefix' => 'AcMarche\Sepulture\Entity',
                            'alias' => 'AcMarcheSepulture',
                        ],
                    ],
                    'dql' => [
                        'string_functions' => [
                            'any_value' => AnyValue::class,
                        ],
                    ],
                ],
            ],
        ],
    ]);
};
