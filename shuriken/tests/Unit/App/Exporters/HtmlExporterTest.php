<?php

use App\Exporters\HtmlExporter;

function injectInArticleAd(string $html, string $ad): string
{
    $exporter = new HtmlExporter();
    $method   = new ReflectionMethod(HtmlExporter::class, 'injectInArticleAd');
    $method->setAccessible(true);
    return $method->invoke($exporter, $html, $ad);
}

it('menyisipkan iklan setelah </p> ke-2', function () {
    $html = '<p>Paragraf 1.</p><p>Paragraf 2.</p><p>Paragraf 3.</p>';
    $ad   = '<div>AD</div>';

    $result = injectInArticleAd($html, $ad);

    expect($result)->toBe('<p>Paragraf 1.</p><p>Paragraf 2.</p><div>AD</div><p>Paragraf 3.</p>');
});

it('tidak menyisipkan apapun jika hanya ada satu paragraf', function () {
    $html = '<p>Hanya satu paragraf.</p>';
    $ad   = '<div>AD</div>';

    $result = injectInArticleAd($html, $ad);

    expect($result)->toBe($html);
});

it('tidak menyisipkan apapun jika tidak ada paragraf', function () {
    $html = '<h1>Judul saja</h1>';
    $ad   = '<div>AD</div>';

    $result = injectInArticleAd($html, $ad);

    expect($result)->toBe($html);
});

it('case-insensitive untuk tag </P>', function () {
    $html = '<p>Paragraf 1.</P><p>Paragraf 2.</P><p>Paragraf 3.</p>';
    $ad   = '<div>AD</div>';

    $result = injectInArticleAd($html, $ad);

    // </P> uppercase dinormalisasi ke </p>, iklan tetap disisipkan setelah ke-2
    expect($result)->toContain('</p><div>AD</div><p>Paragraf 3.');
});

it('iklan hanya disisipkan sekali meski banyak paragraf', function () {
    $html = '<p>1</p><p>2</p><p>3</p><p>4</p><p>5</p>';
    $ad   = '<div>AD</div>';

    $result = injectInArticleAd($html, $ad);

    expect(substr_count($result, '<div>AD</div>'))->toBe(1);
});

it('menyisipkan iklan yang mengandung HTML kompleks', function () {
    $html = '<p>Paragraf 1.</p><p>Paragraf 2.</p><p>Paragraf 3.</p>';
    $ad   = '<div id="banner"><script src="//ads.example.com/native.js"></script></div>';

    $result = injectInArticleAd($html, $ad);

    expect($result)->toContain($ad);
    expect(strpos($result, $ad))->toBeGreaterThan(strpos($result, '<p>Paragraf 2.</p>'));
});
