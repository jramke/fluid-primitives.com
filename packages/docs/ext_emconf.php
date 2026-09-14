<?php

declare(strict_types=1);

// @mago-expect analysis:mixed-array-assignment
// @mago-expect analysis:undefined-variable(2)
/** @disregard */
$EM_CONF[$_EXTKEY] = [
    'title' => 'Docs',
    'description' => '',
    'category' => 'templates',
    'constraints' => [
        'depends' => [
            'typo3' => '14.0.0-14.3.99',
        ],
        'conflicts' => [],
    ],
    'autoload' => [
        'psr-4' => [
            'FluidPrimitives\\Docs\\' => 'Classes',
        ],
    ],
    'state' => 'stable',
    'author' => 'Joost Ramke',
    'author_email' => 'hey@joostramke.com',
    'author_company' => 'jramke',
    'version' => '1.0.0',
];
