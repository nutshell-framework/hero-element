<?php

declare(strict_types=1);

/*
 * This file is part of nutshell-framework/hero-element.
 *
 * (c) Erdmann & Freunde <https://erdmann-freunde.de>
 *
 * @license MIT
 */

use Contao\BackendUser;
use Contao\System;

$GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'][] = 'addText';
$GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'][] = 'addBackgroundImage';
$GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'][] = 'addBackgroundVideo';

$GLOBALS['TL_DCA']['tl_content']['palettes']['hero'] = '{type_legend},type,headline;'
                                                       .'{text_legend},addText;'
                                                       .'{image_legend},addImage;'
                                                       .'{background_legend},addBackgroundImage,addBackgroundVideo;'
                                                       .'{link_primary_legend},urlPrimary,targetPrimary,linkTitlePrimary,titleTextPrimary,relPrimary,linkClassPrimary;'
                                                       .'{link_secondary_legend},urlSecondary,targetSecondary,linkTitleSecondary,titleTextSecondary,relSecondary,linkClassSecondary;'
                                                       .'{template_legend:hide},customTpl;'
                                                       .'{protected_legend:hide},protected;'
                                                       .'{expert_legend:hide},guests,cssID,space;'
                                                       .'{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['subpalettes']['addText'] = 'text';
$GLOBALS['TL_DCA']['tl_content']['subpalettes']['addBackgroundImage'] = 'heroBackgroundImage,heroSize';
$GLOBALS['TL_DCA']['tl_content']['subpalettes']['addBackgroundVideo'] = 'heroBackgroundVideo';

$GLOBALS['TL_DCA']['tl_content']['fields']['addBackgroundImage'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_content']['addBackgroundImage'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true],
    'sql' => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['addBackgroundVideo'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_content']['addBackgroundVideo'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true],
    'sql' => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['heroBackgroundImage'] = [
    'exclude' => true,
    'inputType' => 'fileTree',
    'eval' => ['filesOnly' => true, 'fieldType' => 'radio', 'mandatory' => false, 'tl_class' => 'clr'],
    'load_callback' => [
        ['tl_content', 'setSingleSrcFlags'],
    ],
    'sql' => 'binary(16) NULL',
];

$GLOBALS['TL_DCA']['tl_content']['fields']['heroBackgroundVideo'] = [
    'exclude' => true,
    'inputType' => 'fileTree',
    'eval' => ['filesOnly' => true, 'fieldType' => 'checkbox', 'multiple' => true, 'mandatory' => false, 'tl_class' => 'clr'],
    'sql' => 'blob NULL',
];

$GLOBALS['TL_DCA']['tl_content']['fields']['heroSize'] = [
    'exclude' => true,
    'inputType' => 'imageSize',
    'reference' => &$GLOBALS['TL_LANG']['MSC'],
    'eval' => ['rgxp' => 'natural', 'includeBlankOption' => true, 'nospace' => true, 'helpwizard' => true, 'tl_class' => 'w50'],
    'options_callback' => static fn () => System::getContainer()->get('contao.image.sizes')->getOptionsForUser(BackendUser::getInstance()),
    'sql' => "varchar(255) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['urlPrimary'] = [
    'label' => &$GLOBALS['TL_LANG']['MSC']['url'],
    'exclude' => true,
    'search' => true,
    'inputType' => 'text',
    'eval' => ['rgxp' => 'url', 'decodeEntities' => true, 'maxlength' => 255, 'dcaPicker' => true, 'tl_class' => 'w50 wizard'],
    'sql' => "varchar(255) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['targetPrimary'] = [
    'label' => &$GLOBALS['TL_LANG']['MSC']['target'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'w50 m12'],
    'sql' => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['titleTextPrimary'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_content']['titleText'],
    'exclude' => true,
    'search' => true,
    'inputType' => 'text',
    'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
    'sql' => "varchar(255) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['linkTitlePrimary'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_content']['linkTitle'],
    'exclude' => true,
    'search' => true,
    'inputType' => 'text',
    'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
    'sql' => "varchar(255) NOT NULL default ''",
];
$GLOBALS['TL_DCA']['tl_content']['fields']['relPrimary'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_content']['rel'],
    'exclude' => true,
    'search' => true,
    'inputType' => 'text',
    'eval' => ['maxlength' => 64, 'tl_class' => 'w50'],
    'sql' => "varchar(64) NOT NULL default ''",
];
$GLOBALS['TL_DCA']['tl_content']['fields']['linkClassPrimary'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_content']['linkClass'],
    'exclude' => true,
    'search' => true,
    'inputType' => 'text',
    'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
    'sql' => "varchar(255) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['urlSecondary'] = [
    'label' => &$GLOBALS['TL_LANG']['MSC']['url'],
    'exclude' => true,
    'search' => true,
    'inputType' => 'text',
    'eval' => ['rgxp' => 'url', 'decodeEntities' => true, 'maxlength' => 255, 'dcaPicker' => true, 'tl_class' => 'w50 wizard'],
    'sql' => "varchar(255) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['targetSecondary'] = [
    'label' => &$GLOBALS['TL_LANG']['MSC']['target'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'w50 m12'],
    'sql' => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['titleTextSecondary'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_content']['titleText'],
    'exclude' => true,
    'search' => true,
    'inputType' => 'text',
    'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
    'sql' => "varchar(255) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['linkTitleSecondary'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_content']['linkTitle'],
    'exclude' => true,
    'search' => true,
    'inputType' => 'text',
    'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
    'sql' => "varchar(255) NOT NULL default ''",
];
$GLOBALS['TL_DCA']['tl_content']['fields']['relSecondary'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_content']['rel'],
    'exclude' => true,
    'search' => true,
    'inputType' => 'text',
    'eval' => ['maxlength' => 64, 'tl_class' => 'w50'],
    'sql' => "varchar(64) NOT NULL default ''",
];
$GLOBALS['TL_DCA']['tl_content']['fields']['linkClassSecondary'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_content']['linkClass'],
    'exclude' => true,
    'search' => true,
    'inputType' => 'text',
    'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
    'sql' => "varchar(255) NOT NULL default ''",
];
$GLOBALS['TL_DCA']['tl_content']['fields']['addText'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_content']['addText'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true],
    'sql' => "char(1) NOT NULL default ''",
];
