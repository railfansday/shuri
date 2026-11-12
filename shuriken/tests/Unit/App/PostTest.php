<?php
use App\Keyword;
use App\Post;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

test("generate post", function () {
    Artisan::call("image:scrape", [
        "--max" => 1,
        "--keywords" => "worksheet",
    ]);

    $keyword = Keyword::first();
    $tag = Keyword::firstOrCreate(
        ["name" => "tag1"],
        ["keyword_id" => $keyword->id]
    );

    config(["shuriken.category" => "Worksheet Ideas"]);

    $post = Post::firstOrCreate(
        ["keyword_id" => $keyword->id],
        ["published_at" => Carbon::now()]
    );

    foreach (Post::TEMPLATES as $template) {
        $post = Post::generate("image." . $template, $post);
        if ($template === "category") {
            expect($post->{$template})->toContain("Worksheet Ideas");
        }

        expect($post->{$template})->not->toBeEmpty();

        file_put_contents("bing.html", $post->{$template});
    }
});
