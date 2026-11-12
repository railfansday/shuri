<?php

namespace App\Commands;

use App\Concerns\ManagesSites;
use App\Exporters\BloggerExporter;
use App\Exporters\CsvExporter;
use App\Exporters\HtmlExporter;
use App\Exporters\HugoExporter;
use App\Exporters\NativeExporter;
use App\Exporters\WordpressExporter;
use App\Helper;
use App\Post;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;

class ImageExporter extends Command
{
    use ManagesSites;
    public $exporters = [
        "wordpress" => WordpressExporter::class,
        "blogger" => BloggerExporter::class,
        "hugo" => HugoExporter::class,
        "csv" => CsvExporter::class,
        "native" => NativeExporter::class,
        "html" => HtmlExporter::class,
    ];
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'image:export
    {--platform=wordpress : The target platform)}
    {--category= : Post will be categorized into this)}
    {--start= : Post will be published started at this date (Y-m-d) default last year}
    {--end= : Post will be published ended at this date (Y-m-d) default today}
    {--chunk=50000 : Maximum posts per file)}
    {--theme=default : Select theme to export (only for HTML export)}
    {--site_name= : Site name. Default: first keyword (only for HTML export)}
    {--description= : Site name. Default: random image description (only for HTML export)}
    {--base_url= : Base URL for sitemap (e.g. https://mysite.com). Auto-detected if --site is set}
    {--site= : Site alias — auto-derives base_url from site config}
    {--images_txt : Use images.txt as image source instead of Bing scraped images}
    {--images_per_post=5 : Number of images per post when using --images_txt}
    {--clear_post : Cleanup database dan folder data sebelum export}
    ';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = "Export content to various platform";

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $platform = $this->option("platform");

        if (!array_key_exists($platform, $this->exporters)) {
            $this->error("Platform '$platform' tidak dikenal. Pilihan: " . implode(', ', array_keys($this->exporters)));
            return 1;
        }

        $this->info('Starting Exporter');
        Helper::mergeConfig("export", $this->options());

        $baseUrl = $this->option('base_url') ?? $this->deriveBaseUrlFromSite($this->option('site'));
        if ($baseUrl) {
            config(['export.base_url' => rtrim($baseUrl, '/')]);
        }

        if ($this->option('images_txt')) {
            $path = base_path('images.txt');
            if (!file_exists($path)) {
                $this->error('images.txt not found in Shuriken root directory.');
                return 1;
            }
            $urls = array_filter(array_map('trim', file($path)));
            if (empty($urls)) {
                $this->error('images.txt is empty.');
                return 1;
            }
            config([
                'export.custom_images'       => array_values($urls),
                'export.images_per_post'     => (int) $this->option('images_per_post'),
            ]);
            $this->info('Using images.txt — ' . count($urls) . ' images loaded.');
        }

        config([
            "database.connections.sqlite.database" => database_path(
                "database.sqlite"
            ),
        ]);

        if (config("export.clear_post")) {
            File::cleanDirectory("exports");
        }

        File::ensureDirectoryExists(base_path("exports"));

        $start = Carbon::now();

        $index = 0;

        Post::with("keyword", 'keyword.images')->chunk($this->option("chunk"), function (
            $posts
        ) use (&$index, $platform) {
            $index++;
            $this->task(
                "Exporting #" . $platform . "-" . $index,
                function () use ($posts, $index, $platform) {
                    $class = $this->exporters[$platform];

                    $exporter = new $class();

                    $exporter->export($posts, $index);
                }
            );
        });

        $end = Carbon::now();

        $this->info("Export finished in " . $end->diffForHumans($start));
    }

    private function deriveBaseUrlFromSite(?string $siteAlias): ?string
    {
        if (!$siteAlias) {
            return null;
        }

        $sites  = $this->loadSites();
        $config = $sites[$siteAlias] ?? null;

        if (!$config) {
            $this->warn("Site '{$siteAlias}' not found. Sitemap will use default base URL.");
            return null;
        }

        return match ($config['platform'] ?? '') {
            'cloudflare-pages' => 'https://' . ($config['project_name'] ?? '') . '.pages.dev',
            'vercel'           => 'https://' . ($config['project_name'] ?? '') . '.vercel.app',
            'netlify'          => 'https://' . ($config['site_id'] ?? '') . '.netlify.app',
            'surge'            => 'https://' . ($config['domain'] ?? ''),
            'github-pages'     => 'https://' . ($config['owner'] ?? '') . '.github.io/' . ($config['repo'] ?? ''),
            'wordpress'        => rtrim($config['url'] ?? '', '/'),
            default            => null,
        };
    }

}
