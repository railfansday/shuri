<?php
use App\Helper;
use App\Image;
use App\Keyword;
use App\Spiders\BingSpider;

it(
    "can scrape images from bing",
    closure: function () {
        $spider = new BingSpider();

        $keyword = Keyword::firstOrCreate(["name" => "worksheet"]);

        $response = Helper::getResponse(BingSpider::buildUrl($keyword));

        expect($spider->scrapeImages($response))
            ->toBeArray()
            ->not->toBeEmpty();
    }
);

it("can scrape related keywords from bing", function () {
    $spider = new BingSpider();

    $keyword = Keyword::firstOrCreate(["name" => "worksheet"]);

    $response = Helper::getResponse(BingSpider::buildUrl($keyword));

    file_put_contents("bing.html", $response->getBody());

    expect($spider->scrapeKeywords($response))
        ->toBeArray()
        ->not->toBeEmpty();
});

it("can save images to database", function () {
    $spider = new BingSpider();

    $keyword = Keyword::firstOrCreate(["name" => "worksheet"]);

    $response = Helper::getResponse(BingSpider::buildUrl($keyword));

    $spider->saveImages($response, $keyword);

    expect(Image::count())->toBeGreaterThan(0);
});

it("postProcessSingleImage normalisasi filetype jpeg ke jpg", function () {
    $spider = new BingSpider();
    $raw = [
        "url" => "https://example.com/photo.jpeg",
        "link" => "https://example.com",
        "size" => "800 × 600",
    ];

    $result = $spider->postProcessSingleImage($raw);
    expect($result["filetype"])->toBe("jpg");
});

it("postProcessSingleImage normalisasi filetype animatedgif ke gif", function () {
    $spider = new BingSpider();
    $raw = [
        "url" => "https://example.com/anim.animatedgif",
        "link" => "https://example.com",
        "size" => "100 × 100",
    ];

    $result = $spider->postProcessSingleImage($raw);
    expect($result["filetype"])->toBe("gif");
});

it("postProcessSingleImage parsing dimensi width dan height dengan benar", function () {
    $spider = new BingSpider();
    $raw = [
        "url" => "https://example.com/photo.png",
        "link" => "https://example.com",
        "size" => "1280 × 720",
    ];

    $result = $spider->postProcessSingleImage($raw);
    expect($result["width"])->toBe("1280")
        ->and($result["height"])->toBe("720");
});

it("postProcessSingleImage parsing domain dari link", function () {
    $spider = new BingSpider();
    $raw = [
        "url" => "https://cdn.example.com/photo.jpg",
        "link" => "https://www.pinterest.com/pin/123",
        "size" => "400 × 300",
    ];

    $result = $spider->postProcessSingleImage($raw);
    expect($result["domain"])->toBe("www.pinterest.com");
});

it("can build url by preset", function () {
    $spider = new BingSpider();

    $keyword = Keyword::firstOrCreate(["name" => "worksheet"]);

    config([
        "shuriken.preset" =>
            "https://www.bing.com/images/search?sp=-1&pq=workshee&sc=8-8&cvid=C0CB4142644F47A9AC8DB5A3FB0D452C&q=site:pinterest.com&qft=+filterui:photo-clipart+filterui:imagesize-medium&form=IRFLTR&first=1&tsc=ImageHoverTitle",
        "shuriken.language" => "en",
        "shuriken.country"  => "us",
    ]);

    $url = $spider->buildUrl($keyword);
    expect($url)->toBe(
        "https://www.bing.com/images/async?q=worksheet+site:pinterest.com&qft=+filterui:photo-clipart+filterui:imagesize-medium&mkt=en-US"
    );
});

it("buildUrl includes mkt param from language and country config", function () {
    $keyword = Keyword::firstOrCreate(["name" => "worksheet"]);

    config([
        "shuriken.preset"   => "https://www.bing.com/images/search?q=",
        "shuriken.language" => "id",
        "shuriken.country"  => "id",
    ]);

    $url = BingSpider::buildUrl($keyword);
    expect($url)->toContain("mkt=id-ID");
});
