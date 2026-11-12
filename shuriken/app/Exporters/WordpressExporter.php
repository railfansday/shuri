<?php

namespace App\Exporters;

class WordpressExporter
{
    public function export($posts, $index)
    {
        $content = view("export.wordpress", ["posts" => $posts])->render();

        file_put_contents(
            base_path("exports/wordpress-" . $index . ".xml"),
            $content
        );
    }
}
