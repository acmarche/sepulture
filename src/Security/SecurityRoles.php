<?php

namespace AcMarche\Sepulture\Security;

class SecurityRoles
{
    public function roles(): array
    {
        return [self::getRoleAdmin(), self::getRoleEditeur()];
    }

    public static function getRoleAdmin(): string
    {
        return 'ROLE_SEPULTURE_ADMIN';
    }

    public static function getRoleEditeur(): string
    {
        return 'ROLE_SEPULTURE_EDITEUR';
    }
}
