<?php
/**
 * This file is part of sepulture application.
 *
 * @author jfsenechal <jfsenechal@gmail.com>
 * @date 6/11/19
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AcMarche\Sepulture\Service;

class Rw
{
    public static function a1945(): array
    {
        return [
            'Monument à préserver dans son emplacement à revendre ou réaffectation communale',
            'Monument à préserver mais qui peut être déplacé en zone conservatoire',
            'Monument dont l’élimination est autorisée',
            'Monument demandant une expertise de terrain',
        ];
    }

    public static function sihl(): array
    {
        return [
            'Monument à préserver dans son emplacement',
            'Monument à préserver dans son emplacement à revendre ou réaffectation communale',
            'Monument à préserver mais qui peut être déplacé en zone conservatoire',
        ];
    }
}
