<?php

use App\Helper;
use App\Image;
use App\Keyword;
use App\Spiders\BingSpider;

it("can be saved", function () {
    $keyword = new Keyword();
    $keyword->name = "Coloring Page";

    expect($keyword->save())->toBeTrue();
});

it("can suggest new keywords", function () {
    $keyword = new Keyword();
    $keyword->name = "Coloring Page";

    expect($keyword->suggest())
        ->toBeArray()
        ->not->toBeEmpty();
});

it("can import keywords.txt", function () {
    Keyword::txt();

    expect(Keyword::count())->toBeGreaterThan(0);
});

it("can import trending keywords", function () {
    Keyword::trends("US");

    expect(Keyword::count())->toBeGreaterThan(0);
});

it("can import keyword from cli", function () {
    config(["shuriken.keywords" => "worksheet"]);
    Keyword::manual();

    expect(Keyword::count())->toBeGreaterThan(0);
});

test("ambil sentence dari title dan desc gambar", function () {
    $spider = new BingSpider();

    $keyword = Keyword::firstOrCreate(["name" => "worksheet"]);

    $response = Helper::getResponse(BingSpider::buildUrl($keyword));

    $spider->saveImages($response, $keyword);

    expect($keyword->sentences(null, true)->count())->toBeGreaterThan(
        Image::count()
    );
});
