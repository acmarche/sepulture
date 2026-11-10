<?php

declare(strict_types=1);

use AcMarche\Sepulture\Entity\User;
use AcMarche\Sepulture\Security\AppAuthenticator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('security', [
        'providers' => [
            'sepulture_user_provider' => [
                'entity' => [
                    'class' => User::class,
                    'property' => 'email',
                ],
            ],
        ],
        'role_hierarchy' => [
            'ROLE_SEPULTURE_ADMIN' => ['ROLE_SEPULTURE_EDITEUR'],
        ],
        // Firewall order matters (first match wins): "dev" must come before "main".
        'firewalls' => [
            'dev' => [
                'pattern' => '^/(_profiler(/|$)|(_wdt|assets|build)/)',
                'security' => false,
            ],
            'main' => [
                'provider' => 'sepulture_user_provider',
                'form_login' => [],
                'custom_authenticators' => [AppAuthenticator::class],
                'entry_point' => AppAuthenticator::class,
                'logout' => [
                    'path' => 'app_logout',
                ],
                'login_throttling' => [
                    'max_attempts' => 6, // per minute
                ],
                'remember_me' => [
                    'secret' => '%kernel.secret%',
                    'lifetime' => 604800,
                    'path' => '/',
                    'always_remember_me' => true,
                ],
            ],
        ],
    ]);
};
