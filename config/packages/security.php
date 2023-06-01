<?php

use AcMarche\Sepulture\Entity\User;
use AcMarche\Sepulture\Security\AppAuthenticator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $main = [
        'provider' => 'sepulture_user_provider',
        'logout' => [
            'path' => 'app_logout',
        ],
        'form_login' => [],
        'entry_point' => AppAuthenticator::class,
        'switch_user' => true,
        'custom_authenticator' => AppAuthenticator::class,
        'login_throttling' => [
            'max_attempts' => 6, //per minute...
        ],
    ];

    // focant en fin de
    /* @see PasswordHasherFactory.php */
    // $config['encode_as_base64'] = false;
    // $config['iterations'] = 1;
    $containerConfigurator->extension('security', [
        'password_hashers' => [
            'legacy' => [
                'algorithm' => 'sha512',
                'encode_as_base64' => false,
                'iterations' => 13,
            ],
            'AcMarche\Sepulture\Entity\Security\User' => [
                'algorithm' => 'auto',
                'migrate_from' => [
                    'legacy',
                ],
            ],
        ],
        'providers' => [
            'sepulture_user_provider' => [
                'entity' => [
                    'class' => User::class,
                    'property' => 'email',
                ],
            ],
        ],
        'firewalls' => [
            'main' => $main,
        ],
        'role_hierarchy' => [
            'ROLE_SEPULTURE_ADMIN' => ['ROLE_SEPULTURE_EDITEUR'],
        ],
    ]);
};
