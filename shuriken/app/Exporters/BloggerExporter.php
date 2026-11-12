<?php

namespace App\Exporters;

class BloggerExporter
{
    public function export($posts, $index)
    {
        $content = view("export.blogger", ["posts" => $posts])->render();

        file_put_contents(
            base_path("exports/blogger-" . $index . ".xml"),
            $content
        );
    }
}
