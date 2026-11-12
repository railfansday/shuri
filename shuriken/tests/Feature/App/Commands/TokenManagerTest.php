<?php

use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    file_put_contents(base_path('storage/tokens.json'), '{}');
});

afterEach(function () {
    file_put_contents(base_path('storage/tokens.json'), '{}');
});

it('token list berjalan tanpa error', function () {
    $exitCode = Artisan::call('token', ['action' => 'list']);
    expect($exitCode)->toBe(0);
});

it('token add menyimpan token ke tokens.json', function () {
    Artisan::call('token', ['action' => 'add', 'provider' => 'openai', 'token' => 'sk-test-abc']);

    $tokens = json_decode(file_get_contents(base_path('storage/tokens.json')), true);
    expect($tokens['openai'])->toContain('sk-test-abc');
});

it('token add beberapa token untuk satu provider', function () {
    Artisan::call('token', ['action' => 'add', 'provider' => 'gemini', 'token' => 'AIza-1']);
    Artisan::call('token', ['action' => 'add', 'provider' => 'gemini', 'token' => 'AIza-2']);
    Artisan::call('token', ['action' => 'add', 'provider' => 'gemini', 'token' => 'AIza-3']);

    $tokens = json_decode(file_get_contents(base_path('storage/tokens.json')), true);
    expect(count($tokens['gemini']))->toBe(3);
});

it('token add tidak menyimpan token duplikat', function () {
    Artisan::call('token', ['action' => 'add', 'provider' => 'openai', 'token' => 'sk-sama']);
    Artisan::call('token', ['action' => 'add', 'provider' => 'openai', 'token' => 'sk-sama']);

    $tokens = json_decode(file_get_contents(base_path('storage/tokens.json')), true);
    expect(count($tokens['openai']))->toBe(1);
});

it('token add provider tidak valid tidak menyimpan apapun', function () {
    Artisan::call('token', ['action' => 'add', 'provider' => 'provider-palsu', 'token' => 'sk-test']);

    $tokens = json_decode(file_get_contents(base_path('storage/tokens.json')), true);
    expect($tokens)->toBeEmpty();
});

it('token remove menghapus token dari tokens.json', function () {
    Artisan::call('token', ['action' => 'add', 'provider' => 'openai', 'token' => 'sk-hapus-ini']);
    Artisan::call('token', ['action' => 'remove', 'provider' => 'openai', 'token' => 'sk-hapus-ini']);

    $tokens = json_decode(file_get_contents(base_path('storage/tokens.json')), true);
    expect($tokens)->not->toHaveKey('openai');
});

it('token remove hanya menghapus token yang ditarget', function () {
    Artisan::call('token', ['action' => 'add', 'provider' => 'openai', 'token' => 'sk-pertama']);
    Artisan::call('token', ['action' => 'add', 'provider' => 'openai', 'token' => 'sk-kedua']);
    Artisan::call('token', ['action' => 'remove', 'provider' => 'openai', 'token' => 'sk-pertama']);

    $tokens = json_decode(file_get_contents(base_path('storage/tokens.json')), true);
    expect($tokens['openai'])->toContain('sk-kedua');
    expect($tokens['openai'])->not->toContain('sk-pertama');
});

it('token remove token yang tidak ada tidak mengubah file', function () {
    Artisan::call('token', ['action' => 'add', 'provider' => 'openai', 'token' => 'sk-ada']);
    Artisan::call('token', ['action' => 'remove', 'provider' => 'openai', 'token' => 'sk-tidak-ada']);

    $tokens = json_decode(file_get_contents(base_path('storage/tokens.json')), true);
    expect($tokens['openai'])->toContain('sk-ada');
});

it('token dengan action tidak valid tidak crash', function () {
    $exitCode = Artisan::call('token', ['action' => 'aksi-salah']);
    expect($exitCode)->toBe(0);
});
