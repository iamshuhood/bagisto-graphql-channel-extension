<?php

namespace Webkul\GraphQLChannelExtension\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Http\Kernel;

class GraphQLChannelExtensionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/channel-extension.php', 'graphql-channel-extension');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Automatically inject middleware into Lighthouse configuration
        $this->injectMiddleware();
        
        // Automatically inject query namespaces
        $this->injectQueryNamespaces();
    }

    /**
     * Inject middleware into Lighthouse configuration at runtime.
     */
    protected function injectMiddleware(): void
    {
        $config = config('lighthouse.route.middleware', []);
        
        // Find the position to inject ChannelMiddleware (after AttemptAuthentication)
        $channelMiddleware = \Webkul\GraphQLChannelExtension\Http\Middleware\ChannelMiddleware::class;
        $cacheMiddleware = \Webkul\GraphQLChannelExtension\Http\Middleware\GraphQLCacheMiddleware::class;
        
        // Remove old middleware if it exists (from vendor)
        $oldChannelMiddleware = 'Webkul\\GraphQLAPI\\Http\\Middleware\\ChannelMiddleware';
        $oldCacheMiddleware = 'Webkul\\GraphQLAPI\\Http\\Middleware\\GraphQLCacheMiddleware';
        
        $config = array_filter($config, function($middleware) use ($oldChannelMiddleware, $oldCacheMiddleware) {
            return $middleware !== $oldChannelMiddleware && $middleware !== $oldCacheMiddleware;
        });
        
        // Re-index array
        $config = array_values($config);
        
        // Find AttemptAuthentication position
        $attemptAuthPos = array_search('Nuwave\\Lighthouse\\Http\\Middleware\\AttemptAuthentication', $config);
        
        if ($attemptAuthPos !== false && !in_array($channelMiddleware, $config)) {
            // Insert ChannelMiddleware right after AttemptAuthentication
            array_splice($config, $attemptAuthPos + 1, 0, [$channelMiddleware]);
        } elseif (!in_array($channelMiddleware, $config)) {
            // Fallback: add at the beginning
            array_unshift($config, $channelMiddleware);
        }
        
        // Find RateLimitMiddleware position for cache middleware
        $rateLimitPos = false;
        foreach ($config as $idx => $middleware) {
            if (strpos($middleware, 'RateLimitMiddleware') !== false) {
                $rateLimitPos = $idx;
                break;
            }
        }
        
        if ($rateLimitPos !== false && !in_array($cacheMiddleware, $config)) {
            // Insert GraphQLCacheMiddleware right after RateLimitMiddleware
            array_splice($config, $rateLimitPos + 1, 0, [$cacheMiddleware]);
        } elseif (!in_array($cacheMiddleware, $config)) {
            // Fallback: add after ChannelMiddleware
            $channelPos = array_search($channelMiddleware, $config);
            if ($channelPos !== false) {
                array_splice($config, $channelPos + 1, 0, [$cacheMiddleware]);
            } else {
                $config[] = $cacheMiddleware;
            }
        }
        
        // Update the configuration
        config(['lighthouse.route.middleware' => $config]);
    }

    /**
     * Inject query namespaces into Lighthouse configuration.
     */
    protected function injectQueryNamespaces(): void
    {
        $queries = config('lighthouse.namespaces.queries', []);
        
        // Ensure it's an array
        if (is_string($queries)) {
            $queries = [$queries];
        }
        
        $extensionNamespace = 'Webkul\\GraphQLChannelExtension\\Queries';
        
        // Add our namespace if not already present
        if (!in_array($extensionNamespace, $queries)) {
            $queries[] = $extensionNamespace;
        }
        
        config(['lighthouse.namespaces.queries' => $queries]);
    }
}
