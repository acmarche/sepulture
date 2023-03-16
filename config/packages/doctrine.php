<?php

declare(strict_types=1);

use AcMarche\Sepulture\DoctrineExtensions\AnyValue;
use Symfony\Config\DoctrineConfig;
use function Symfony\Component\DependencyInjection\Loader\Configurator\Env;

return static function (DoctrineConfig $doctrine) {

    $doctrine->dbal()
        ->connection('default')
        ->url(env('DATABASE_URL')->resolve())
        ->charset('utf8mb4');

    $emMda = $doctrine->orm()->entityManager('default');
    $emMda->connection('default');
    $emMda->mapping('AcMarcheSepulture')
        ->isBundle(false)
        ->type('attribute')
        ->dir('%kernel.project_dir%/src/AcMarche/Sepulture/src/Entity')
        ->prefix('AcMarche\Sepulture')
        ->alias('AcMarcheSepulture');
    $emMda->dql([
        'string_functions' => [
            'any_value' => AnyValue::class,
        ],
    ]);
};
