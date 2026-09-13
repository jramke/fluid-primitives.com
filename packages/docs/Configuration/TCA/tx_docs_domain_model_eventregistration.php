<?php

declare(strict_types=1);

return [
    'ctrl' => [
        'title' => 'Event Registration',
        'label' => 'uid',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
    ],
    'columns' => [
        'sys_language_uid' => [
            'config' => [
                'type' => 'language',
            ],
        ],
        'l10n_parent' => [
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_docs_domain_model_eventregistration',
                'foreign_table_where' => 'AND {#tx_docs_domain_model_eventregistration}.{#pid}=###CURRENT_PID### AND {#tx_docs_domain_model_eventregistration}.{#sys_language_uid} IN (-1,0)',
                'default' => 0,
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'hidden' => [
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
            ],
        ],
        'ticket_type' => [
            'label' => 'Ticket type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'VIP', 'value' => 'vip'],
                    ['label' => 'Standard', 'value' => 'standard'],
                    ['label' => 'Student', 'value' => 'student'],
                ],
            ],
        ],
        'ticket_count' => [
            'label' => 'Ticket count',
            'config' => [
                'type' => 'number',
                'default' => 1,
                'range' => [
                    'lower' => 1,
                    'upper' => 10,
                ],
            ],
        ],
        'person' => [
            'label' => 'Person',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_docs_domain_model_eventregistrationperson',
                'foreign_field' => 'parent_event_registration',
                'relationship' => 'oneToOne',
                'minitems' => 1,
                'maxitems' => 1,
            ],
        ],
        'mode' => [
            'label' => 'Attendance mode',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'In Person', 'value' => 'person'],
                    ['label' => 'Virtual', 'value' => 'virtual'],
                ],
            ],
        ],
        'student_id' => [
            'label' => 'Student ID',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'badge_photo' => [
            'label' => 'Badge photo',
            'config' => [
                'type' => 'file',
                'relationship' => 'manyToOne',
                'allowed' => 'jpg,jpeg,png',
            ],
        ],
        'a11y_needs' => [
            'label' => 'Accessibility needs',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectCheckBox',
                'items' => [
                    ['label' => 'Wheelchair Access', 'value' => 'wheelchair'],
                    ['label' => 'Sign Language Interpretation', 'value' => 'sign-language'],
                ],
            ],
        ],
        'comment' => [
            'label' => 'Comment',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 5,
                'max' => 500,
                'searchable' => false,
            ],
        ],
        'privacy' => [
            'label' => 'Privacy policy accepted',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
            ],
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => 'ticket_type, ticket_count, person, mode, student_id, badge_photo, a11y_needs, comment, privacy',
        ],
    ],
];
