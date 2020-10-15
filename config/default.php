<?php

use App\Models\TicketPriority;

return [
    'ticket_issue_types' => [
        [
            'description' => 'Incident',
            'ticket_reasons' => [
                [
                    'description' => 'Not Working',
                    'ticket_priority_code' => TicketPriority::URGENT,
                ],
            ],
        ],
        [
            'description' => 'Other',
            'ticket_reasons' => [
                [
                    'description' => 'Other',
                    'ticket_priority_code' => TicketPriority::HIGH,
                ]
            ],
        ],
        [
            'description' => 'Inquiry',
            'ticket_reasons' => [
                [
                    'description' => 'Pricing',
                    'ticket_priority_code' => TicketPriority::MEDIUM,
                ],
                [
                    'description' => 'Update',
                    'ticket_priority_code' => TicketPriority::MEDIUM,
                ],
            ],
        ],
        [
            'description' => 'Request',
            'ticket_reasons' => [
                [
                    'description' => 'Refund',
                    'ticket_priority_code' => TicketPriority::HIGH,
                ],
                [
                    'description' => 'Update',
                    'ticket_priority_code' => TicketPriority::HIGH,
                ],
                [
                    'description' => 'Cancel',
                    'ticket_priority_code' => TicketPriority::HIGH,
                ],
            ],
        ],
        [
            'description' => 'Task',
            'default_workflow' => true,
            'ticket_reasons' => [
                [
                    'description' => 'Subtask',
                    'ticket_priority_code' => TicketPriority::MEDIUM,
                    'default_workflow' => true,
                ]
            ]
        ]
    ]
];