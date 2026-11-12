<?php

namespace App\Commands;

use App\Concerns\ManagesSites;
use Illuminate\Support\Facades\Artisan;
use LaravelZero\Framework\Commands\Command;

class SiteDeployer extends Command
{
    use ManagesSites;
    protected $signature = 'site:deploy
        {--site= : Site alias dari storage/sites.json}
        {--theme= : Export HTML dengan tema ini sebelum deploy}
        {--zip= : Path ZIP manual (default: exports/html-1.zip)}
    ';

    protected $description = 'Deploy HTML export ke Netlify atau Cloudflare Pages';

    public function handle()
    {
        $siteName = $this->option('site');

        if (!$siteName) {
            $this->error('Please specify a site: php shuriken site:deploy --site={name}');
            $this->line('Run <comment>php shuriken site list</comment> to see configured sites.');
            return 1;
        }

        $sites = $this->loadSites();

        if (!isset($sites[$siteName])) {
            $this->error("Site '{$siteName}' not found.");
            $this->line('Run <comment>php shuriken site list</comment> to see configured sites.');
            return 1;
        }

        $config   = $sites[$siteName];
        $platform = $config['platform'];

        // Optional: export first if --theme is given
        if ($theme = $this->option('theme')) {
            $this->line("Exporting HTML with theme <comment>{$theme}</comment>...");
            Artisan::call('image:export', [
                '--platform' => 'html',
                '--theme'    => $theme,
            ]);
            $this->line('  <info>✓</info> Export done.');
            $this->line('');
        }

        // Resolve ZIP path
        $zipPath = $this->option('zip') ?? base_path('exports/html-1.zip');

        if (!file_exists($zipPath)) {
            $this->error("ZIP not found: {$zipPath}");
            $this->line('Run <comment>php shuriken image:export --platform=html</comment> first, or use --theme to export automatically.');
            return 1;
        }

        $size = round(filesize($zipPath) / 1024 / 1024, 2);
        $this->line("Deploying <info>{$siteName}</info> [{$platform}] ({$size} MB)...");

        $start = microtime(true);
        try {
            $deployer = $this->makeDeployer($platform, $config);
            $url      = $deployer->deploy($zipPath);
            $ms       = round((microtime(true) - $start) * 1000);
            $this->line("  <info>✓</info> Live at: <comment>{$url}</comment> ({$ms}ms)");
        } catch (\Throwable $e) {
            $this->error("Deploy failed: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

}
