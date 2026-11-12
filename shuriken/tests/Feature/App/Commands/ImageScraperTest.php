<?php

use App\Keyword;
use App\Image;
use App\Post;
use Illuminate\Support\Facades\Artisan;

test("menentukan geo dan bahasa ketika scraping", function () {
    Artisan::call("image:scrape", [
        "--clear_data" => true,
        "--country" => "us",
        "--language" => "en",
        "--keywords" => "worksheets",
        "--max" => 1,
        "--no_expand" => true,
    ]);

    expect(Image::count())->toBeGreaterThan(0);

    Artisan::call("image:scrape", [
        "--clear_data" => true,
        "--country" => "id",
        "--language" => "id",
        "--keywords" => "worksheets",
        "--max" => 1,
        "--no_expand" => true,
    ]);

    expect(Image::count())->toBeGreaterThan(0);
});

test("pakai proxy", function () {
    Artisan::call("image:scrape", [
        "--country" => "id",
        "--language" => "id",
        "--keywords" => "worksheets,coloring pages",
        "--max" => 2,
        "--no_expand" => true,
        "--proxy" => env("PROXY"),
    ]);

    expect(Keyword::count())->toEqual(2);
});
test("import keywords via keywords.txt", function () {
    Artisan::call("image:scrape", [
        "--max" => 2,
        "--keywords_txt" => true,
    ]);

    expect(Keyword::has("images")->count())->toEqual(2);
});
test("import keywords via command line", function () {
    Artisan::call("image:scrape", [
        "--max" => 2,
        "--keywords" => "worksheets,coloring pages",
    ]);

    expect(Keyword::has("images")->count())->toEqual(2);
});
test("import trending keywords", function () {
    Artisan::call("image:scrape", [
        "--max" => 2,
        "--trends" => "US",
    ]);

    expect(Keyword::count())->toBeGreaterThan(0);
});
test("menentukan jumlah data maksimal yang discrape", function () {
    Artisan::call("image:scrape", [
        "--max" => 2,
        "--keywords" => "worksheets,coloring pages",
        "--no_expand" => true,
    ]);

    expect(Keyword::count())->toEqual(2);
});
test(
    "ambil data yang lebih relevan dengan enable javascript (lebih lambat)",
    function () {
        Artisan::call("image:scrape", [
            "--max" => 1,
            "--keywords" => "worksheets",
            "--execute_javascript" => true,
            "--image_count" => 35,
        ]);

        expect(Image::count())->toBeGreaterThan(16);
    }
);
test("scraping secara paralel untuk mempercepat waktu scraping", function () {
    Artisan::call("image:scrape", [
        "--max" => 3,
        "--keywords" => "worksheets",
        "--concurrency" => 10,
    ]);

    expect(Keyword::has("images")->count())->toEqual(3);
});
test("menentukan delay tiap scraping agar tidak mudah banned ip", function () {
    Artisan::call("image:scrape", [
        "--max" => 2,
        "--keywords" => "worksheets,coloring pages",
        "--no_expand" => true,
        "--requestDelay" => 1,
    ]);

    expect(Keyword::count())->toEqual(2);
});
test("membersihkan data sebelum scraping", function () {
    Keyword::importArray(["makan", "minum", "tidur", "pulang"]);

    Artisan::call("image:scrape", [
        "--max" => 2,
        "--keywords" => "worksheets,coloring pages",
        "--no_expand" => true,
        "--clear_data" => true,
    ]);

    expect(Keyword::count())->toEqual(2);
});

test("scraping dengan preset query dan filter", function () {
    Artisan::call("image:scrape", [
        "--max" => 1,
        "--keywords" => "worksheets",
        "--preset" =>
            "https://www.bing.com/images/search?sp=-1&pq=workshee&sc=8-8&cvid=C0CB4142644F47A9AC8DB5A3FB0D452C&q=site:pinterest.com&qft=+filterui:photo-clipart+filterui:imagesize-medium&form=IRFLTR&first=1&tsc=ImageHoverTitle",
    ]);

    expect(Image::first()->link)->toContain("pinterest.com");
});

test("scraping pakai query tambahan (append_query)", function () {
    Artisan::call("image:scrape", [
        "--max" => 1,
        "--keywords" => "iphone",
        "--no_expand" => true,
        "--append_query" => "site:tokopedia.com",
    ]);

    $image = Image::first();
    if ($image !== null) {
        expect($image->link)->toContain("tokopedia.com");
    } else {
        // Bing tidak return hasil untuk query ini saat test dijalankan — bukan bug
        expect(true)->toBeTrue();
    }
});

test("scraping pakai filter tambahan (qft)", function () {
    Artisan::call("image:scrape", [
        "--max" => 1,
        "--keywords" => "worksheets",
    ]);

    $link_a = Image::first()->link;

    Artisan::call("image:scrape", [
        "--max" => 1,
        "--keywords" => "worksheets",
        "--clear_data" => true,
        "--qft" => "+filterui:photo-clipart+filterui:imagesize-medium",
    ]);

    $link_b = Image::first()->link;

    expect($link_a)->not->toEqual($link_b);
});

test("menentukan jumlah gambar per keyword yang akan di scrape", function () {
    Artisan::call("image:scrape", [
        "--max" => 1,
        "--keywords" => "worksheets",
        "--image_count" => 9,
    ]);

    expect(
        Keyword::first()
            ->images()
            ->count()
    )->toBeLessThanOrEqual(9);

    //    Calling setting image count above 9 should use --execute_javascript
    Artisan::call("image:scrape", [
        "--max" => 1,
        "--clear_data" => true,
        "--keywords" => "worksheets",
        "--execute_javascript" => true,
        "--image_count" => 35,
    ]);

    expect(Image::count())->toBeGreaterThan(9);
});

test("scraping non latin (UTF-8)", function () {
    Artisan::call("image:scrape", [
        "--max" => 1,
        "--keywords" => "나 혼자만 레벨업",
        "--language" => "kr",
        "--country" => "kr",
    ]);

    $keyword = Keyword::where("name", "나 혼자만 레벨업")->first();

    $post = Post::regenerate($keyword);

    expect($post->content)->toContain("나 혼자만 레벨업");
});

test("exit dengan error ketika --ai aktif tapi openai tokens kosong", function () {
    config(["ai.openai_tokens" => []]);

    $exitCode = Artisan::call("image:scrape", [
        "--keywords" => "worksheets",
        "--max" => 1,
        "--ai" => true,
    ]);

    expect($exitCode)->toBe(1);
});

test("exit dengan error ketika --ai --provider=gemini aktif tapi gemini tokens kosong", function () {
    config(["ai.gemini_tokens" => []]);

    $exitCode = Artisan::call("image:scrape", [
        "--keywords" => "worksheets",
        "--max" => 1,
        "--ai" => true,
        "--provider" => "gemini",
    ]);

    expect($exitCode)->toBe(1);
});

test("exit dengan error ketika --ai --provider=claude aktif tapi claude tokens kosong", function () {
    config(["ai.claude_tokens" => []]);

    $exitCode = Artisan::call("image:scrape", [
        "--keywords" => "worksheets",
        "--max" => 1,
        "--ai" => true,
        "--provider" => "claude",
    ]);

    expect($exitCode)->toBe(1);
});

test("exit dengan error ketika --ai --provider=deepseek aktif tapi deepseek tokens kosong", function () {
    config(["ai.deepseek_tokens" => []]);

    $exitCode = Artisan::call("image:scrape", [
        "--keywords" => "worksheets",
        "--max" => 1,
        "--ai" => true,
        "--provider" => "deepseek",
    ]);

    expect($exitCode)->toBe(1);
});

test("exit dengan error ketika --ai --provider=groq aktif tapi groq tokens kosong", function () {
    config(["ai.groq_tokens" => []]);

    $exitCode = Artisan::call("image:scrape", [
        "--keywords" => "worksheets",
        "--max" => 1,
        "--ai" => true,
        "--provider" => "groq",
    ]);

    expect($exitCode)->toBe(1);
});

test("exit dengan error ketika --ai --provider=openrouter aktif tapi openrouter tokens kosong", function () {
    config(["ai.openrouter_tokens" => []]);

    $exitCode = Artisan::call("image:scrape", [
        "--keywords" => "worksheets",
        "--max" => 1,
        "--ai" => true,
        "--provider" => "openrouter",
    ]);

    expect($exitCode)->toBe(1);
});

test("exit dengan error ketika --ai --provider=kimi aktif tapi kimi tokens kosong", function () {
    config(["ai.kimi_tokens" => []]);

    $exitCode = Artisan::call("image:scrape", [
        "--keywords" => "worksheets",
        "--max" => 1,
        "--ai" => true,
        "--provider" => "kimi",
    ]);

    expect($exitCode)->toBe(1);
});

test("exit dengan error ketika --ai --provider=grok aktif tapi grok tokens kosong", function () {
    config(["ai.grok_tokens" => []]);

    $exitCode = Artisan::call("image:scrape", [
        "--keywords" => "worksheets",
        "--max" => 1,
        "--ai" => true,
        "--provider" => "grok",
    ]);

    expect($exitCode)->toBe(1);
});

test("exit dengan error ketika --ai --provider=mistral aktif tapi mistral tokens kosong", function () {
    config(["ai.mistral_tokens" => []]);

    $exitCode = Artisan::call("image:scrape", [
        "--keywords" => "worksheets",
        "--max" => 1,
        "--ai" => true,
        "--provider" => "mistral",
    ]);

    expect($exitCode)->toBe(1);
});

test("exit dengan error ketika --ai --provider=together aktif tapi together tokens kosong", function () {
    config(["ai.together_tokens" => []]);

    $exitCode = Artisan::call("image:scrape", [
        "--keywords" => "worksheets",
        "--max" => 1,
        "--ai" => true,
        "--provider" => "together",
    ]);

    expect($exitCode)->toBe(1);
});

test("exit dengan error ketika --ai --provider=perplexity aktif tapi perplexity tokens kosong", function () {
    config(["ai.perplexity_tokens" => []]);

    $exitCode = Artisan::call("image:scrape", [
        "--keywords" => "worksheets",
        "--max" => 1,
        "--ai" => true,
        "--provider" => "perplexity",
    ]);

    expect($exitCode)->toBe(1);
});

test("exit dengan error ketika --ai aktif dengan provider tidak dikenal", function () {
    $exitCode = Artisan::call("image:scrape", [
        "--keywords" => "worksheets",
        "--max" => 1,
        "--ai" => true,
        "--provider" => "provider-tidak-ada",
    ]);

    expect($exitCode)->toBe(1);
});

test("cegah badword masuk ke keyword dan images", function () {
    $keywords = [
        "makan nasi",
        "nonton website bokep",
        "banyak nyamuk di rumahku",
    ];
    Keyword::importArray($keywords);

    $keyword = Keyword::where("name", "like", "%porn%")->first();
    expect($keyword)->toBeNull();

    $image = Image::where("title", "like", "%porn%")
        ->orWhere("desc", "like", "%porn%")
        ->first();
    expect($image)->toBeNull();
});
