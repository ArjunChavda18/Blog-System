<?php
defined('TYPO3') || die();

(static function() {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'BlogSystem', //Extension Name
        'BlogList', //Plugin Name
        [
            \NITSAN\BlogSystem\Controller\BlogController::class => 'list, show, createComment, deleteComment'
        ],
        // non-cacheable actions
        [
            \NITSAN\BlogSystem\Controller\BlogController::class => 'createComment, deleteComment'
        ],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );

})();
