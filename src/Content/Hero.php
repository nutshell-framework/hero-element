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

namespace Nutshell\HeroElement\Content;

use Contao\Config;
use Contao\ContentElement;
use Contao\FilesModel;
use Contao\StringUtil;
use Contao\System;
use Contao\FrontendTemplate;

class Hero extends ContentElement
{
    /**
     * Template.
     *
     * @var string
     */
    protected $strTemplate = 'ce_hero';

    /**
     * Generate the content element.
     */
    protected function compile()
    {
        // Add the static files URL to images
        if ($staticUrl = System::getContainer()->get('contao.assets.files_context')->getStaticUrl()) {
            $path = Config::get('uploadPath') . '/';
            $this->text = str_replace(' src="' . $path, ' src="' . $staticUrl . $path, $this->text);
        }

        $this->Template->text = StringUtil::encodeEmail($this->text);
        $this->Template->addImage = false;

        // Add an content image
        if ($this->addImage && $this->singleSRC) {
            $objModel = FilesModel::findByUuid($this->singleSRC);

            if ($objModel !== null && is_file(System::getContainer()->getParameter('kernel.project_dir') . '/' . $objModel->path)) {
                $this->singleSRC = $objModel->path;

                $figure = System::getContainer()
                    ->get('contao.image.studio')
                    ->createFigureBuilder()
                    ->from($this->singleSRC)
                    ->setSize($this->size)
                    ->buildIfResourceExists();


                if (null !== $figure)
                {
                    $figure->applyLegacyTemplateData($this->Template,'', $this->floating);
                }
            }
        }

        // Add an background image
        if ($this->heroBackgroundImage) {
            $objModel = FilesModel::findByUuid($this->heroBackgroundImage);

            if ($objModel !== null && is_file(System::getContainer()->getParameter('kernel.project_dir') . '/' . $objModel->path)) {
                $this->Template->heroImage = $objModel->path;

                $figure = System::getContainer()
                    ->get('contao.image.studio')
                    ->createFigureBuilder()
                    ->from($this->heroBackgroundImage)
                    ->setSize($this->heroSize)
                    ->buildIfResourceExists();


                if (null !== $figure)
                {
                    $this->Template->heroBackground = (object) $figure->getLegacyTemplateData();
                }
            }
        }

        $this->Template->targetPrimary = '';
        $this->Template->relPrimary = '';
        $this->Template->targetSecondary = '';
        $this->Template->relSecondary = '';

        // Override the link target
        if ($this->targetPrimary) {
            $this->Template->targetPrimary = ' target="_blank"';
            $this->Template->relPrimary = ' rel="noreferrer noopener"';
        }

        if ($this->targetSecondary) {
            $this->Template->targetSecondary = ' target="_blank"';
            $this->Template->relSecondary = ' rel="noreferrer noopener"';
        }
    }
}
