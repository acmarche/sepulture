<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 7/11/18
 * Time: 9:00.
 */

namespace AcMarche\Sepulture\Security;

class SecurityData
{
    public static function getRoles()
    {
        $roles = [self::getRoleAdmin(), self::getRoleEditeur()];

        return array_combine($roles, $roles);
    }

    public static function getRoleAdmin()
    {
        return 'ROLE_SEPULTURE_ADMIN';
    }

    public static function getRoleEditeur()
    {
        return 'ROLE_SEPULTURE_EDITEUR';
    }
}
