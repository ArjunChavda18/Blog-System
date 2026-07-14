<?php
defined('TYPO3') || die();

(static function() {
    // 1. MAIN BLOG LIST PLUGIN
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'BlogSystem',
        'BlogList', 
        [
            \NITSAN\BlogSystem\Controller\BlogController::class => 'list, show, createComment, deleteComment'
        ],
        // non-cacheable actions
        [
            \NITSAN\BlogSystem\Controller\BlogController::class => 'createComment, deleteComment'
        ]
    );

    // 2. DEDICATED AJAX PLUGIN FOR TYPO3 v13
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'BlogSystem',
        'BlogAjaxList', 
        [
            \NITSAN\BlogSystem\Controller\BlogController::class => 'list'
        ],
        [
            \NITSAN\BlogSystem\Controller\BlogController::class => 'list'
        ]
    );

    // TypoScript Includes
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup(
        '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:blog_system/Configuration/TypoScript/setup.typoscript">'
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants(
        '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:blog_system/Configuration/TypoScript/constants.typoscript">'
    );
})();