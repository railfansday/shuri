<?php

namespace App\Exporters;
use League\Csv\Writer;

class CsvExporter
{
    public function export($posts, $index)
    {
        $download_path = base_path("exports/csv-" . $index . ".csv");
        $writer = Writer::createFromPath($download_path, "w+");

        $writer->insertOne(
            collect($posts[0]->toArray())
                ->keys()
                ->all()
        );

        $writer->insertAll(
            collect($posts)
                ->map(function ($post) {
                    $post = $post->setPublicationDate();
                    $post = $post->toArray();
                    $post["keyword"] = $post["keyword"]["name"];
                    $post["tags"] = json_encode($post["tags"]);
                    $post["category"] = config("export.category");

                    return $post;
                })
                ->toArray()
        );
    }
}
