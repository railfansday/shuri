<?php

use App\Keyword;
use App\Post;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

test("clear semua data hasil scraping dan ekspor", function () {
    Artisan::call("image:scrape", [
        "--keywords" => "worksheet",
        "--max" => 1,
    ]);

    Artisan::call("image:export", [
        "--platform" => "html",
    ]);

    expect(Post::count())->toBeGreaterThan(0);
    expect(Keyword::count())->toBeGreaterThan(0);
    expect(Storage::exists("exports/html-1.zip"))->toBeTrue();

    Artisan::call("clear:data");

    expect(Post::count())->toBe(0);
    expect(Keyword::count())->toBe(0);
    expect(Storage::exists("exports/html-1.zip"))->toBeFalse();
});
