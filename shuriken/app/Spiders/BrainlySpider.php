<?php

namespace App\Spiders;

use App\Answer;
use App\Image;
use App\Keyword;
use App\Post;
use Buchin\Badwords\Badwords;
use Carbon\Carbon;
use Illuminate\Support\Str;
use RoachPHP\Http\Request;
use RoachPHP\Http\Response;
use RoachPHP\Spider\BasicSpider;

class BrainlySpider extends BingSpider
{
    public function before(Response $response, Keyword $keyword)
    {
    }

    public function after(Response $response, Keyword $keyword)
    {
        $questions = $this->getResults($keyword->name);

        $questions = collect($questions)->map(function ($item) use ($keyword) {
            $item["title"] = \str(
                preg_replace(
                    [
                        "/<(?:br|p)[^>]*>/i", //replace br p with ' '
                        "/<[^>]*>/", //replace any tag with ''
                        "/\s+/", //remove run on space
                        '/^\s+|\s+$/', //trim
                    ],
                    [" ", "", " ", ""],
                    $item["question"]
                )
            )->limit();

            return $item;
        });

        foreach ($questions as $key => $question) {
            $new_keyword = Keyword::firstOrCreate(
                [
                    "name" => $question["title"],
                ],
                [
                    "scraped_at" => Carbon::now(),
                ]
            );

            if ($new_keyword->wasRecentlyCreated) {
                $this->count++;
                cache(["keywords_count" => $this->count]);
                $this->cli->info(
                    "#" . $this->count . " | " . $new_keyword->name
                );

                $question["keyword_id"] = $new_keyword->id;

                $answer = Answer::create($question);

                $images = $keyword->images
                    ->map(function ($item) {
                        unset($item->id);
                        unset($item->keyword_id);
                        return $item;
                    })
                    ->toArray();

                $new_keyword->images()->createMany($images);

                $post = Post::regenerate(
                    $new_keyword,
                    "brainly_" . config("shuriken.country")
                );
            }
        }
    }

    public function buildQuery($query)
    {
        return json_encode([
            "operationName" => "SearchQuery",
            "variables" => [
                "query" => $query,
                "after" => null,
                "first" => 100,
            ],
            "query" => self::GRAPHQL_PAYLOAD,
        ]);
    }

    public function getResults($keyword)
    {
        $ch = curl_init("https://brainly.co.id/graphql/id?op=SearchQuery");
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => [
                "Accept-Encoding: gzip",
                "Content-Type: application/json",
                "Origin: https://brainly.co.id",
            ],
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $this->buildQuery($keyword),
            CURLOPT_USERAGENT => "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:65.0) Gecko/20100101 Firefox/65.0",
            CURLOPT_REFERER =>
                "https://brainly.co.id/app/ask?entry=top&q=" .
                urlencode($keyword),
            CURLOPT_TIMEOUT => 120,
            CURLOPT_CONNECTTIMEOUT => 120,
        ]);
        $out = curl_exec($ch);

        $hdrSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $hdr = substr($out, 0, $hdrSize);
        $out = substr($out, $hdrSize);
        curl_close($ch);

        if (preg_match("/content-encoding: gzip/i", $hdr)) {
            $out = gzdecode($out);
        }

        $out = self::parseOut($out);

        return $out;
    }

    private static function parseOut($out)
    {
        $result = [];

        $out = json_decode($out, true);

        if (!is_array($out)) {
            goto ret;
        }

        if (!isset($out["data"]["questionSearch"]["edges"])) {
            goto ret;
        }

        $edges = $out["data"]["questionSearch"]["edges"];
        unset($out);

        if (!is_array($edges)) {
            goto ret;
        }

        foreach ($edges as $r) {
            $answers = [];
            if (!isset($r["node"]["answers"]["nodes"])) {
                goto fcon;
            }

            $nodes = $r["node"]["answers"]["nodes"];
            if (!is_array($nodes)) {
                goto fcon;
            }

            foreach ($nodes as $rr) {
                $answers[] = $rr["content"];
            }

            fcon:
            $result[] = [
                "url" =>
                    "https://brainly.co.id/tugas/" . $r["node"]["databaseId"],
                "question" => $r["node"]["content"],
                "answers" => $answers,
            ];
        }

        ret:
        return $result;
    }

    public const GRAPHQL_PAYLOAD = <<<'GRAPHQL_PAYLOAD'
query SearchQuery($query: String!, $first: Int!, $after: ID) {
  questionSearch(query: $query, first: $first, after: $after) {
    count
    edges {
      node {
        id
        databaseId
        author {
          id
          databaseId
          isDeleted
          nick
          avatar {
            thumbnailUrl
            __typename
          }
          rank {
            name
            __typename
          }
          __typename
        }
        content
        answers {
          nodes {
            thanksCount
            ratesCount
            rating
            content
            __typename
          }
          hasVerified
          __typename
        }
        __typename
      }
      highlight {
        contentFragments
        __typename
      }
      __typename
    }
    __typename
  }
}
GRAPHQL_PAYLOAD;
}
