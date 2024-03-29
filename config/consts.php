<?php

return [
    //Shared constatns
    'all_formats'   => [
            1 => 'Interstitial',
            2 => 'Banner',
        ],
    'all_devices'   => [
            1 => 'Mobile',
            2 => 'Tablet'
        ],
    'all_layouts'   => [
            1 => 'Portrait',
            2 => 'Landscape'
        ],
    'page_sizes'    =>[
            10, 20, 50, 100
        ],

    // For User Module
    'user_status'   => [
            1 => 'Pending',
            2 => 'Active',
            3 => 'Suspended', 
        ],
    'user_roles'    => [
            1 => 'Developer',
            2 => 'Admin'
        ],

    // For Application Module
    'app_status'    => [
            1 => 'Pending',
            2 => 'Active',
            3 => 'Deleted'
        ],
    'app_platforms' => [
            1 => 'Android',
            2 => 'IOS'
        ],

    // For Zone Module
    'zone_formats'  => [
            1 => 'Interstitial',
            2 => 'Banner',
        ],
    'zone_devices'  => [
            1 => 'Mobile',
            2 => 'Tablet'
        ],
    'zone_layouts'  => [
            1 => 'Portrait',
            2 => 'Landscape'
        ],
    'zone_status'   => [
            1 => 'Active',
            2 => 'Deleted'
        ],

    // For Campaign Module
    'camp_status'   => [
            1 => 'Running',
            2 => 'Paused',
            3 => 'Completed',
            4 => 'Deleted'
        ],
    'camp_serving'  => [
            1 => 'Even',
            2 => 'Fast'
        ],

    // For Creative Ads
    'ads_types'     => [
            2 => 'Image', 
            1 => 'Text'
        ],
    'ads_status'    => [
            1 => 'Running',
            2 => 'Paused',
            3 => 'Completed',
            4 => 'Deleted'
        ],

    // For sdk actions
    'sdk_actions'   => [
            1 => 'Request Action',
            2 => 'Show Action',
            3 => 'Click Action',
            4 => 'Install Action'
        ],

    // for charts filter
    'charts_filters' => [
            'd'     => "DATE",
            'w'     => "WEEK",
            'm'     => "MONTH"
        ]
];