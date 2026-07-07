<?php
defined('TYPO3') || die();

$pluginSignature = \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'BlogSystem',
    'BlogList',
    'Blog System',
    'blog_system-plugin-'
);

$GLOBALS['TCA']['tt_content']['types'][$pluginSignature] = [
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,
            --palette--;;headers,
        --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.plugin,
            pages, recursive, pi_flexform,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,
            --palette--;;access,
    ',
];

// Official registration hook using the exact new file location
$GLOBALS['TCA']['tt_content']['columns']['pi_flexform']['config']['ds']['*,' . $pluginSignature] = 
    'FILE:EXT:blog_system/Configuration/FlexForms/FlexForm.xml';