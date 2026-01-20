<?php

declare(strict_types=1);

/*
 * This file is part of nutshell-framework/hero-element.
 *
 * (c) Erdmann & Freunde <https://erdmann-freunde.de>
 *
 * @license MIT
 */

namespace Nutshell\HeroElement\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Config\ConfigInterface;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Nutshell\HeroElement\NutshellHeroElement;

class Plugin implements BundlePluginInterface
{
    /**
     * Gets a list of autoload configurations for this bundle.
     *
     * @return array<ConfigInterface>
     */
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(NutshellHeroElement::class)
                ->setLoadAfter(
                    [
                        ContaoCoreBundle::class,
                    ],
                ),
        ];
    }
}
