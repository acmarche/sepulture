<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {

    $containerConfigurator->extension(
        'liip_imagine',
        [
            'filter_sets' => [
                'cache' => null,
                'my_thumb' => [
                    'quality' => 100,
                    'cache' => 'default',
                    'filters' => ['thumbnail' => ['size' => [120, 90], 'mode' => 'outbound']],
                ],
                'edit_thumb' => [
                    'quality' => 100,
                    'cache' => 'default',
                    'filters' => ['thumbnail' => ['size' => [200, 150], 'mode' => 'outbound']],
                ],
                'zoom_thumb' => [
                    'quality' => 100,
                    'cache' => 'default',
                    'filters' => [
                        'thumbnail' =>
                            ['size' => [1200, 900], 'mode' => 'inset'],
                        'watermark_image' => [
                            # Relative path to the watermark file (prepended with "%kernel.root_dir%/")
                            'image' => 'public/bundles/sepulture/images/watermark.png',
                            # Size of the watermark relative to the origin images size
                            'size' => '0.5',
                            # Position: One of topleft,top,topright,left,center,right,bottomleft,bottom,bottomright
                            'position' => 'bottomright',
                        ],
                    ],
                ],
                'acmarche_sepulture_thumb' => [
                    'quality' => 100,
                    'cache' => 'default',
                    'filters' => ['thumbnail' => ['size' => [120, 90], 'mode' => 'outbound']],
                ],
                'acmarche_sepulture_edit_thumb' => [
                    'quality' => 100,
                    'cache' => 'default',
                    'filters' => ['thumbnail' => ['size' => [200, 150], 'mode' => 'outbound']],
                ],
                'acmarche_sepulture_zoom_thumb' => [
                    'quality' => 100,
                    'cache' => 'default',
                    'filters' => [
                        'thumbnail' =>
                            ['size' => [1200, 900], 'mode' => 'inset'],
                        'watermark_image' => [
                            # Relative path to the watermark file (prepended with "%kernel.root_dir%/")
                            'image' => 'public/bundles/sepulture/images/watermark.png',
                            # Size of the watermark relative to the origin images size
                            'size' => '0.5',
                            # Position: One of topleft,top,topright,left,center,right,bottomleft,bottom,bottomright
                            'position' => 'bottomright',
                        ],
                    ],
                ],
            ],
        ]
    );
};
