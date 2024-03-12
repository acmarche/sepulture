<?php

use AcMarche\Sepulture\Entity\User;
use AcMarche\Sepulture\Security\AppAuthenticator;
use Symfony\Config\SecurityConfig;

return static function (SecurityConfig $security) {

    $security->provider('sepulture_user_provider')
        ->entity()
        ->class(User::class)
        ->property('email');

    // @see Symfony\Config\Security\FirewallConfig
    $main = [
        'provider' => 'sepulture_user_provider',
        'logout' => [
            'path' => 'app_logout',
        ],
        'form_login' => [],
        'entry_point' => AppAuthenticator::class,
        'login_throttling' => [
            'max_attempts' => 6, // per minute...
        ],
        'remember_me' => [
            'secret' => '%kernel.secret%',
            'lifetime' => 604800,
            'path' => '/',
            'always_remember_me' => true,
        ],
    ];

    $authenticators = [AppAuthenticator::class];

    $main['custom_authenticators'] = $authenticators;
    $security->roleHierarchy('ROLE_SEPULTURE_ADMIN', ['ROLE_SEPULTURE_EDITEUR']);
    $security->firewall('main', $main);
};
