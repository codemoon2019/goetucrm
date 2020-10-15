<?php

return [
    'App\Contracts\BaseService' => [
        'class' => 'App\Services\BaseServiceImpl',
        'shared' => false,
        'singleton' => true,
    ],

    'App\Contracts\DepartmentService' => [
        'class' => 'App\Services\Departments\DepartmentServiceImpl',
        'shared' => false,
        'singleton' => true
    ],

    'App\Contracts\Departments\DepartmentListService' => [
        'class' => 'App\Services\Departments\DepartmentListServiceImpl',
        'shared' => false,
        'singleton' => true
    ],

    'App\Contracts\ProductOrderService' => [
        'class' => 'App\Services\Products\ProductOrderServiceImpl',
        'shared' => false,
        'singleton' => true
    ],
	'App\Contracts\DashboardService' => [
        'class' => 'App\Services\Dashboard\DashboardServiceImpl',
        'shared' => false,
        'singleton' => true
    ],

    'App\Contracts\TicketService' => [
        'class' => 'App\Services\TicketServiceImpl',
        'shared' => false,
        'singleton' => true
    ],

    /**
     * Ticket
     */
    'App\Contracts\Tickets\TicketActivityListService' => [
        'class' => 'App\Services\Tickets\TicketActivityListServiceImpl',
        'shared' => false,
        'singleton' => true
    ],

    'App\Contracts\Tickets\TicketActivityOperationService' => [
        'class' => 'App\Services\Tickets\TicketActivityOperationServiceImpl',
        'shared' => false,
        'singleton' => true
    ],

    'App\Contracts\TicketCountService' => [
        'class' => 'App\Services\Tickets\TicketCountServiceImpl',
        'shared' => false,
        'singleton' => true
    ],
    'App\Contracts\TicketListService' => [
        'class' => 'App\Services\Tickets\TicketListServiceImpl',
        'shared' => false,
        'singleton' => true
    ],
    'App\Contracts\TicketActionService' => [
        'class' => 'App\Services\Tickets\TicketActionServiceImpl',
        'shared' => false,
        'singleton' => true
    ],
    'App\Contracts\TicketNotifyService' => [
        'class' => 'App\Services\Tickets\TicketNotifyServiceImpl',
        'shared' => false,
        'singleton' => true
    ],

    /**
     * Users
     */

    'App\Contracts\Users\UserListService' => [
        'class' => 'App\Services\Users\UserListServiceImpl',
        'shared' => false,
        'singleton' => true
    ],

    /**
     * Workflow
     */

    'App\Contracts\Workflow\WorkflowNotifyService' => [
        'class' => 'App\Services\Workflow\WorkflowNotifyServiceImpl',
        'shared' => false,
        'singleton' => true
    ],
];
