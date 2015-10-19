<?php
/**
 * Here you can add your subnetworks and their location based informations
 *
 * visitorInfo can be extended to all available fields inside the `log_visit` table of piwik
 */
return [
    
    'test' => [
        
        'address' => [
            'latitude'     => 47.213357,
            'longitude'    => 9.522432,
            'bounds'       => [
                'south' => null,
                'west'  => null,
                'north' => null,
                'east'  => null,
            ],
            'streetNumber' => '123',
            'streetName'   => 'your street',
            'locality'     => 'your location',
            'postalCode'   => null,
            'subLocality'  => null,
            'adminLevels'  => [],
            'country'      => 'Austria',
            'countryCode'  => 'AT',
            'timezone'     => 'Europe/Vienna',
        ],
        
        'networks' => [
            '10.58.0.0/18',
        ]
    ]
];
