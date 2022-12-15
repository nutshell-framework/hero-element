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
use Contao\File;
use Contao\Image;

class Hero extends ContentElement
{
    /**
     * Template.
     *
     * @var string
     */
    protected $strTemplate = 'ce_hero';

    /**
     * Files object
     * @var Collection|FilesModel
     */
    protected $objFiles;

    /**
     * Return if there are no files
     *
     * @return string
     */
    public function generate()
    {
        $source = StringUtil::deserialize($this->heroBackgroundVideo);


        if (!empty($source) || \is_array($source))
        {
            $objFiles = FilesModel::findMultipleByUuidsAndExtensions($source, array('mp4', 'm4v', 'mov', 'wmv', 'webm', 'ogv', 'm4a', 'mp3', 'wma', 'mpeg', 'wav', 'ogg'));

            if ($objFiles !== null)
            {
                $request = System::getContainer()->get('request_stack')->getCurrentRequest();

                // Display a list of files in the back end
                if ($request && System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest($request))
                {
                    $return = '<ul>';

                    while ($objFiles->next())
                    {
                        $objFile = new File($objFiles->path);
                        $return .= '<li>' . Image::getHtml($objFile->icon, '', 'class="mime_icon"') . ' <span>' . $objFile->name . '</span> <span class="size">(' . $this->getReadableSize($objFile->size) . ')</span></li>';
                    }

                    $return .= '</ul>';

                    if ($this->headline)
                    {
                        $return = '<' . $this->hl . '>' . $this->headline . '</' . $this->hl . '>' . $return;
                    }

                    return $return;
                }

                $this->objFiles = $objFiles;
            }
        }

        return parent::generate();
    }

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


                if (null !== $figure) {
                    $figure->applyLegacyTemplateData($this->Template, '', $this->floating);
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


                if (null !== $figure) {
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

        // VideoBackground
        if ($this->heroBackgroundVideo) {
            $objFiles = $this->objFiles;

            /** @var FilesModel $objFirst */
            $objFirst = $objFiles->current();

            // Pre-sort the array by preference
            if (\in_array($objFirst->extension, array('mp4', 'm4v', 'mov', 'wmv', 'webm', 'ogv'))) {
                $this->Template->isVideo = true;

                $arrFiles = array('webm'=>null, 'mp4'=>null, 'm4v'=>null, 'mov'=>null, 'wmv'=>null, 'ogv'=>null);
            }

            // Pass File objects to the template
            foreach ($objFiles as $objFileModel) {
                $objFile = new File($objFileModel->path);
                $arrFiles[$objFile->extension] = $objFile;
            }

            $this->Template->files = array_values(array_filter($arrFiles));
        }
    }
}
