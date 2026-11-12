<?php

namespace App\Exporters;

use Codedungeon\PHPCliColors\Color;
use PhpZip\ZipFile;

class HtmlExporter
{
    public ZipFile $zip;
    public array $pages = ["contact", "copyright", "dmca", "privacy-policy"];
    public $posts;
    public $sidebar_posts = [];
    public $post_index = 0;
    public const THEMES = [
        "default",
        "eight",
        "eight-dark",
        "eight-mini",
        "eight-mini-dark",
        "eleven",
        "eleven-dark",
        "eleven-mini",
        "eleven-mini-dark",
        "five",
        "five-dark",
        "five-mini",
        "five-mini-dark",
        "four",
        "four-dark",
        "four-mini",
        "four-mini-dark",
        "nine",
        "nine-dark",
        "nine-mini",
        "nine-mini-dark",
        "one",
        "one-dark",
        "one-mini",
        "one-mini-dark",
        "seven",
        "seven-dark",
        "seven-mini",
        "seven-mini-dark",
        "six",
        "six-dark",
        "six-mini",
        "six-mini-dark",
        "ten",
        "ten-dark",
        "ten-mini",
        "ten-mini-dark",
        "three",
        "three-dark",
        "three-mini",
        "three-mini-dark",
        "two",
        "two-dark",
        "two-mini",
        "two-mini-dark",
    ];

    public function export($posts, int $index)
    {
        $post_count =  count($posts);

        echo "\n    -> Post count: " . $post_count;

        $this->posts = $posts;
        $this->sidebar_posts = collect($posts)->take(10)->all();
        $this->zip = new ZipFile();

        if (config("export.theme") === "random") {
            config(["export.theme" => collect(self::THEMES)->random()]);
        }

        $this->theme = "export.html." . config("export.theme");

        $post = $posts[0];

        if (empty(config("export.site_name"))) {
            config(["export.site_name" => $post->keyword->name]);
        }

        if (empty(config("export.description"))) {
            config(["export.description" => $post->title]);
        }

        if (!empty($this->theme)) {
            $this->zip->addFromString(
                config("export.theme") . ".txt",
                $this->theme
            );
        }

        if (config('export.custom_images')) {
            $this->injectCustomImages($this->posts);
        }

        $this->generatePosts();
        $this->generatePages();
        $this->generateIndex();
        $this->generateSitemap();

        file_put_contents(base_path("exports/html-" . $index . ".zip"), $this->zip->outputAsString());
        $this->zip->close();
    }

    private function injectCustomImages($posts): void
    {
        $allUrls = config('export.custom_images');
        $perPost = config('export.images_per_post', 5);

        foreach ($posts as $post) {
            shuffle($allUrls);
            $selected = array_slice($allUrls, 0, $perPost);

            $images = collect($selected)->map(function ($url) {
                $filename = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_FILENAME);
                $caption  = ucwords(str_replace(['-', '_'], ' ', $filename));
                $domain   = parse_url($url, PHP_URL_HOST) ?? '';

                return new \Illuminate\Support\Fluent([
                    'url'       => $url,
                    'thumbnail' => $url,
                    'title'     => $caption,
                    'desc'      => $caption,
                    'domain'    => $domain,
                ]);
            });

            $post->keyword->setRelation('images', $images);
        }
    }

    public function generateIndex()
    {
        $content = view($this->theme . ".index", [
            "posts" => $this->posts,
            "pages" => $this->pages,
        ]);
        $this->zip->addFromString("index.html", $content);
    }

    public function generateSitemap()
    {
        $content = view("export.html.sitemap", [
            "posts" => $this->posts,
            "pages" => $this->pages,
        ]);
        $this->zip->addFromString("sitemap.xml", $content);
    }

    public function generatePages()
    {
        foreach ($this->pages as $page) {
            $content = view("export.html._pages." . $page, [
                "page" => $page,
                "posts" => $this->sidebar_posts,
                "pages" => $this->pages,
            ]);

            $this->zip->addFromString($page . ".html", $content);
        }
    }

    public function generatePosts()
    {
        $inArticleAd = view('export.html._ads.in_article')->render();

        foreach ($this->posts as $key => $post) {
            $post = $post->setPublicationDate();
            $post->category = config("export.category");
            $content = view($this->theme . ".post", [
                "post" => $post,
                "posts" => $this->sidebar_posts,
                "pages" => $this->pages,
            ])->render();

            if (config("export.theme" === "four")) {
                file_put_contents("bing.html", $content);
            }

            if (trim($inArticleAd)) {
                $content = $this->injectInArticleAd($content, $inArticleAd);
            }

            $this->add($post->slug . ".html", $content);
        }
    }

    private function injectInArticleAd(string $html, string $ad): string
    {
        $count = 0;
        return preg_replace_callback('/<\/p>/i', function ($m) use ($ad, &$count) {
            $count++;
            return $count === 2 ? '</p>' . $ad : '</p>';
        }, $html);
    }

    public function add($file, $content)
    {
        $this->post_index++;

        echo "\n" . '    -> ' . 'Exporting #' . $this->post_index . ' ' . Color::yellow() .$file . Color::RESET;
        $this->zip->addFromString($file, $content);
    }
}
