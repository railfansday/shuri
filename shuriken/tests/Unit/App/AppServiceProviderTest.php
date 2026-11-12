<?php

use App\Providers\AppServiceProvider;

beforeEach(function () {
    file_put_contents(base_path('storage/tokens.json'), '{}');
});

afterEach(function () {
    file_put_contents(base_path('storage/tokens.json'), '{}');
});

it('AppServiceProvider memuat token dari tokens.json ke config saat boot', function () {
    file_put_contents(base_path('storage/tokens.json'), json_encode([
        'openai' => ['sk-dari-json'],
    ]));

    (new AppServiceProvider(app()))->boot();

    expect(config('ai.openai_tokens'))->toContain('sk-dari-json');
});

it('AppServiceProvider memuat beberapa provider sekaligus dari tokens.json', function () {
    file_put_contents(base_path('storage/tokens.json'), json_encode([
        'openai' => ['sk-test'],
        'gemini' => ['AIza-test'],
        'claude' => ['sk-ant-test'],
    ]));

    (new AppServiceProvider(app()))->boot();

    expect(config('ai.openai_tokens'))->toContain('sk-test');
    expect(config('ai.gemini_tokens'))->toContain('AIza-test');
    expect(config('ai.claude_tokens'))->toContain('sk-ant-test');
});

it('AppServiceProvider menempatkan token dari tokens.json sebelum token dari config', function () {
    config(['ai.openai_tokens' => ['sk-dari-config']]);

    file_put_contents(base_path('storage/tokens.json'), json_encode([
        'openai' => ['sk-dari-json'],
    ]));

    (new AppServiceProvider(app()))->boot();

    $tokens = config('ai.openai_tokens');
    expect($tokens[0])->toBe('sk-dari-json');
    expect($tokens)->toContain('sk-dari-config');
});

it('AppServiceProvider tidak mengubah config ketika tokens.json kosong', function () {
    config(['ai.openai_tokens' => ['sk-existing']]);
    file_put_contents(base_path('storage/tokens.json'), '{}');

    (new AppServiceProvider(app()))->boot();

    expect(config('ai.openai_tokens'))->toContain('sk-existing');
});

it('AppServiceProvider tidak mengubah config ketika tokens.json tidak ada', function () {
    config(['ai.openai_tokens' => ['sk-existing']]);
    $path = base_path('storage/tokens.json');
    rename($path, $path . '.bak');

    (new AppServiceProvider(app()))->boot();

    expect(config('ai.openai_tokens'))->toContain('sk-existing');
    rename($path . '.bak', $path);
});
