<?php

$date = date('Y');

$header = <<<EOF
Hero Element for Contao Open Source CMS.

@copyright  Copyright (c) $date, Erdmann & Freunde
@author     Dennis Erdmann
@author     Richard Henkenjohann
@license    MIT
@link       http://github.com/nutshell-framework/hero-element
EOF;


$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
;

$config = new PhpCsFixer\Config();
return $config->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        '@PHPUnit60Migration:risky' => true,
        'align_multiline_comment' => true,
        'array_indentation' => true,
        'array_syntax' => ['syntax' => 'short'],
        'combine_consecutive_issets' => true,
        'combine_consecutive_unsets' => true,
        'comment_to_phpdoc' => true,
        'compact_nullable_typehint' => true,
        'declare_strict_types' => true,
        'header_comment' => ['header' => $header],
        'heredoc_to_nowdoc' => true,
        'linebreak_after_opening_tag' => true,
        'native_function_invocation' => [
            'include' => ['@compiler_optimized'],
        ],
        'no_null_property_initialization' => true,
        'no_superfluous_elseif' => true,
        'no_unreachable_default_argument_value' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'ordered_class_elements' => true,
        'ordered_imports' => true,
        'php_unit_strict' => true,
        'phpdoc_add_missing_param_annotation' => true,
        'phpdoc_order' => true,
        'phpdoc_types_order' => [
            'null_adjustment' => 'always_last',
            'sort_algorithm' => 'none',
        ],
        'strict_comparison' => true,
        'strict_param' => true,
    ])
    ->setFinder(PhpCsFixer\Finder::create()->in([__DIR__.'/src']))
    ->setRiskyAllowed(true)
    ->setUsingCache(false)
;