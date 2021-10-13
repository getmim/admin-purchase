<?php

return [
    '__name' => 'admin-purchase',
    '__version' => '0.0.1',
    '__git' => 'git@github.com:getmim/admin-purchase.git',
    '__license' => 'MIT',
    '__author' => [
        'name' => 'Iqbal Fauzi',
        'email' => 'iqbalfawz@gmail.com',
        'website' => 'http://iqbalfn.com/'
    ],
    '__files' => [
        'modules/admin-purchase' => ['install','update','remove'],
        'theme/admin/purchase' => ['install','update','remove']
    ],
    '__dependencies' => [
        'required' => [
            [
                'purchase' => NULL
            ],
            [
                'admin' => NULL
            ],
            [
                'lib-formatter' => NULL
            ],
            [
                'lib-form' => NULL
            ],
            [
                'lib-pagination' => NULL
            ],
            [
                'admin-product' => null
            ]
        ],
        'optional' => []
    ],
    'autoload' => [
        'classes' => [
            'AdminPurchase\\Controller' => [
                'type' => 'file',
                'base' => 'modules/admin-purchase/controller'
            ]
        ],
        'files' => []
    ],
    'routes' => [
        'admin' => [
            'adminPurchase' => [
                'path' => [
                    'value' => '/purchase'
                ],
                'method' => 'GET',
                'handler' => 'AdminPurchase\\Controller\\Purchase::index'
            ],
            'adminPurchaseDetails' => [
                'path' => [
                    'value' => '/purchase/(:id)',
                    'params' => [
                        'id' => 'number'
                    ]
                ],
                'method' => 'GET',
                'handler' => 'AdminPurchase\\Controller\\Purchase::details'
            ],
            'adminPurchaseCourier' => [
                'path' => [
                    'value' => '/purchase/(:id)/courier',
                    'params' => [
                        'id' => 'number'
                    ]
                ],
                'method' => 'POST',
                'handler' => 'AdminPurchase\\Controller\\Purchase::courier'
            ]
        ]
    ],
    'adminUi' => [
        'sidebarMenu' => [
            'items' => [
                'product' => [
                    'label' => 'Product',
                    'icon' => '<i class="fas fa-box-open"></i>',
                    'priority' => 0,
                    'filterable' => false,
                    'children' => [
                        'purchase' => [
                            'label' => 'Purchase',
                            'icon'  => '<i></i>',
                            'route' => ['adminPurchase'],
                            'perms' => 'manage_purchase'
                        ]
                    ]
                ]
            ]
        ]
    ],
    'libForm' => [
        'forms' => [
            'admin.purchase.index' => [
                'q' => [
                    'label' => 'Search',
                    'type' => 'search',
                    'nolabel' => TRUE,
                    'rules' => []
                ],
                'status' => [
                    'label' => 'Status',
                    'type' => 'select',
                    'nolabel' => TRUE,
                    'options' => [
                        0 => 'All',
                        2 => 'Waiting for payment',
                        3 => 'Already paid by customer',
                        4 => 'On the way',
                        5 => 'Received by customer',
                        6 => 'On the way back to merchant',
                        7 => 'Returned back to merchant'
                    ],
                    'rules' => []
                ]
            ],
            'admin.purchase.courier' => [
                'courier_receipt' => [
                    'label' => 'Courier Receipt',
                    'type' => 'text',
                    'nolabel' => true,
                    'rules' => [
                        'required' => true
                    ]
                ]
            ]
        ]
    ]
];
