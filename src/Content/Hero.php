<?php

declare(strict_types=1);

/*
 * This file is part of nutshell-framework/hero-element.
 *
 * (c) Erdmann & Freunde <https://erdmann-freunde.de>
 *
 * @license MIT
 */

namespace Nutshell\HeroElement\Content;

use Contao\Config;
use Contao\ContentElement;
use Contao\File;
use Contao\FilesModel;
use Contao\Image;
use Contao\Model\Collection;
use Contao\StringUtil;
use Contao\System;

class Hero extends ContentElement
{
    /**
     * Template.
     *
     * @var string
     */
    protected $strTemplate = 'ce_hero';

    /**
     * Files object.
     *
     * @var Collection<FilesModel>|null
     */
    protected $objFiles;

    /**
     * Return if there are no files.
     *
     * @return string
     */
    #[\Override]
    public function generate()
    {
        $source = StringUtil::deserialize($this->heroBackgroundVideo);

        if (!empty($source) || \is_array($source)) {
            $objFiles = FilesModel::findMultipleByUuidsAndExtensions($source, ['mp4', 'm4v', 'mov', 'wmv', 'webm', 'ogv']);

            if (null !== $objFiles) {
                $request = System::getContainer()->get('request_stack')->getCurrentRequest();

                // Display a list of files in the back end
                if ($request && System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest($request)) {
                    $return = '<ul>';

                    while ($objFiles->next()) {
                        $objFile = new File($objFiles->path);
                        $return .= '<li>'.Image::getHtml($objFile->icon, '', 'class="mime_icon"').' <span>'.$objFile->name.'</span> <span class="size">('.$this->getReadableSize($objFile->size).')</span></li>';
                    }

                    $return .= '</ul>';

                    if ($this->headline) {
                        $return = '<'.$this->hl.'>'.$this->headline.'</'.$this->hl.'>'.$return;
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
    protected function compile(): void
    {
        // Add the static files URL to images
        if ($this->text && $staticUrl = System::getContainer()->get('contao.assets.files_context')->getStaticUrl()) {
            $path = Config::get('uploadPath').'/';
            $this->text = str_replace(' src="'.$path, ' src="'.$staticUrl.$path, $this->text);
        }

        $this->Template->text = StringUtil::encodeEmail($this->text);
        $this->Template->addImage = false;

        // Add an content image
        if ($this->addImage && $this->singleSRC) {
            $objModel = FilesModel::findByUuid($this->singleSRC);

            if (null !== $objModel && is_file(System::getContainer()->getParameter('kernel.project_dir').'/'.$objModel->path)) {
                $this->singleSRC = $objModel->path;

                $figure = System::getContainer()
                    ->get('contao.image.studio')
                    ->createFigureBuilder()
                    ->from($this->singleSRC)
                    ->setSize($this->size)
                    ->setOverwriteMetadata($this->objModel->getOverwriteMetadata())
                    ->buildIfResourceExists()
                ;

                if (null !== $figure) {
                    $figure->applyLegacyTemplateData($this->Template, '', $this->floating);
                }
            }
        }

        // Add an background image
        if ($this->heroBackgroundImage) {
            $objModel = FilesModel::findByUuid($this->heroBackgroundImage);

            if (null !== $objModel && is_file(System::getContainer()->getParameter('kernel.project_dir').'/'.$objModel->path)) {
                $this->Template->heroImage = $objModel->path;

                $figure = System::getContainer()
                    ->get('contao.image.studio')
                    ->createFigureBuilder()
                    ->from($this->heroBackgroundImage)
                    ->setSize($this->heroSize)
                    ->buildIfResourceExists()
                ;

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
        if ($this->heroBackgroundVideo && null !== $this->objFiles) {
            $this->Template->isVideo = true;

            // Pre-sort the array by preference
            $arrFiles = ['webm' => null, 'mp4' => null, 'm4v' => null, 'mov' => null, 'wmv' => null, 'ogv' => null];
            $arrSources = $arrFiles;

            $mimeMap = [
                'webm' => 'video/webm',
                'mp4'  => 'video/mp4',
                'm4v'  => 'video/mp4',
                'mov'  => 'video/quicktime',
                'wmv'  => 'video/x-ms-wmv',
                'ogv'  => 'video/ogg',
            ];

            foreach ($this->objFiles as $objFileModel) {
                $objFile = new File($objFileModel->path);
                $arrFiles[$objFile->extension] = $objFile;
                $arrSources[$objFile->extension] = [
                    'path' => $objFile->path,
                    'mime' => $mimeMap[$objFile->extension] ?? '',
                ];
            }

            // Legacy template data (BC for *.html5 templates)
            $this->Template->files = array_values(array_filter($arrFiles));

            // Plain data for the Twig template (cannot read Contao\File magic properties)
            $this->Template->videoSources = array_values(array_filter($arrSources));
        }
    }
}
