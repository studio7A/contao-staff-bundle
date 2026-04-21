<?php

declare(strict_types=1);

namespace Studio7A\ContaoStaffBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Studio7A\ContaoStaffBundle\Studio7AContaoStaffBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(Studio7AContaoStaffBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }
}
