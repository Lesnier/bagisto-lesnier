<?php

return [
    [
        'key'        => 'marketplace',
        'name'       => 'Marketplace',
        'route'      => 'admin.marketplace.vendors.index',
        'sort'       => 5,
        'icon'       => 'icon-store', // You can change this to a valid Bagisto icon class
    ],
    [
        'key'        => 'marketplace.vendors',
        'name'       => 'Vendors',
        'route'      => 'admin.marketplace.vendors.index',
        'sort'       => 1,
        'icon'       => '',
    ],
    [
        'key'        => 'marketplace.earnings',
        'name'       => 'Earnings',
        'route'      => 'admin.marketplace.earnings.index',
        'sort'       => 2,
        'icon'       => '',
    ]
];
