<?php

namespace App\Commands;

use App\Helper;
use App\Keyword;
use App\Spiders\BingSpider;
use App\Spiders\BingSpiderLimiterMiddleware;
use App\Spiders\RequestLimiterMiddleware;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use LaravelZero\Framework\Commands\Command;
use RoachPHP\Downloader\Middleware\ExecuteJavascriptMiddleware;
use RoachPHP\Roach;
use RoachPHP\Spider\Configuration\Overrides;

class ImageScraper extends Command
{
    public $spider = BingSpider::class;
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'image:scrape
    {--language= : The target language (optional)}
    {--country= : The target country (optional)}
    {--proxy= : Use proxy to scrape}
    {--keywords_txt : Enable keywords from keyword.txt}
    {--trends= : Enable trending keywords, see https://tools.dojo.cc/tools/trending-keywords for country code}
    {--keywords= : Enable import keywords from cli, separated by comma}
    {--max= : Maximum keywords to be scraped}
    {--execute_javascript : Get more relevant data but slower scraping time}
    {--concurrency= : Scrape in parallel}
    {--requestDelay= : Delay between scraping}
    {--append_query= : Additional query which will be appended each request}
    {--qft= : Custom filter from bing}
    {--preset= : Auto configure append query dan qft pake url bing}
    {--clear_data : Clear database before run}
    {--image_count= : 0-35, above 9 images usually will require --execute_javascript}
    {--timeout= : maximum wait time each scraping in seconds}
    {--ai : create content using AI, ignoring all blade templates }
    {--provider= : AI provider to use: openai, gemini, claude, deepseek, groq, openrouter, kimi, grok, mistral, together, perplexity, fireworks, cerebras, siliconflow, novita, ollama, lmstudio, claude-cli, gemini-cli. Auto-detected if not specified.}
    {--model= : Override the default AI model for the selected provider}
    {--memory_db : using in memory database to fasten the process }
    {--prompt= : using in memory database to fasten the process }
    {--no_expand : disable expand keywords using related keyword found on image search }
    {--no_suggest : disable adding new keyword via bing suggest }
    {--images_txt : Use images.txt as image source instead of Bing scraped images}
    {--images_per_post=5 : Number of images per post when using --images_txt}
    {--template=image : Template folder to use for content generation (e.g. image, pfp)}
    ';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = "Scrape images and sentences";

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $start = Carbon::now();

        $this->line('
   _____ __               _ __                   _____
  / ___// /_  __  _______(_) /_____  ____       |___ /
  \__ \/ __ \/ / / / ___/ / //_/ _ \/ __ \        / /
 ___/ / / / / /_/ / /  / / ,< /  __/ / / /       / /
/____/_/ /_/\__,_/_/  /_/_/|_|\___/_/ /_/       /_/
======== https://shuriken.dojo.cc ============
');
        sleep(3);

        Helper::mergeConfig("shuriken", $this->option());

        if (config('shuriken.images_txt')) {
            $path = base_path('images.txt');
            if (!file_exists($path)) {
                $this->error('images.txt not found in Shuriken root directory.');
                return 1;
            }
            $urls = array_values(array_filter(array_map('trim', file($path))));
            if (empty($urls)) {
                $this->error('images.txt is empty.');
                return 1;
            }
            config([
                'shuriken.custom_images'    => $urls,
                'shuriken.images_per_post'  => (int) $this->option('images_per_post'),
            ]);
            $this->info('Using images.txt — ' . count($urls) . ' images loaded.');
        }

        if (config('shuriken.ai') && !config('shuriken.provider')) {
            $provider = $this->autoDetectProvider();
            if (!$provider) {
                $this->error('No AI provider available. Add a token first:');
                $this->line('  php shuriken token add openai <your-token>');
                return 1;
            }
            $this->line("  <fg=gray>→</> No provider specified. Auto-selected: <comment>{$provider}</comment>");
            config(['shuriken.provider' => $provider]);
        }

        if(config('shuriken.memory_db')){
            config([
                "database.connections.sqlite.database" => ":memory:",
            ]);
        }

        $this->call("migrate");

        if (
            config("shuriken.keywords_txt") === false &&
            config("shuriken.keywords") === false &&
            config("shuriken.trends") === false
        ) {
            config(["shuriken.trends" => Omakase::trend()]);
        }

        $this->table(
            ["config", "value"],
            collect(config("shuriken"))
                ->reject(function ($item, $key) {
                    return $key === "cli";
                })
                ->map(function ($item, $key) {
                    return [
                        "param" => $key,
                        "value" => $item ? "<info>$item</info>" : "false",
                    ];
                })
                ->reject(function ($item, $key) {
                    return in_array($key, [
                        "help",
                        "ansi",
                        "quiet",
                        "verbose",
                        "version",
                        "no-interaction",
                        "env",
                    ]);
                })
        );

        config(["shuriken.cli" => $this]);

        if (config("shuriken.clear_data")) {
            $scraped = Keyword::whereNotNull('scraped_at')->count();
            if ($scraped > 0) {
                $this->line("<fg=yellow>⚠ --clear_data will reset {$scraped} scraped keywords. Progress will be lost.</>");
            }
            Artisan::call("clear:data");
            cache(["keywords_count" => 0]);
        } else {
            cache(["keywords_count" => 0]);
        }

        if(config('shuriken.ai')){
            $provider = config('shuriken.provider', 'openai');
            $tokenMap = [
                'openai'     => ['key' => 'ai.openai_tokens',     'msg' => 'ERROR: No openai token. Run: php shuriken token add openai <token>  (get token from https://platform.openai.com/account/api-keys)'],
                'gemini'     => ['key' => 'ai.gemini_tokens',     'msg' => 'ERROR: No gemini token. Run: php shuriken token add gemini <token>'],
                'claude'     => ['key' => 'ai.claude_tokens',     'msg' => 'ERROR: No claude token. Run: php shuriken token add claude <token>  (get token from https://console.anthropic.com)'],
                'deepseek'   => ['key' => 'ai.deepseek_tokens',   'msg' => 'ERROR: No deepseek token. Run: php shuriken token add deepseek <token>  (get token from https://platform.deepseek.com)'],
                'groq'       => ['key' => 'ai.groq_tokens',       'msg' => 'ERROR: No groq token. Run: php shuriken token add groq <token>  (get token from https://console.groq.com)'],
                'openrouter' => ['key' => 'ai.openrouter_tokens', 'msg' => 'ERROR: No openrouter token. Run: php shuriken token add openrouter <token>  (get token from https://openrouter.ai/keys)'],
                'kimi'       => ['key' => 'ai.kimi_tokens',       'msg' => 'ERROR: No kimi token. Run: php shuriken token add kimi <token>  (get token from https://platform.moonshot.cn)'],
                'grok'       => ['key' => 'ai.grok_tokens',       'msg' => 'ERROR: No grok token. Run: php shuriken token add grok <token>  (get token from https://console.x.ai)'],
                'mistral'    => ['key' => 'ai.mistral_tokens',    'msg' => 'ERROR: No mistral token. Run: php shuriken token add mistral <token>  (get token from https://console.mistral.ai)'],
                'together'   => ['key' => 'ai.together_tokens',   'msg' => 'ERROR: No together token. Run: php shuriken token add together <token>  (get token from https://api.together.ai)'],
                'perplexity' => ['key' => 'ai.perplexity_tokens', 'msg' => 'ERROR: No perplexity token. Run: php shuriken token add perplexity <token>  (get token from https://www.perplexity.ai/settings/api)'],
                'fireworks'  => ['key' => 'ai.fireworks_tokens',  'msg' => 'ERROR: No fireworks token. Run: php shuriken token add fireworks <token>  (get token from https://fireworks.ai/account/api-keys)'],
                'cerebras'   => ['key' => 'ai.cerebras_tokens',   'msg' => 'ERROR: No cerebras token. Run: php shuriken token add cerebras <token>  (get token from https://cloud.cerebras.ai)'],
                'siliconflow' => ['key' => 'ai.siliconflow_tokens', 'msg' => 'ERROR: No siliconflow token. Run: php shuriken token add siliconflow <token>  (get token from https://cloud.siliconflow.cn/account/ak)'],
                'novita'     => ['key' => 'ai.novita_tokens',     'msg' => 'ERROR: No novita token. Run: php shuriken token add novita <token>  (get token from https://novita.ai/settings/key-management)'],
            ];

            // Local and CLI providers don't require tokens
            $cliProviders = ['claude-cli', 'gemini-cli', 'ollama', 'lmstudio'];

            if (!isset($tokenMap[$provider]) && !in_array($provider, $cliProviders)) {
                $supported = implode(', ', array_merge(array_keys($tokenMap), $cliProviders));
                $this->error("ERROR: Unknown AI provider '$provider'. Supported: $supported");
                return 1;
            }

            if (isset($tokenMap[$provider]) && count(config($tokenMap[$provider]['key'])) < 1) {
                $this->error($tokenMap[$provider]['msg']);
                return 1;
            }
        }

        if (
            config("shuriken.image_count") > 9 &&
            config("shuriken.execute_javascript") === false
        ) {
            $this->info(
                "Usually you need to call --execute_javascript to get more than 9 images, but scraping time will be slower."
            );
        }

        //      Let's import all keywords
        Keyword::import();

        // Reset scraped_at for keywords that have no images (failed/incomplete scrapes)
        Keyword::doesntHave('images')->update(['scraped_at' => null]);

        // Trim excess keywords beyond max — delete unscraped ones (highest IDs first)
        $max    = config('shuriken.max');
        $excess = Keyword::count() - $max;
        if ($excess > 0) {
            Keyword::doesntHave('images')->orderBy('id', 'desc')->limit($excess)->delete();
        }

        $total     = Keyword::count();
        $scraped   = Keyword::has('images')->count();
        $remaining = $total - $scraped;

        if ($scraped > 0 && $remaining > 0) {
            $this->line("Keywords: {$total} total — {$scraped} scraped, <comment>{$remaining} remaining</comment>");
            $this->line("Resuming...");
        } elseif ($remaining === 0 && $total > 0) {
            $this->line("Keywords: {$total} total — all scraped. Nothing to do.");
        } else {
            $this->line("Keywords: {$total} total — fresh start");
        }

        cache(["keywords_total" => $total]);

        $downloaderMiddleware = [RequestLimiterMiddleware::class];

        if (config("shuriken.execute_javascript")) {
            $downloaderMiddleware[] = ExecuteJavascriptMiddleware::class;
        }

        Roach::startSpider(
            $this->spider,
            new Overrides(
                downloaderMiddleware: $downloaderMiddleware,
                concurrency: config("shuriken.concurrency"),
                requestDelay: config("shuriken.requestDelay")
            )
        );

        if(config('shuriken.memory_db')){
            if (file_exists("database/database.sqlite")) {
                unlink("database/database.sqlite");
            }

            DB::statement("vacuum main into 'database/database.sqlite'");
        }

        $end = Carbon::now();

        $this->info("Scraping finished in " . $end->diffForHumans($start));

        $this->table(
            ['Metric', 'Count'],
            [
                ['Keywords',              Keyword::count()],
                ['Keywords with images',  Keyword::has('images')->count()],
                ['Keywords with posts',   Keyword::has('posts')->count()],
                ['Images',                \App\Image::count()],
                ['Posts',                 \App\Post::count()],
            ]
        );
    }

    private function autoDetectProvider(): ?string
    {
        $priority = [
            'openai', 'gemini', 'claude', 'groq', 'deepseek', 'openrouter',
            'kimi', 'grok', 'mistral', 'together', 'perplexity',
        ];

        foreach ($priority as $p) {
            if (!empty(config("ai.{$p}_tokens", []))) {
                return $p;
            }
        }

        // Local/CLI providers need no token — use as last resort
        foreach (['ollama', 'lmstudio', 'claude-cli', 'gemini-cli'] as $p) {
            return $p;
        }

        return null;
    }

}