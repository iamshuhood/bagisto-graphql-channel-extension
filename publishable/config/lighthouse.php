<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Route Configuration
    |--------------------------------------------------------------------------
    */

    'route' => [
        'uri' => env('GRAPHQL_PATH', '/graphql'),
        'name' => 'graphql',
        'middleware' => [
            // Lighthouse provides its own authentication middleware.
            // Instead of using Laravel's default guards, you can configure your
            // guards to use the `AttemptAuthentication` middleware directly on your
            // schema's field types. If you prefer to use Laravel's default guards,
            // middleware, this delegates auth and permission checks to the field level.
            Nuwave\Lighthouse\Http\Middleware\AttemptAuthentication::class,

            // Logs every incoming GraphQL query.
            // Nuwave\Lighthouse\Http\Middleware\LogGraphQLQueries::class,

            // Validate channel in request (must run before Locale/Currency/RateLimit)
            // THIS IS PROVIDED BY THE CHANNEL EXTENSION PACKAGE
            Webkul\GraphQLChannelExtension\Http\Middleware\ChannelMiddleware::class,

            // Validate Locale in request
            Webkul\GraphQLAPI\Http\Middleware\LocaleMiddleware::class,

            // Validate Currency in request
            Webkul\GraphQLAPI\Http\Middleware\CurrencyMiddleware::class,

            // Rate Limit the request
            Webkul\GraphQLAPI\Http\Middleware\RateLimitMiddleware::class,

            // Validate request Cache with channel support
            // THIS IS PROVIDED BY THE CHANNEL EXTENSION PACKAGE
            Webkul\GraphQLChannelExtension\Http\Middleware\GraphQLCacheMiddleware::class,

            // Encrypt cookies
            App\Http\Middleware\EncryptCookies::class,

            // Add Queued Cookies to response
            Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,

            // Start session for request
            Illuminate\Session\Middleware\StartSession::class,
        ],

        // Limit the complexity & depth
        'complexity' => null,
        'max_query_complexity' => 1000,
        'max_query_depth' => 5,

        // Custom Lighthouse HTTP response handler for GraphQL requests
        'response_handler' => null,

        // Disable query batching on the public GraphQL endpoint.
        'disable_batching' => false,

        // PHP Constraints for router that the public route must match.
        // https://laravel.com/docs/routing#parameters-regular-expression-constraints
        // 'where' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    */

    'guard' => 'admin-api',

    /*
    |--------------------------------------------------------------------------
    | Schema Path
    |--------------------------------------------------------------------------
    */

    'schema_path' => base_path('vendor/bagisto/graphql-api/src/graphql/schema.graphql'),

    /*
    |--------------------------------------------------------------------------
    | Schema Cache
    |--------------------------------------------------------------------------
    */

    'schema_cache' => [
        'enable' => env('LIGHTHOUSE_SCHEMA_CACHE_ENABLE', env('APP_ENV') !== 'local'),
        'path' => env('LIGHTHOUSE_SCHEMA_CACHE_PATH', base_path('bootstrap/cache/lighthouse-schema.php')),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Directive Tags
    |--------------------------------------------------------------------------
    */
    'cache_directive_tags' => false,

    /*
    |--------------------------------------------------------------------------
    | Query Cache
    |--------------------------------------------------------------------------
    */

    'query_cache' => [
        'enable' => env('LIGHTHOUSE_QUERY_CACHE_ENABLE', true),
        'ttl' => env('LIGHTHOUSE_QUERY_CACHE_TTL', 24 * 60 * 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation Cache
    |--------------------------------------------------------------------------
    */

    'validation_cache' => [
        'enable' => env('LIGHTHOUSE_VALIDATION_CACHE_ENABLE', true),
        'ttl' => env('LIGHTHOUSE_VALIDATION_CACHE_TTL', 24 * 60 * 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | Parse source location
    |--------------------------------------------------------------------------
    */

    'parse_source_location' => true,

    /*
    |--------------------------------------------------------------------------
    | Namespaces
    |--------------------------------------------------------------------------
    */

    'namespaces' => [
        'models' => [
            'Webkul\\*\\Models',
            'App\\Models',
        ],
        'mutations' => 'Webkul\\GraphQLAPI\\Mutations',
        'queries' => [
            'Webkul\\GraphQLAPI\\Queries',
            // Add channel extension queries
            'Webkul\\GraphQLChannelExtension\\Queries',
        ],
        'subscriptions' => 'Webkul\\GraphQLAPI\\Subscriptions',
        'directives' => [
            'Webkul\\GraphQLAPI\\Directives',
        ],
        'validators' => [
            'Webkul\\GraphQLAPI\\Validators',
        ],
        'scalars' => [
            'Webkul\\GraphQLAPI\\Scalars',
        ],
        'unions' => [
            'Webkul\\GraphQLAPI\\Unions',
        ],
        'interfaces' => [
            'Webkul\\GraphQLAPI\\Interfaces',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Security
    |--------------------------------------------------------------------------
    */

    'security' => [
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */

    'pagination' => [
        'default_count' => 10,
        'max_count' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Debug
    |--------------------------------------------------------------------------
    */

    'debug' => env('LIGHTHOUSE_DEBUG', GraphQL\Error\DebugFlag::INCLUDE_DEBUG_MESSAGE | GraphQL\Error\DebugFlag::INCLUDE_TRACE),

    /*
    |--------------------------------------------------------------------------
    | Error Handlers
    |--------------------------------------------------------------------------
    */

    'error_handlers' => [
        Nuwave\Lighthouse\Execution\ExtensionErrorHandler::class,
        Nuwave\Lighthouse\Execution\ReportingErrorHandler::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Field Middleware
    |--------------------------------------------------------------------------
    */

    'field_middleware' => [
        Nuwave\Lighthouse\Schema\Directives\TrimDirective::class,
        Nuwave\Lighthouse\Schema\Directives\SanitizeDirective::class,
        Nuwave\Lighthouse\Schema\Directives\TransformArgsDirective::class,
        Nuwave\Lighthouse\Schema\Directives\SpreadDirective::class,
        Nuwave\Lighthouse\Schema\Directives\RenameArgsDirective::class,
        Nuwave\Lighthouse\Schema\Directives\DropArgsDirective::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Global ID
    |--------------------------------------------------------------------------
    */

    'global_id_field' => 'id',

    /*
    |--------------------------------------------------------------------------
    | Persisted Queries
    |--------------------------------------------------------------------------
    */

    'persisted_queries' => true,

    /*
    |--------------------------------------------------------------------------
    | Transactional Mutations
    |--------------------------------------------------------------------------
    */

    'transactional_mutations' => true,

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment Protection
    |--------------------------------------------------------------------------
    */

    'force_fill' => true,

    /*
    |--------------------------------------------------------------------------
    | Batchload Relations
    |--------------------------------------------------------------------------
    */

    'batchload_relations' => true,

    /*
    |--------------------------------------------------------------------------
    | Shortcut Foreign Key Selection
    |--------------------------------------------------------------------------
    */

    'shortcut_foreign_key_selection' => false,
];
