<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Blog Management System',
    'description' => 'A simple extension to manage blogs and posts.',
    'category' => 'plugin',
    'author' => 'Arjun',
    'author_email' => 'arjunchavda@gmail.com',
    'state' => 'alpha',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-13.4.99',
            'extbase' => '13.4.0-13.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
