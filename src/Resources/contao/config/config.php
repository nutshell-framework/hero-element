<?php

declare(strict_types=1);

/*
 * Hero Element for Contao Open Source CMS.
 *
 * @copyright  Copyright (c) 2021, Erdmann & Freunde
 * @author     Dennis Erdmann
 * @author     Richard Henkenjohann
 * @license    MIT
 * @link       http://github.com/nutshell-framework/hero-element
 */

use Nutshell\HeroElement\Content\Hero;

/*
 * EuF Hero ContentElement
 */
Contao\ArrayUtil::arrayInsert((
    $GLOBALS['TL_CTE']['media'],
    4,
    [
        'hero' => Hero::class,
    ]
);
