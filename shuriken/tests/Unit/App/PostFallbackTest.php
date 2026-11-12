<?php

use App\Post;

function getFallbackProviders(): array
{
    $reflection = new ReflectionClass(Post::class);
    $method = $reflection->getMethod('getFallbackProviders');
    $method->setAccessible(true);
    return $method->invoke(null);
}

beforeEach(function () {
    // Reset all API provider tokens to empty
    foreach (['openai', 'gemini', 'claude', 'groq', 'deepseek', 'openrouter', 'kimi', 'grok', 'mistral', 'together', 'perplexity'] as $p) {
        config(["ai.{$p}_tokens" => []]);
    }
    config(['shuriken.provider' => null]);
});

it('getFallbackProviders menempatkan primary provider di posisi pertama', function () {
    config(['shuriken.provider' => 'gemini']);
    config(['ai.gemini_tokens' => ['AIza-test']]);

    $providers = getFallbackProviders();

    expect($providers[0])->toBe('gemini');
});

it('getFallbackProviders memasukkan provider lain yang punya token setelah primary', function () {
    config(['shuriken.provider' => 'gemini']);
    config(['ai.gemini_tokens' => ['AIza-test']]);
    config(['ai.openai_tokens' => ['sk-test']]);
    config(['ai.groq_tokens' => ['gsk-test']]);

    $providers = getFallbackProviders();

    expect($providers[0])->toBe('gemini');
    expect($providers)->toContain('openai');
    expect($providers)->toContain('groq');
});

it('getFallbackProviders tidak memasukkan provider tanpa token', function () {
    config(['shuriken.provider' => 'openai']);
    config(['ai.openai_tokens' => ['sk-test']]);
    // gemini, claude, dll kosong

    $providers = getFallbackProviders();

    expect($providers)->not->toContain('gemini');
    expect($providers)->not->toContain('claude');
});

it('getFallbackProviders tidak menduplikat primary provider', function () {
    config(['shuriken.provider' => 'openai']);
    config(['ai.openai_tokens' => ['sk-test']]);

    $providers = getFallbackProviders();

    expect(array_count_values($providers)['openai'])->toBe(1);
});

it('getFallbackProviders selalu menyertakan local providers di akhir list', function () {
    config(['shuriken.provider' => 'openai']);

    $providers = getFallbackProviders();

    expect($providers)->toContain('ollama');
    expect($providers)->toContain('lmstudio');
    expect($providers)->toContain('claude-cli');
    expect($providers)->toContain('gemini-cli');
});

it('getFallbackProviders mengembalikan list meski tidak ada provider dengan token', function () {
    config(['shuriken.provider' => 'openai']);
    // semua token kosong

    $providers = getFallbackProviders();

    expect($providers)->not->toBeEmpty();
    // setidaknya openai (primary) dan local providers
    expect($providers)->toContain('openai');
    expect($providers)->toContain('ollama');
});
