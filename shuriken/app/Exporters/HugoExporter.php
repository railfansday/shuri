<?php

namespace App\Exporters;

use Codedungeon\PHPCliColors\Color;
use PhpZip\ZipFile;

class HugoExporter
{
    public function export($posts, $index)
    {
        $zip = new ZipFile();

        foreach ($posts as $i => $post) {
            $post = $post->setPublicationDate();
            $post->category = config("export.category");
            $content = view("export.hugo", ["post" => $post]);

            echo "\n" . '    -> ' . 'Exporting #' . $i . ' ' . Color::yellow() . $post->slug . '.md' . Color::RESET;

            $zip->addFromString($post->slug . ".md", $content);
        }

        file_put_contents(base_path("exports/" . "hugo-" . $index . ".zip"), $zip->outputAsString());
        $zip->close();
    }
}
