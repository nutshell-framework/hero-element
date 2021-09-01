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
        /* @var \PageModel $objPage */
        global $objPage;

        // Clean the RTE output
        if ('xhtml' === $objPage->outputFormat) {
            $this->text = StringUtil::toXhtml($this->text);
        } else {
            $this->text = StringUtil::toHtml5($this->text);
        }

        // Add the static files URL to images
        if (TL_FILES_URL) {
            $path = Config::get('uploadPath').'/';
            $this->text = str_replace(' src="'.$path, ' src="'.TL_FILES_URL.$path, $this->text);
        }

        $this->Template->text = StringUtil::encodeEmail($this->text);
        $this->Template->addImage = false;

        // Add an content image
        if ($this->addImage && $this->singleSRC) {
            $objModel = FilesModel::findByUuid($this->singleSRC);

            if (is_file(TL_ROOT.'/'.$objModel->path)) {
                $this->singleSRC = $objModel->path;

                static::addImageToTemplate($this->Template, [
                    'singleSRC' => $objModel->path,
                    'size' => $size,
                ], null, null, $objModel);
            }
        }

        // Add an content image
        if ($this->heroBackgroundImage) {
            $objModel = FilesModel::findByUuid($this->heroBackgroundImage);

            if (is_file(TL_ROOT.'/'.$objModel->path)) {
                $this->Template->heroImage = $objModel->path;

                $image = new \stdClass();
                static::addImageToTemplate($image, [
                    'singleSRC' => $objModel->path,
                    'size' => $heroSize,
                ], null, null, $objModel);

                $this->Template->heroBackground = $image;
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
