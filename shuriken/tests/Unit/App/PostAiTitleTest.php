<?php

// Tests for the <title> extraction logic in Post::regenerate()
// The logic: preg_match('/<title>(.*?)<\/title>/is', $ai_content, $matches)
//   → $post->ai_title  = trim(strip_tags($matches[1]))
//   → $post->ai_content = trim(preg_replace('/<title>.*?<\/title>/is', '', $ai_content))

function extractAiTitle(string $aiContent): array
{
    $aiTitle = null;

    if (preg_match('/<title>(.*?)<\/title>/is', $aiContent, $matches)) {
        $aiTitle   = trim(strip_tags($matches[1]));
        $aiContent = trim(preg_replace('/<title>.*?<\/title>/is', '', $aiContent));
    }

    return ['ai_title' => $aiTitle, 'ai_content' => $aiContent];
}

it('mengekstrak <title> dari ai_content dan menyimpan ke ai_title', function () {
    $result = extractAiTitle("<title>Best Interior Tips</title>\n<p>Some content here.</p>");

    expect($result['ai_title'])->toBe('Best Interior Tips');
    expect($result['ai_content'])->toBe('<p>Some content here.</p>');
});

it('ai_title null jika ai_content tidak mengandung <title>', function () {
    $result = extractAiTitle('<p>Some content without title tag.</p>');

    expect($result['ai_title'])->toBeNull();
    expect($result['ai_content'])->toBe('<p>Some content without title tag.</p>');
});

it('strip HTML tags dari dalam <title>', function () {
    $result = extractAiTitle('<title><b>Bold Title</b></title><p>Content.</p>');

    expect($result['ai_title'])->toBe('Bold Title');
});

it('case-insensitive untuk tag <TITLE>', function () {
    $result = extractAiTitle('<TITLE>Uppercase Title</TITLE><p>Content.</p>');

    expect($result['ai_title'])->toBe('Uppercase Title');
});

it('membuang whitespace dari ai_title dan ai_content', function () {
    $result = extractAiTitle("  <title>  Spaced Title  </title>  \n\n  <p>Body</p>  ");

    expect($result['ai_title'])->toBe('Spaced Title');
    expect($result['ai_content'])->toBe('<p>Body</p>');
});

it('<title> di tengah konten tetap diekstrak dengan benar', function () {
    $result = extractAiTitle("<p>Intro</p>\n<title>Mid Title</title>\n<p>More content.</p>");

    expect($result['ai_title'])->toBe('Mid Title');
    expect($result['ai_content'])->not->toContain('<title>');
    expect($result['ai_content'])->toContain('<p>Intro</p>');
    expect($result['ai_content'])->toContain('<p>More content.</p>');
});
