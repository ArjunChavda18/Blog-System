<?php
return [
    'blog_system_' => [
        'parent' => 'web',
        'position' => [],
        'access' => 'user,group',
        'iconIdentifier' => 'blog_system-module-',
        'labels' => 'LLL:EXT:blog_system/Resources/Private/Language/locallang_.xlf',
        'extensionName' => 'BlogSystem',
        'controllerActions' => [
            \NITSAN\BlogSystem\Controller\BlogController::class => ['list', 'show'],
            
        ],
    ],
];
