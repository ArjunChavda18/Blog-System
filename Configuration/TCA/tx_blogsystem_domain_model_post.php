<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:blog_system/Resources/Private/Language/locallang_db.xlf:tx_blogsystem_domain_model_post',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'versioningWS' => true,
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'title,content,author',
        'iconfile' => 'EXT:blog_system/Resources/Public/Icons/tx_blogsystem_domain_model_post.gif'
    ],
    'types' => [
        '1' => ['showitem' => 'title, content, author, --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language, sys_language_uid, l10n_parent, l10n_diffsource, --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, hidden, starttime, endtime'],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'language',
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'default' => 0,
                'items' => [
                    ['label' => '', 'value' => 0],
                ],
                'foreign_table' => 'tx_blogsystem_domain_model_post',
                'foreign_table_where' => 'AND {#tx_blogsystem_domain_model_post}.{#pid}=###CURRENT_PID### AND {#tx_blogsystem_domain_model_post}.{#sys_language_uid} IN (-1,0)',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.visible',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        'label' => '',
                        'value' => '',
                        'invertStateDisplay' => true
                    ]
                ],
            ],
        ],
        'starttime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'datetime',
                'format' => 'datetime',
                'default' => 0,
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ],
        ],
        'endtime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'datetime',
                'format' => 'datetime',
                'default' => 0,
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ],
        ],

        'title' => [
            'exclude' => true,
            'label' => 'LLL:EXT:blog_system/Resources/Private/Language/locallang_db.xlf:tx_blogsystem_domain_model_post.title',
            'description' => 'LLL:EXT:blog_system/Resources/Private/Language/locallang_db.xlf:tx_blogsystem_domain_model_post.title.description',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'default' => ''
            ],
            
        ],
        'content' => [
            'exclude' => true,
            'label' => 'LLL:EXT:blog_system/Resources/Private/Language/locallang_db.xlf:tx_blogsystem_domain_model_post.content',
            'description' => 'LLL:EXT:blog_system/Resources/Private/Language/locallang_db.xlf:tx_blogsystem_domain_model_post.content.description',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
                'richtextConfiguration' => 'default',
                'fieldControl' => [
                    'fullScreenRichtext' => [
                        'disabled' => false,
                    ],
                ],
                'cols' => 40,
                'rows' => 15,
            ],
            
        ],
        'author' => [
            'exclude' => true,
            'label' => 'LLL:EXT:blog_system/Resources/Private/Language/locallang_db.xlf:tx_blogsystem_domain_model_post.author',
            'description' => 'LLL:EXT:blog_system/Resources/Private/Language/locallang_db.xlf:tx_blogsystem_domain_model_post.author.description',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'default' => ''
            ],
            
        ],
    
    ],
];
