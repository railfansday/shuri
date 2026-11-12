<?php

use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    file_put_contents(base_path('storage/sites.json'), '{}');
});

afterEach(function () {
    file_put_contents(base_path('storage/sites.json'), '{}');
});

function sites(): array
{
    return json_decode(file_get_contents(base_path('storage/sites.json')), true) ?? [];
}

// ---------------------------------------------------------------------------
// site list
// ---------------------------------------------------------------------------

it('site list berjalan tanpa error', function () {
    $exitCode = Artisan::call('site', ['action' => 'list']);
    expect($exitCode)->toBe(0);
});

// ---------------------------------------------------------------------------
// site add — validasi platform
// ---------------------------------------------------------------------------

it('site add platform tidak dikenal tidak menyimpan apapun', function () {
    Artisan::call('site', ['action' => 'add', 'name' => 'mysite', 'platform' => 'platform-palsu']);
    expect(sites())->toBeEmpty();
});

it('site add tanpa platform tidak menyimpan apapun', function () {
    Artisan::call('site', ['action' => 'add', 'name' => 'mysite']);
    expect(sites())->toBeEmpty();
});

// ---------------------------------------------------------------------------
// site add — netlify
// ---------------------------------------------------------------------------

it('site add netlify menyimpan site_id dan api_token', function () {
    Artisan::call('site', [
        'action'   => 'add',
        'name'     => 'myblog',
        'platform' => 'netlify',
        'arg1'     => 'site-abc123',
        'arg2'     => 'tok-xyz',
    ]);

    $s = sites();
    expect($s)->toHaveKey('myblog');
    expect($s['myblog']['platform'])->toBe('netlify');
    expect($s['myblog']['site_id'])->toBe('site-abc123');
    expect($s['myblog']['api_token'])->toBe('tok-xyz');
});

it('site add netlify tanpa arg1/arg2 tidak menyimpan', function () {
    Artisan::call('site', ['action' => 'add', 'name' => 'myblog', 'platform' => 'netlify']);
    expect(sites())->toBeEmpty();
});

// ---------------------------------------------------------------------------
// site add — cloudflare-pages
// ---------------------------------------------------------------------------

it('site add cloudflare-pages menyimpan account_id, project_name, api_token', function () {
    Artisan::call('site', [
        'action'   => 'add',
        'name'     => 'mycf',
        'platform' => 'cloudflare-pages',
        'arg1'     => 'acc-123',
        'arg2'     => 'my-project',
        'arg3'     => 'tok-cf',
    ]);

    $s = sites();
    expect($s['mycf']['platform'])->toBe('cloudflare-pages');
    expect($s['mycf']['account_id'])->toBe('acc-123');
    expect($s['mycf']['project_name'])->toBe('my-project');
    expect($s['mycf']['api_token'])->toBe('tok-cf');
});

it('site add cloudflare-pages tanpa arg3 tidak menyimpan', function () {
    Artisan::call('site', [
        'action'   => 'add',
        'name'     => 'mycf',
        'platform' => 'cloudflare-pages',
        'arg1'     => 'acc-123',
        'arg2'     => 'my-project',
    ]);
    expect(sites())->toBeEmpty();
});

// ---------------------------------------------------------------------------
// site add — vercel
// ---------------------------------------------------------------------------

it('site add vercel menyimpan project_name dan api_token', function () {
    Artisan::call('site', [
        'action'   => 'add',
        'name'     => 'myvercel',
        'platform' => 'vercel',
        'arg1'     => 'my-project',
        'arg2'     => 'tok-vercel',
    ]);

    $s = sites();
    expect($s['myvercel']['platform'])->toBe('vercel');
    expect($s['myvercel']['project_name'])->toBe('my-project');
    expect($s['myvercel']['api_token'])->toBe('tok-vercel');
    expect($s['myvercel'])->not->toHaveKey('team_id');
});

it('site add vercel menyimpan team_id jika diberikan', function () {
    Artisan::call('site', [
        'action'   => 'add',
        'name'     => 'myvercel',
        'platform' => 'vercel',
        'arg1'     => 'my-project',
        'arg2'     => 'tok-vercel',
        'arg3'     => 'team-abc',
    ]);

    expect(sites()['myvercel']['team_id'])->toBe('team-abc');
});

// ---------------------------------------------------------------------------
// site add — surge
// ---------------------------------------------------------------------------

it('site add surge menyimpan domain dan token', function () {
    Artisan::call('site', [
        'action'   => 'add',
        'name'     => 'mysurge',
        'platform' => 'surge',
        'arg1'     => 'mysite.surge.sh',
        'arg2'     => 'tok-surge',
    ]);

    $s = sites();
    expect($s['mysurge']['platform'])->toBe('surge');
    expect($s['mysurge']['domain'])->toBe('mysite.surge.sh');
    expect($s['mysurge']['token'])->toBe('tok-surge');
});

it('site add surge tanpa arg1/arg2 tidak menyimpan', function () {
    Artisan::call('site', ['action' => 'add', 'name' => 'mysurge', 'platform' => 'surge']);
    expect(sites())->toBeEmpty();
});

// ---------------------------------------------------------------------------
// site add — github-pages
// ---------------------------------------------------------------------------

it('site add github-pages menyimpan owner, repo, dan token', function () {
    Artisan::call('site', [
        'action'   => 'add',
        'name'     => 'mygithub',
        'platform' => 'github-pages',
        'arg1'     => 'budisetiawan',
        'arg2'     => 'myblog',
        'arg3'     => 'ghp_xxxxxxxxxxxx',
    ]);

    $s = sites();
    expect($s['mygithub']['platform'])->toBe('github-pages');
    expect($s['mygithub']['owner'])->toBe('budisetiawan');
    expect($s['mygithub']['repo'])->toBe('myblog');
    expect($s['mygithub']['token'])->toBe('ghp_xxxxxxxxxxxx');
});

it('site add github-pages tanpa token tidak menyimpan', function () {
    Artisan::call('site', [
        'action'   => 'add',
        'name'     => 'mygithub',
        'platform' => 'github-pages',
        'arg1'     => 'budisetiawan',
        'arg2'     => 'myblog',
    ]);
    expect(sites())->toBeEmpty();
});

// ---------------------------------------------------------------------------
// site add — multiple sites
// ---------------------------------------------------------------------------

it('site add bisa menyimpan beberapa site sekaligus', function () {
    Artisan::call('site', ['action' => 'add', 'name' => 'site-a', 'platform' => 'netlify',      'arg1' => 'id-a', 'arg2' => 'tok-a']);
    Artisan::call('site', ['action' => 'add', 'name' => 'site-b', 'platform' => 'surge',        'arg1' => 'b.surge.sh', 'arg2' => 'tok-b']);
    Artisan::call('site', ['action' => 'add', 'name' => 'site-c', 'platform' => 'github-pages', 'arg1' => 'owner', 'arg2' => 'repo', 'arg3' => 'ghp_c']);

    $s = sites();
    expect($s)->toHaveCount(3);
    expect($s)->toHaveKey('site-a');
    expect($s)->toHaveKey('site-b');
    expect($s)->toHaveKey('site-c');
});

it('site add menimpa site yang sudah ada dengan nama sama', function () {
    Artisan::call('site', ['action' => 'add', 'name' => 'mysite', 'platform' => 'netlify', 'arg1' => 'id-lama', 'arg2' => 'tok-lama']);
    Artisan::call('site', ['action' => 'add', 'name' => 'mysite', 'platform' => 'surge',   'arg1' => 'mysite.surge.sh', 'arg2' => 'tok-baru']);

    $s = sites();
    expect($s)->toHaveCount(1);
    expect($s['mysite']['platform'])->toBe('surge');
});

// ---------------------------------------------------------------------------
// site remove
// ---------------------------------------------------------------------------

it('site remove menghapus site yang ada', function () {
    Artisan::call('site', ['action' => 'add', 'name' => 'hapus-ini', 'platform' => 'netlify', 'arg1' => 'id', 'arg2' => 'tok']);
    Artisan::call('site', ['action' => 'remove', 'name' => 'hapus-ini']);

    expect(sites())->not->toHaveKey('hapus-ini');
});

it('site remove tidak menghapus site lain', function () {
    Artisan::call('site', ['action' => 'add', 'name' => 'keep',   'platform' => 'netlify', 'arg1' => 'id-1', 'arg2' => 'tok-1']);
    Artisan::call('site', ['action' => 'add', 'name' => 'remove', 'platform' => 'surge',   'arg1' => 'r.surge.sh', 'arg2' => 'tok-2']);
    Artisan::call('site', ['action' => 'remove', 'name' => 'remove']);

    $s = sites();
    expect($s)->toHaveKey('keep');
    expect($s)->not->toHaveKey('remove');
});

it('site remove site yang tidak ada tidak crash', function () {
    $exitCode = Artisan::call('site', ['action' => 'remove', 'name' => 'tidak-ada']);
    expect($exitCode)->toBe(0);
});

// ---------------------------------------------------------------------------
// action tidak valid
// ---------------------------------------------------------------------------

it('site action tidak valid tidak crash', function () {
    $exitCode = Artisan::call('site', ['action' => 'aksi-salah']);
    expect($exitCode)->toBe(0);
});
