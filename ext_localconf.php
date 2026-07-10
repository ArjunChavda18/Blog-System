<?php
defined('TYPO3') || die();

(static function() {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'BlogSystem', //Extension Name
        'BlogList', //Plugin Name
        [
            \NITSAN\BlogSystem\Controller\BlogController::class => 'list,listJson, show, createComment, deleteComment'
        ],
        // non-cacheable actions
        [
            \NITSAN\BlogSystem\Controller\BlogController::class => 'createComment, deleteComment, list, listJson'
        ],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup(
        '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:blog_system/Configuration/TypoScript/setup.typoscript">'
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants(
        '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:blog_system/Configuration/TypoScript/constants.typoscript">'
    );
})();
