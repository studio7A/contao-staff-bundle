<?php

declare(strict_types=1);

namespace Studio7A\ContaoStaffBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class Studio7AContaoStaffBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
