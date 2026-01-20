<?php

declare(strict_types=1);

/*
 * This file is part of nutshell-framework/hero-element.
 *
 * (c) Erdmann & Freunde <https://erdmann-freunde.de>
 *
 * @license MIT
 */

use Contao\ArrayUtil;
use Nutshell\HeroElement\Content\Hero;

/*
 * EuF Hero ContentElement
 */
ArrayUtil::arrayInsert(
    $GLOBALS['TL_CTE']['media'],
    4,
    [
        'hero' => Hero::class,
    ],
);
