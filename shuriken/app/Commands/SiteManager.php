<?php

namespace App\Commands;

use App\Concerns\ManagesSites;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;

class SiteManager extends Command
{
    use ManagesSites;
    const PLATFORMS = ['wordpress', 'netlify', 'cloudflare-pages', 'vercel', 'surge', 'github-pages'];

    protected $signature = 'site
        {action : add, remove, list, or test}
        {name? : Site alias}
        {platform? : Platform (wordpress|netlify|cloudflare-pages|vercel)}
        {arg1? : URL (wordpress) | site_id (netlify) | account_id (cloudflare-pages) | project_name (vercel)}
        {arg2? : Username (wordpress) | api_token (netlify) | project_name (cloudflare-pages) | api_token (vercel)}
        {arg3? : Password (wordpress) | — | api_token (cloudflare-pages) | team_id (vercel)}
    ';

    protected $description = 'Manage publish destinations (add, remove, list, test)';

    public function handle()
    {
        $action = $this->argument('action');

        match ($action) {
            'list'   => $this->actionList(),
            'add'    => $this->actionAdd(),
            'remove' => $this->actionRemove(),
            'test'   => $this->actionTest(),
            default  => $this->error("Unknown action: {$action}. Use: add, remove, list, test"),
        };
    }

    private function actionList(): void
    {
        $sites = $this->loadSites();

        $this->line('');
        $this->line('<comment>Configured Sites</comment>');
        $this->line(str_repeat('─', 50));

        if (empty($sites)) {
            $this->line('  <fg=gray>No sites configured.</>');
            $this->line('');
            $this->line('  Add one: <comment>php shuriken site add {name} wordpress {url} {user} {password}</comment>');
        } else {
            foreach ($sites as $name => $config) {
                $platform = $config['platform'] ?? '?';
                $detail   = match ($platform) {
                    'wordpress'        => $config['url'] ?? '',
                    'netlify'          => 'site_id=' . ($config['site_id'] ?? ''),
                    'cloudflare-pages' => ($config['project_name'] ?? '') . '.pages.dev',
                    'vercel'           => ($config['project_name'] ?? '') . '.vercel.app',
                    'surge'            => $config['domain'] ?? '',
                    'github-pages'     => ($config['owner'] ?? '') . '/' . ($config['repo'] ?? ''),
                    default            => '',
                };
                $this->line("  <info>{$name}</info> <fg=gray>[{$platform}]</> {$detail}");
            }
        }

        $this->line('');
    }

    private function actionAdd(): void
    {
        $name     = $this->argument('name');
        $platform = $this->argument('platform');
        $arg1     = $this->argument('arg1');
        $arg2     = $this->argument('arg2');
        $arg3     = $this->argument('arg3');

        if (!$name || !$platform) {
            $this->error('Usage: php shuriken site add {name} {platform} ...');
            $this->printAddExamples();
            return;
        }

        if (!in_array($platform, self::PLATFORMS)) {
            $this->error("Unknown platform: {$platform}");
            $this->line('Supported: ' . implode(', ', self::PLATFORMS));
            return;
        }

        $sites       = $this->loadSites();
        $config      = ['platform' => $platform];

        switch ($platform) {
            case 'wordpress':
                if (!$arg1 || !$arg2 || !$arg3) {
                    $this->error('Usage: php shuriken site add {name} wordpress {url} {username} {password}');
                    return;
                }
                $config += ['url' => $arg1, 'username' => $arg2, 'password' => $arg3];
                $this->line("<info>✓</info> Site <comment>{$name}</comment> [wordpress] {$arg1}");
                break;

            case 'netlify':
                if (!$arg1 || !$arg2) {
                    $this->error('Usage: php shuriken site add {name} netlify {site_id} {api_token}');
                    return;
                }
                $config += ['site_id' => $arg1, 'api_token' => $arg2];
                $masked = Str::of($arg2)->limit(6, '***');
                $this->line("<info>✓</info> Site <comment>{$name}</comment> [netlify] site_id={$arg1} token={$masked}");
                break;

            case 'cloudflare-pages':
                if (!$arg1 || !$arg2 || !$arg3) {
                    $this->error('Usage: php shuriken site add {name} cloudflare-pages {account_id} {project_name} {api_token}');
                    return;
                }
                $config += ['account_id' => $arg1, 'project_name' => $arg2, 'api_token' => $arg3];
                $masked = Str::of($arg3)->limit(6, '***');
                $this->line("<info>✓</info> Site <comment>{$name}</comment> [cloudflare-pages] {$arg2}.pages.dev token={$masked}");
                break;

            case 'vercel':
                if (!$arg1 || !$arg2) {
                    $this->error('Usage: php shuriken site add {name} vercel {project_name} {api_token} [team_id]');
                    return;
                }
                $config += ['project_name' => $arg1, 'api_token' => $arg2];
                if ($arg3) $config['team_id'] = $arg3;
                $masked = Str::of($arg2)->limit(6, '***');
                $this->line("<info>✓</info> Site <comment>{$name}</comment> [vercel] {$arg1}.vercel.app token={$masked}");
                break;

            case 'surge':
                if (!$arg1 || !$arg2) {
                    $this->error('Usage: php shuriken site add {name} surge {domain} {token}');
                    return;
                }
                $config += ['domain' => $arg1, 'token' => $arg2];
                $masked = Str::of($arg2)->limit(6, '***');
                $this->line("<info>✓</info> Site <comment>{$name}</comment> [surge] {$arg1} token={$masked}");
                break;

            case 'github-pages':
                if (!$arg1 || !$arg2 || !$arg3) {
                    $this->error('Usage: php shuriken site add {name} github-pages {owner} {repo} {token}');
                    return;
                }
                $config += ['owner' => $arg1, 'repo' => $arg2, 'token' => $arg3];
                $masked = Str::of($arg3)->limit(6, '***');
                $this->line("<info>✓</info> Site <comment>{$name}</comment> [github-pages] {$arg1}/{$arg2} token={$masked}");
                break;
        }

        $sites[$name] = $config;
        $this->saveSites($sites);
    }

    private function printAddExamples(): void
    {
        $this->line('');
        $this->line('  <comment>WordPress:</comment>');
        $this->line('    php shuriken site add myblog wordpress https://myblog.com admin "app-password"');
        $this->line('  <comment>Netlify:</comment>');
        $this->line('    php shuriken site add mysite netlify {site_id} {api_token}');
        $this->line('  <comment>Cloudflare Pages:</comment>');
        $this->line('    php shuriken site add mysite cloudflare-pages {account_id} {project_name} {api_token}');
        $this->line('  <comment>Vercel:</comment>');
        $this->line('    php shuriken site add mysite vercel {project_name} {api_token} [team_id]');
        $this->line('  <comment>Surge:</comment>');
        $this->line('    php shuriken site add mysite surge mysite.surge.sh {token}');
        $this->line('  <comment>GitHub Pages:</comment>');
        $this->line('    php shuriken site add mysite github-pages {owner} {repo} {token}');
    }

    private function actionRemove(): void
    {
        $name = $this->argument('name');

        if (!$name) {
            $this->error('Usage: php shuriken site remove {name}');
            return;
        }

        $sites = $this->loadSites();

        if (!isset($sites[$name])) {
            $this->error("Site '{$name}' not found.");
            if (!empty($sites)) {
                $this->line('Configured sites: ' . implode(', ', array_keys($sites)));
            }
            return;
        }

        unset($sites[$name]);
        $this->saveSites($sites);

        $this->line("<info>✓</info> Site <comment>{$name}</comment> removed.");
    }

    private function actionTest(): void
    {
        $name = $this->argument('name');

        if (!$name) {
            $this->error('Usage: php shuriken site test {name}');
            return;
        }

        $sites = $this->loadSites();

        if (!isset($sites[$name])) {
            $this->error("Site '{$name}' not found. Run: php shuriken site list");
            return;
        }

        $config   = $sites[$name];
        $platform = $config['platform'] ?? 'wordpress';

        $label = $this->siteDisplayLabel($platform, $config);
        $this->line("Testing <info>{$name}</info> [{$platform}] {$label}...");

        $start = microtime(true);
        try {
            $publisher = $this->makeAnyPublisher($platform, $config);
            $user      = $publisher->test();
            $ms        = round((microtime(true) - $start) * 1000);
            $this->line("<info>✓</info> Connected as: <comment>{$user}</comment> ({$ms}ms)");
        } catch (\Throwable $e) {
            $ms = round((microtime(true) - $start) * 1000);
            $this->error("✗ Failed ({$ms}ms): " . $e->getMessage());
        }
    }

}
