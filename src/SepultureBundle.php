<?php

namespace AcMarche\Sepulture;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class SepultureBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
