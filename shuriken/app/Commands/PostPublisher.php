<?php

namespace App\Commands;

use App\Concerns\ManagesSites;
use App\Post;
use LaravelZero\Framework\Commands\Command;

class PostPublisher extends Command
{
    use ManagesSites;
    protected $signature = 'post:publish
        {--site= : Site alias dari storage/sites.json}
        {--limit=50 : Jumlah post per run}
        {--start= : Filter tanggal mulai (Y-m-d)}
        {--end= : Filter tanggal selesai (Y-m-d)}
        {--status=publish : Status post (publish/draft)}
    ';

    protected $description = 'Publish posts to WordPress or other platforms via API';

    public function handle()
    {
        $siteName = $this->option('site');

        if (!$siteName) {
            $this->error('Please specify a site: php shuriken post:publish --site={name}');
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
        $platform = $config['platform'] ?? 'wordpress';

        if ($platform !== 'wordpress') {
            $this->error("post:publish only supports WordPress. Use site:deploy for '{$platform}'.");
            return 1;
        }

        // Test connection first
        $this->line("Connecting to <info>{$siteName}</info> [{$platform}]...");
        try {
            $publisher = $this->makePublisher($platform, $config);
            $user      = $publisher->test();
            $this->line("  <info>✓</info> Connected as: <comment>{$user}</comment>");
        } catch (\Throwable $e) {
            $this->error("Connection failed: " . $e->getMessage());
            return 1;
        }

        // Query posts
        $query = Post::with('keyword')
            ->whereNotNull('title')
            ->orderBy('published_at');

        if ($this->option('start')) {
            $query->where('published_at', '>=', $this->option('start'));
        }
        if ($this->option('end')) {
            $query->where('published_at', '<=', $this->option('end') . ' 23:59:59');
        }

        $limit = (int) $this->option('limit');
        $posts = $query->limit($limit)->get();

        if ($posts->isEmpty()) {
            $this->warn('No posts found matching the given filters.');
            return 0;
        }

        $this->line('');
        $label = $this->siteDisplayLabel($platform, $config);
        $this->line("Publishing <comment>{$posts->count()}</comment> posts to <info>{$label}</info>...");
        $this->line('');

        $success = 0;
        $failed  = 0;
        $options = ['status' => $this->option('status')];

        foreach ($posts as $i => $post) {
            $num   = str_pad($i + 1, strlen((string) $posts->count()), ' ', STR_PAD_LEFT);
            $title = \Illuminate\Support\Str::of($post->title)->limit(55);

            $this->task("[{$num}/{$posts->count()}] {$title}", function () use ($post, $publisher, $options, &$success, &$failed) {
                try {
                    $publisher->publish($post, $options);
                    $success++;
                    return true;
                } catch (\Throwable $e) {
                    $failed++;
                    $this->line("    <fg=red>✗ " . $e->getMessage() . "</>");
                    return false;
                }
            });
        }

        $this->line('');
        $this->info("Done. {$success} published" . ($failed > 0 ? ", <fg=red>{$failed} failed</>" : '') . '.');

        return $failed > 0 ? 1 : 0;
    }

}
