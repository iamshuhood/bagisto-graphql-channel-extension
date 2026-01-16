<?php

return [
    /*
    |--------------------------------------------------------------------------
    | GraphQL Channel Extension Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration is automatically merged into your Lighthouse config
    | at runtime. No manual configuration changes needed!
    |
    */

    'enabled' => env('GRAPHQL_CHANNEL_EXTENSION_ENABLED', true),

    'cache' => [
        'enabled' => env('GRAPHQL_CHANNEL_CACHE_ENABLED', true),
        'ttl' => env('GRAPHQL_CHANNEL_CACHE_TTL', 60 * 60 * 24), // 24 hours
    ],

    'channel_detection' => [
        // Order of detection: 'header' will be checked first, then 'hostname'
        'priority' => ['header', 'hostname'],
        
        // Header name for explicit channel selection
        'header_name' => 'x-channel',
        
        // Enable hostname-based auto-detection
        'auto_detect_hostname' => true,
        
        // Strip www from hostnames during comparison
        'strip_www' => true,
    ],
];
