<?php

namespace Webkul\GraphQLChannelExtension\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Webkul\Core\Repositories\ChannelRepository;

class ChannelMiddleware
{
    public function __construct(protected ChannelRepository $channels) {}

    public function handle(Request $request, Closure $next)
    {
        $channel = null;

        // 1) Explicit header wins
        if ($code = $request->header('x-channel')) {
            $channel = $this->channels->findOneByField('code', $code);
        }

        // 2) Fallback to host
        if (! $channel) {
            $reqHost = $this->canonicalHost($request->getHttpHost());

            // Fetch all and match in PHP to tolerate stored variations
            $all = $this->channels->getModel()->newQuery()->get();

            $channel = $all->first(function ($ch) use ($reqHost) {
                $stored = $this->extractHost((string) $ch->hostname);
                return $stored && $this->canonicalHost($stored) === $reqHost;
            });
        }

        if ($channel) {
            $request->attributes->set('graphql_channel', $channel);
        }

        return $next($request);
    }

    private function canonicalHost(string $host): string
    {
        $host = strtolower(trim($host));

        // Strip scheme if present
        if (str_starts_with($host, 'http://') || str_starts_with($host, 'https://')) {
            $host = parse_url($host, PHP_URL_HOST) ?: $host;
        }

        // Remove port
        $host = explode(':', $host)[0];

        // Remove leading www.
        if (str_starts_with($host, 'www.')) {
            $host = substr($host, 4);
        }

        return rtrim($host, '/');
    }

    private function extractHost(string $stored): ?string
    {
        $stored = trim($stored);

        // If it looks like a URL, parse it; otherwise assume it's a host (with or without path)
        if (preg_match('#^https?://#i', $stored)) {
            return parse_url($stored, PHP_URL_HOST) ?: null;
        }

        // Remove any path segment if stored as "example.com/some/path"
        $parts = explode('/', $stored, 2);

        return $parts[0] ?: null;
    }
}
