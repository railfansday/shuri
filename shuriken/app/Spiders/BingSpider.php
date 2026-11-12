<?php

namespace App\Spiders;

use App\Image;
use App\Keyword;
use App\Post;
use Buchin\Badwords\Badwords;
use Carbon\Carbon;
use Codedungeon\PHPCliColors\Color;
use RoachPHP\Http\Request;
use RoachPHP\Http\Response;
use RoachPHP\Spider\BasicSpider;

class BingSpider extends BasicSpider
{
    public $cli;

    public $count = 0;

    public array $downloaderMiddleware = [];

    public int $concurrency = 10;
    public int $requestDelay = 0;

    public array $extensions = [];

    public function scrapeImages(Response $response)
    {
        $results = $response->filter(".imgpt")->each(function ($node) {
            $json = json_decode($node->filter("a.iusc")->attr("m"));
            try {
                $size = $node->filter("div.img_info span.nowrap")->html();
            } catch (\InvalidArgumentException $e) {
                // I guess its InvalidArgumentException in this case
                $size = "0 x 0";
            }

            try {
                return [
                    "url" => $json->murl,
                    "link" => $json->purl,
                    "title" => str_replace(["", "", " ..."], "", $json->t),
                    "thumbnail" => $json->turl,
                    "size" => $size,
                    "desc" => $json->desc,
                ];
            }
            catch (\Exception $e){
                return [];
            }
        });

        $results = collect($results)
            ->filter()
            ->unique("url")
            ->toArray();

        $results = $this->postProcessImage($results);

        return $results;
    }

    public function postProcessImage($raw_images)
    {
        $images = [];
        foreach ($raw_images as $raw_image) {
            $image = $this->postProcessSingleImage($raw_image);

            $images[] = $image;
        }

        return $images;
    }

    public function postProcessSingleImage($raw_image, $delimiter = ".")
    {
        $urlparts = explode($delimiter, $raw_image["url"]);

        $raw_image["filetype"] = $urlparts[count($urlparts)-1];
        $raw_image["filetype"] =
            $raw_image["filetype"] == "jpeg" ? "jpg" : $raw_image["filetype"];
        $raw_image["filetype"] =
            $raw_image["filetype"] == "animatedgif"
                ? "gif"
                : $raw_image["filetype"];

        try {
            $parts = explode("×", explode($delimiter, $raw_image["size"])[0]);
            $raw_image["width"] = trim($parts[0]);
            $raw_image["height"] = trim($parts[1]);
        }
        catch (\Exception $e)
        {
            $raw_image["width"] = 0;
            $raw_image["height"] = 0;
        }

        $raw_image["domain"] = parse_url($raw_image["link"], PHP_URL_HOST);

        return $raw_image;
    }

    public function scrapeKeywords(Response $response, $keyword = null): array
    {
        $keywords = $response
            ->filter("div.inlinecap a span.itm span[title], div.tittxt")
            ->each(function ($node) {
                $title = $node->attr("title");
                return strtolower($title ?: $node->text());
            });

        echo "    ", Color::black(), Color::bg_light_cyan(), "->", Color::RESET, Color::green(), " Keywords broaden ", Color::reset(), Color::yellow(), "(by ", count($keywords), ")", Color::RESET, PHP_EOL;
        return $keywords;
    }

    /** @return Request[] */
    protected function initialRequests(): array
    {
        $this->cli = config("shuriken.cli");

        $keyword = Keyword::whereNull('scraped_at')->orderBy('id')->first();

        if (!$keyword) {
            return [];
        }

        $keyword->scraped_at = Carbon::now();
        $keyword->save();

        return [
            new Request(
                "GET",
                self::buildUrl($keyword),
                [$this, "parse"],
                $this->getOptions($keyword)
            ),
        ];
    }

    public function parse(Response $response): \Generator
    {
        $keyword = $response->getRequest()->getOptions()["keyword"];

        $this->before($response, $keyword);

        $this->saveImages($response, $keyword);

        $this->after($response, $keyword);

        $max = config("shuriken.max");
        $current_count = cache("keywords_total", 0);

        if ($current_count < $max) {
            $new_keywords = [];

            if(!config('shuriken.no_suggest')){
                $new_keywords = $keyword->suggest();
            }

            if(!config('shuriken.no_expand')){
                $new_keywords = array_merge(
                    $new_keywords,
                    $this->scrapeKeywords($response, $keyword)
                );
            }

            $existing = Keyword::pluck('name')->toArray();
            $new_keywords = array_values(array_diff(array_unique($new_keywords), $existing));

            if (count($new_keywords) + $current_count > $max) {
                $diff = $max - $current_count;

                $new_keywords = collect($new_keywords)
                    ->take($diff)
                    ->toArray();
            }

            Keyword::importArray($new_keywords, $keyword);
        }

        foreach (
            Keyword::whereNull("scraped_at")
                ->orderBy("id", "asc")
                ->get()
            as $keyword
        ) {
            yield $this->request(
                "GET",
                self::buildUrl($keyword),
                "parse",
                $this->getOptions($keyword)
            );

            $keyword->scraped_at = Carbon::now();
            $keyword->save();
        }
    }

    public static function buildUrl(Keyword $keyword)
    {
        $queryString = parse_url(config("shuriken.preset"), PHP_URL_QUERY);

        parse_str($queryString, $queries);

        $q = [
            "q" => "",
            "qft" => "",
        ];
        $q["q"] = $keyword->name;

        if (!empty($queries["q"])) {
            $q["q"] = $q["q"] . " " . $queries["q"];
        }
        if (!empty($queries["qft"])) {
            $q["qft"] = $queries["qft"];
        }

        if (!empty(config("shuriken.append_query"))) {
            $q["q"] = $q["q"] . " " . config("shuriken.append_query");
        }

        if (!empty(config("shuriken.qft"))) {
            $q["qft"] = $q["qft"] . config("shuriken.qft");
        }

        $lang    = config("shuriken.language", "en");
        $country = strtoupper(config("shuriken.country", "us"));
        $q["mkt"] = "{$lang}-{$country}";

        $url =
            "https://www.bing.com/images/async?" .
            str_replace("%3A", ":", http_build_query($q));

        return $url;
    }

    public function saveImages(Response $response, $keyword)
    {
        if (config('shuriken.custom_images')) {
            return; // images.txt mode — skip Bing image saving
        }

        $images = collect($this->scrapeImages($response))
            ->map(function ($item) use ($keyword) {
                $item["keyword_id"] = $keyword->id;

                return $item;
            })
            ->reject(function ($item) {
                return Badwords::isDirty($item["title"]) || Badwords::isDirty($item["desc"]);
            })
            ->take(config("shuriken.image_count"));

        Image::insert($images->toArray());
    }

    private function getOptions(mixed $keyword)
    {
        $lang = config("shuriken.language", "en");

        $options = [
            "keyword" => $keyword,
            "count"   => $this->count,
            "headers" => [
                "User-Agent"      => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36",
                "Accept-Language" => "{$lang},{$lang};q=0.9,en;q=0.8",
                "Accept"          => "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
            ],
        ];

        if (config("shuriken.proxy")) {
            $options["proxy"] = config("shuriken.proxy");
        }

        if (config("shuriken.timeout")) {
            $options["timeout"] = config("shuriken.timeout");
        }

        return $options;
    }

    public function before(Response $response, Keyword $keyword)
    {
        $this->count++;
        cache(["keywords_count" => $this->count]);
        $total = Keyword::count();
        $this->cli->line("<question>#" . $this->count . "/{$total}</question> " . $keyword->name);
    }

    public function after(Response $response, Keyword $keyword)
    {
        $keyword->load('images');

        $customUrls = config('shuriken.custom_images');

        if ($customUrls) {
            $perPost = (int) config('shuriken.images_per_post', 5);
            $selected = collect($customUrls)->shuffle()->take($perPost);

            $records = $selected->map(function ($url) use ($keyword) {
                $filename = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_FILENAME);
                $caption  = ucwords(str_replace(['-', '_'], ' ', $filename));
                $domain   = parse_url($url, PHP_URL_HOST) ?? '';

                return [
                    'keyword_id' => $keyword->id,
                    'url'        => $url,
                    'link'       => $url,
                    'title'      => $caption,
                    'thumbnail'  => $url,
                    'size'       => '',
                    'desc'       => $caption,
                    'domain'     => $domain,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->all();

            Image::insert($records);
            $keyword->load('images');

            $this->cli->info('    <question>-></question> Custom images injected <comment>(Got ' . $keyword->images->count() . ' images from images.txt)</comment>');
        } elseif ($keyword->images->count()) {
            $this->cli->info('    <question>-></question> Images scraped <comment>(Got ' . $keyword->images->count() . ' images)</comment>');
        }

        if ($keyword->images->count()) {
            Post::regenerate($keyword, config('shuriken.template', 'image'));
            if(config('shuriken.ai')){
                $this->cli->info('    <question>-></question> Content generated <comment>(Using AI)</comment>');
            }
            else{
                $this->cli->info('    <question>-></question> Content generated <comment>(Using Blade)</comment>');
            }
        }
    }
}