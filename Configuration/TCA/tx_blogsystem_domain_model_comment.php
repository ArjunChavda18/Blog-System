<?php
return [
    'ctrl' => [
        'title' => 'Blog Comment',
        'label' => 'author_name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'searchFields' => 'author_name,comment_text',
        'iconfile' => 'EXT:core/Resources/Public/Icons/T3Icons/content/content-special-menu.svg'
    ],
    'types' => [
        '1' => ['showitem' => 'approved, author_name, comment_text, hidden'],
    ],
    'columns' => [
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.visible',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        1 => '',
                        'invertStateDisplay' => true
                    ]
                ],
            ],
        ],
        'approved' => [
            'exclude' => true,
            'label' => 'Approve This Comment (Show on Website)',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => 'No',
                        1 => 'Yes'
                    ]
                ],
                'default' => 0,
            ],
        ],
        'author_name' => [
            'exclude' => false,
            'label' => 'Author Name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required'
            ],
        ],
        'comment_text' => [
            'exclude' => false,
            'label' => 'Comment Text',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 5,
                'eval' => 'trim,required'
            ],
        ],
        'blog_id' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
    ],
];