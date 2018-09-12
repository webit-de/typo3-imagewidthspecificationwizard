<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Wizard for image width field',
    'description' => 'This backend extension attaches a select field to content elements like textpic or images to provide a set of default values for the width of an image (eg.: full size, half of the content, small teaser etc.).',
    'category' => 'be',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 1,
    'author' => 'Dan Untenzu',
    'author_email' => 'untenzu@webit.de',
    'author_company' => 'webit! Gesellschaft für neue Medien mbH',
    'version' => '3.1.0',
    'constraints' => [
        'depends' => [
            'php' => '5.5.0-7.1.99',
            'typo3' => '7.6.0-8.7.99'
        ],
        'conflicts' => [],
        'suggests' => []
    ]
];
