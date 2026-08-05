<?php

/*
|--------------------------------------------------------------------------
| Admin Permission Catalog
|--------------------------------------------------------------------------
| Each module lists a label and the actions available for it. This drives
| the role permission matrix UI and the admin_can() checks.
|
| Standard actions: view, create, edit, delete, approve.
| Some modules add module-specific actions (e.g. media.notify, users.verify).
*/

return [

    'modules' => [

        'dashboard'    => ['label' => 'Dashboard',        'actions' => ['view']],

        'books'        => ['label' => 'Books',            'actions' => ['view', 'create', 'edit', 'delete']],
        'category'     => ['label' => 'Categories',       'actions' => ['view', 'create', 'edit', 'delete']],
        'subcategory'  => ['label' => 'Sub Categories',   'actions' => ['view', 'create', 'edit', 'delete']],
        'authors'      => ['label' => 'Authors',          'actions' => ['view', 'create', 'edit', 'delete']],

        'user_books'   => ['label' => 'User Uploaded Books', 'actions' => ['view', 'approve', 'delete']],

        'media'        => ['label' => 'Media Feed',        'actions' => ['view', 'create', 'edit', 'approve', 'delete', 'notify']],
        'posts'        => ['label' => 'Posts',             'actions' => ['view', 'edit', 'delete', 'notify']],

        'users'        => ['label' => 'Users',             'actions' => ['view', 'create', 'edit', 'delete', 'verify']],

        'university'   => ['label' => 'Universities',      'actions' => ['view', 'create', 'edit', 'delete']],
        'department'   => ['label' => 'Departments',       'actions' => ['view', 'create', 'edit', 'delete']],
        'college'      => ['label' => 'Colleges',          'actions' => ['view', 'create', 'edit', 'delete']],

        'transactions' => ['label' => 'Transactions',      'actions' => ['view']],
        'reviews'      => ['label' => 'Reviews',           'actions' => ['view', 'delete']],
        'reports'      => ['label' => 'Reports',           'actions' => ['view', 'delete']],
        'suggestion'   => ['label' => 'Suggestions',       'actions' => ['view', 'delete']],

        'subscription' => ['label' => 'Subscription Plans','actions' => ['view', 'create', 'edit', 'delete']],
        'home_sections'=> ['label' => 'Home Sections',     'actions' => ['view', 'create', 'edit', 'delete']],
        'slider'       => ['label' => 'Sliders',           'actions' => ['view', 'create', 'edit', 'delete']],
        'pages'        => ['label' => 'Pages',             'actions' => ['view', 'create', 'edit', 'delete']],
        'app_ads'      => ['label' => 'App Ads',           'actions' => ['view', 'edit']],
        'payment'      => ['label' => 'Payment Gateways',  'actions' => ['view', 'edit']],

        'notification' => ['label' => 'Notifications',     'actions' => ['view', 'create']],

        'roles'        => ['label' => 'Roles & Permissions','actions' => ['view', 'create', 'edit', 'delete']],
        'settings'     => ['label' => 'Settings',          'actions' => ['view', 'edit']],
    ],

    // Human labels for actions (for the matrix UI).
    'action_labels' => [
        'view'    => 'View',
        'create'  => 'Create',
        'edit'    => 'Edit',
        'delete'  => 'Delete',
        'approve' => 'Approve',
        'notify'  => 'Notify',
        'verify'  => 'Verify',
    ],
];
