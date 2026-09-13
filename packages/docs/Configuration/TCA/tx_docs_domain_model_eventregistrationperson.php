<?php

declare(strict_types=1);

return [
    'ctrl' => [
        'title' => 'Event Registration Person',
        'label' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'hideTable' => true,
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
    ],
    'columns' => [
        'hidden' => [
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
            ],
        ],
        'parent_event_registration' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'name' => [
            'label' => 'Name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'email' => [
            'label' => 'Email',
            'config' => [
                'type' => 'email',
                'size' => 30,
            ],
        ],
        'phone' => [
            'label' => 'Phone',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'eval' => 'trim',
                'searchable' => false,
            ],
        ],
        'country' => [
            'label' => 'Country',
            'config' => [
                'type' => 'input',
                'size' => 10,
                'eval' => 'trim',
                'searchable' => false,
            ],
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => 'name, email, phone, country',
        ],
    ],
];
