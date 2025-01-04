<?php

/**
 * Social Media config.php
 *
 * This file exists only as a template for the Social Media settings.
 * It does nothing on its own.
 *
 * Don't edit this file, instead copy it to 'craft/config' as 'social-media.php'
 * and make your changes there to override default settings.
 */

return [
    'customPlatforms' => [
        'example' => [
            'label' => 'Example Platform',
            'color' => '#FF0000',
            'icon' => '<svg>...</svg>',
            'urlFormat' => 'https://example.com/{username}',
            'urlPattern' => '/^https?:\/\/(www\.)?example\.com\/.*$/',
        ],
        // Add more custom platforms here
    ],
];