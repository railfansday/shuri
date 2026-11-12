<?php

namespace App\Concerns;

use App\Publishers\CloudflarePagesPublisher;
use App\Publishers\DeployerInterface;
use App\Publishers\GitHubPagesPublisher;
use App\Publishers\NetlifyPublisher;
use App\Publishers\PublisherInterface;
use App\Publishers\SurgePublisher;
use App\Publishers\VercelPublisher;
use App\Publishers\WordPressPublisher;

trait ManagesSites
{
    protected function loadSites(): array
    {
        $path = base_path('storage/sites.json');
        if (!file_exists($path)) {
            return [];
        }
        return json_decode(file_get_contents($path), true) ?? [];
    }

    protected function saveSites(array $sites): void
    {
        $path = base_path('storage/sites.json');
        file_put_contents($path, json_encode($sites, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n");
    }

    protected function makePublisher(string $platform, array $config): PublisherInterface
    {
        return match ($platform) {
            'wordpress' => new WordPressPublisher($config),
            default     => throw new \RuntimeException("Unsupported platform for publish: {$platform}"),
        };
    }

    protected function makeDeployer(string $platform, array $config): DeployerInterface
    {
        return match ($platform) {
            'netlify'          => new NetlifyPublisher($config),
            'cloudflare-pages' => new CloudflarePagesPublisher($config),
            'vercel'           => new VercelPublisher($config),
            'surge'            => new SurgePublisher($config),
            'github-pages'     => new GitHubPagesPublisher($config),
            default            => throw new \RuntimeException("Platform '{$platform}' does not support deploy. Use post:publish instead."),
        };
    }

    protected function makeAnyPublisher(string $platform, array $config): PublisherInterface|DeployerInterface
    {
        return match ($platform) {
            'wordpress'        => new WordPressPublisher($config),
            'netlify'          => new NetlifyPublisher($config),
            'cloudflare-pages' => new CloudflarePagesPublisher($config),
            'vercel'           => new VercelPublisher($config),
            'surge'            => new SurgePublisher($config),
            'github-pages'     => new GitHubPagesPublisher($config),
            default            => throw new \RuntimeException("Unsupported platform: {$platform}"),
        };
    }

    protected function siteDisplayLabel(string $platform, array $config): string
    {
        return match ($platform) {
            'wordpress'        => $config['url'] ?? '',
            'netlify'          => 'site_id=' . ($config['site_id'] ?? ''),
            'cloudflare-pages' => ($config['project_name'] ?? '') . '.pages.dev',
            'vercel'           => ($config['project_name'] ?? '') . '.vercel.app',
            'surge'            => $config['domain'] ?? '',
            'github-pages'     => ($config['owner'] ?? '') . '/' . ($config['repo'] ?? ''),
            default            => '',
        };
    }
}
