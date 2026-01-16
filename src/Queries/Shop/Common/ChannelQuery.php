<?php

namespace Webkul\GraphQLChannelExtension\Queries\Shop\Common;

use Webkul\Core\Repositories\ChannelRepository;

class ChannelQuery
{
    public function __construct(protected ChannelRepository $channels) {}

    public function channelByCode($_, array $args)
    {
        return $this->channels->findOneByField('code', $args['code']);
    }

    public function channelByHostname($_, array $args)
    {
        $hostname = strtolower(trim($args['hostname']));
        return $this->channels->getModel()
            ->whereRaw('LOWER(hostname) = ?', [$hostname])
            ->orWhereRaw('LOWER(hostname) = ?', ['https://' . $hostname])
            ->orWhereRaw('LOWER(hostname) = ?', ['http://' . $hostname])
            ->first();
    }

    public function currentChannel(): ?object
    {
        $req = request();

        if ($req->attributes->has('graphql_channel')) {
            return $req->attributes->get('graphql_channel');
        }

        if ($code = $req->header('x-channel')) {
            return $this->channels->findOneByField('code', $code);
        }

        // Fallback to host matching
        $reqHost = $this->canonicalHost($req->getHttpHost());
        $all = $this->channels->getModel()->newQuery()->get();

        return $all->first(function ($ch) use ($reqHost) {
            $stored = $this->extractHost((string) $ch->hostname);
            return $stored && $this->canonicalHost($stored) === $reqHost;
        });
    }

    private function canonicalHost(string $host): string
    {
        $host = strtolower(trim($host));

        if (str_starts_with($host, 'http://') || str_starts_with($host, 'https://')) {
            $host = parse_url($host, PHP_URL_HOST) ?: $host;
        }

        $host = explode(':', $host)[0];

        if (str_starts_with($host, 'www.')) {
            $host = substr($host, 4);
        }

        return rtrim($host, '/');
    }

    private function extractHost(string $stored): ?string
    {
        $stored = trim($stored);

        if (preg_match('#^https?://#i', $stored)) {
            return parse_url($stored, PHP_URL_HOST) ?: null;
        }

        $parts = explode('/', $stored, 2);

        return $parts[0] ?: null;
    }
}
