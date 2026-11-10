<?php

namespace AcMarche\Sepulture;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class SepultureBundle extends AbstractBundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.php');
    }

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/packages/doctrine.php');
        $container->import('../config/packages/security.php');
        $container->import('../config/packages/twig.php');

        // Optional third-party bundles: only configure them once they are installed.
        if ($builder->hasExtension('liip_imagine')) {
            $container->import('../config/packages/imagine.php');
        }
        if ($builder->hasExtension('vich_uploader')) {
            $container->import('../config/packages/vich_uploader.php');
        }
        if ($builder->hasExtension('ckeditor5')) {
            $container->import('../config/packages/ckeditor5.php');
        }
    }
}
