<?php

use App\Exporters\HtmlExporter;
use App\Keyword;
use App\Post;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use PhpZip\ZipFile;

/**
 * @return void
 */
function testPlatform(
    string $platform,
    string $format = "xml",
    int $post_count = 1
): void {
    Artisan::call("image:scrape", [
        "--keywords" => "worksheets",
        "--max" => $post_count,
    ]);

    Artisan::call("image:export", [
        "--platform" => $platform,
    ]);

    $path = base_path("exports/$platform-1.$format");
    expect(file_exists($path))->toBeTrue();

    $content = file_get_contents($path);
    expect($content)->toContain("worksheets");
}

test("ekspor ke WordPress XML", function () {
    testPlatform("wordpress");
});

test("ekspor ke Blogger XML", function () {
    testPlatform("blogger");
});

test("ekspor ke Hugo (markdown)", function () {
    testPlatform("hugo", "zip");
});

test("ekspor ke CSV", function () {
    testPlatform("csv", "csv");
});

test("ekspor ke html", function () {
    testPlatform("html", "zip", 3);
});
test("ekspor ke html bisa ganti theme", function () {
    Artisan::call("image:scrape", [
        "--keywords" => "worksheets",
        "--max" => 1,
    ]);

    foreach (HtmlExporter::THEMES as $theme) {
        Artisan::call("image:export", [
            "--platform" => "html",
            "--theme" => $theme,
        ]);

        $path = base_path("exports/html-1.zip");
        expect(file_exists($path))->toBeTrue();

        $zipFile = new ZipFile();
        $zipFile->openFile($path);

        expect($zipFile->hasEntry("$theme.txt"))->toBeTrue();
    }
});
test("ekspor ke html bisa generate sitemap", function () {
    Artisan::call("image:scrape", [
        "--keywords" => "나 혼자만 레벨업",
        "--max" => 3,
    ]);

    foreach (HtmlExporter::THEMES as $theme) {
        Artisan::call("image:export", [
            "--platform" => "html",
            "--theme" => $theme,
        ]);

        $path = base_path("exports/html-1.zip");
        expect(file_exists($path))->toBeTrue();

        $zipFile = new ZipFile();
        $zipFile->openFile($path);

        expect($zipFile->hasEntry("sitemap.xml"))->toBeTrue();
    }
});

test("ekspor ke native", function () {
    Artisan::call("image:scrape", [
        "--keywords" => "worksheets",
        "--max" => 3,
    ]);

    Artisan::call("image:export", [
        "--platform" => "native",
        "--clear_post" => true,
    ]);

    $databases = glob(base_path("exports/*.sqlite"));

    expect($databases)->not->toBeEmpty();
});

test("template post, title, category dan tag support spintax", function () {
    Artisan::call("image:scrape", [
        "--keywords" => "worksheets",
        "--max" => 1,
    ]);

    $keyword = Keyword::first();

    $post = Post::regenerate($keyword);

    if (str($post->content)->contains("looking for")) {
        expect($post->content)->not->toContain("searching about");
    } else {
        expect($post->content)->not->toContain("looking for");
    }
});

test("menentukan kategori ketika ekspor", function () {
    Artisan::call("image:scrape", [
        "--keywords" => "worksheets",
        "--max" => 1,
    ]);

    Artisan::call("image:export", [
        "--platform" => "wordpress",
        "--category" => "Worksheet Ideas",
    ]);

    expect(file_get_contents(base_path("exports/wordpress-1.xml")))->toContain(
        "Worksheet Ideas"
    );
});

test("ngelimit jumlah konten yang akan diekspor", function () {
    Artisan::call("image:scrape", [
        "--keywords" => "worksheets,coloring page",
        "--max" => 2,
    ]);

    Artisan::call("image:export", [
        "--platform" => "wordpress",
        "--chunk" => 1,
    ]);

    expect(file_get_contents(base_path("exports/wordpress-1.xml")))
        ->toContain("worksheets")
        ->not->toContain("coloring page");
});

test("clear data ketika ekspor", function () {
    Artisan::call("image:scrape", [
        "--keywords" => "worksheets,coloring page",
        "--max" => 2,
    ]);

    Artisan::call("image:export", [
        "--platform" => "wordpress",
        "--clear_post" => true,
    ]);

    expect(count(Storage::files("exports")))->toBe(1);
});
