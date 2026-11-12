<?php

namespace App;

use Buchin\Badwords\Badwords;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use League\Csv\Reader;
use Codedungeon\PHPCliColors\Color;

class Keyword extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function txt()
    {
        $csv = Reader::createFromPath(base_path("keywords.txt"));

        $records = $csv->getRecords();

        $keywords = [];
        foreach ($records as $record) {
            $keywords[] = $record[0];
        }

        self::importArray($keywords);
    }

    public static function trends(string $country)
    {
        $response = json_decode(
            file_get_contents(
                "https://dojo.cc/api/trends/" . $country . "/keywords"
            ),
            true
        );

        self::importArray($response['keywords'] ?? []);
    }

    public static function manual()
    {
        $keywords = explode(",", config("shuriken.keywords"));

        self::importArray($keywords);
    }

    /**
     * @param array $keywords
     * @return void
     */
    public static function importArray(array $keywords, $keyword = null): void
    {
        $keywords = collect($keywords)
            ->map(function ($item) use ($keyword) {
                $record = [];
                $record["name"] = $item;
                $record["keyword_id"] = is_null($keyword) ? null : $keyword->id;

                return $record;
            })
            ->reject(function ($item) {
                return Badwords::isDirty($item["name"]);
            });

        $inserted = Keyword::insertOrIgnore($keywords->toArray());
        if ($inserted > 0) {
            cache()->increment('keywords_total', $inserted);
        }
    }

    /**
     * @return void
     */
    protected function import(): void
    {
        if (config("shuriken.keywords_txt")) {
            Keyword::txt();
        }

        if (config("shuriken.trends")) {
            Keyword::trends(config("shuriken.trends"));
        }

        if (config("shuriken.keywords")) {
            Keyword::manual();
        }
    }

    public function suggest()
    {
        return backoff(function () {
            $keywords = json_decode(file_get_contents('https://api.bing.com/osjson.aspx?query=' . urlencode($this->name)));

            echo "    ", Color::black(), Color::bg_light_cyan(), "->", Color::RESET, Color::green(), " Scraping keywords ", Color::reset(), Color::yellow(), "(Got ", count($keywords[1]), ")", Color::RESET, PHP_EOL;

            return $keywords[1] ?? [];
        });
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function answer()
    {
        return $this->hasOne(Answer::class);
    }

    public function sentences($count = null, bool $as_collection = false)
    {
        $images = $this->images;
        $sentences = $images
            ->pluck("desc")
            ->merge($images->pluck("title"))
            ->map(function ($item) {
                return str($item)
                    ->before(" | ")
                    ->before(" - ")
                    ->lower()
                    ->ucfirst()
                    ->toString();
            })
            ->shuffle()
            ->reject(function ($item) {
                return str($item)->wordCount() < 2;
            });

        if (is_null($count)) {
            $count = $sentences->count();
        }

        if ($as_collection) {
            return $sentences->take($count);
        }

        return $sentences->take($count)->implode(". ");
    }

    public function related_keywords()
    {
        return $this->hasMany(Keyword::class);
    }

    public function parent()
    {
        return $this->belongsTo(Keyword::class);
    }
}