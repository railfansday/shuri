<?php

/**
 * Kirim satu atau beberapa pesan JSON ke mcp:serve via stdin,
 * kembalikan array response (sudah di-decode).
 */
function mcp(string|array $messages): array
{
    if (is_string($messages)) {
        $messages = [$messages];
    }

    $input = implode("\n", $messages) . "\n";

    $proc = proc_open(
        ['php', base_path('shuriken'), 'mcp:serve'],
        [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
        $pipes
    );

    fwrite($pipes[0], $input);
    fclose($pipes[0]);

    $output = stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    proc_close($proc);

    return array_values(array_filter(
        array_map(
            fn($line) => json_decode(trim($line), true),
            explode("\n", trim($output))
        ),
        fn($item) => $item !== null
    ));
}

// ---------------------------------------------------------------------------
// initialize
// ---------------------------------------------------------------------------

it('initialize mengembalikan protocolVersion dan serverInfo', function () {
    $res = mcp('{"jsonrpc":"2.0","id":1,"method":"initialize","params":{"protocolVersion":"2024-11-05","capabilities":{},"clientInfo":{"name":"test","version":"1.0"}}}');

    expect($res)->toHaveCount(1);
    expect($res[0]['id'])->toBe(1);
    expect($res[0]['result']['protocolVersion'])->toBe('2024-11-05');
    expect($res[0]['result']['serverInfo']['name'])->toBe('shuriken');
    expect($res[0]['result']['capabilities'])->toHaveKey('tools');
});

// ---------------------------------------------------------------------------
// notifications/initialized — tidak boleh ada response
// ---------------------------------------------------------------------------

it('notifications/initialized tidak menghasilkan response', function () {
    $res = mcp([
        '{"jsonrpc":"2.0","id":1,"method":"initialize","params":{"protocolVersion":"2024-11-05","capabilities":{},"clientInfo":{"name":"test","version":"1.0"}}}',
        '{"jsonrpc":"2.0","method":"notifications/initialized","params":{}}',
    ]);

    // Hanya ada 1 response (untuk initialize), notifikasi tidak dibalas
    expect($res)->toHaveCount(1);
    expect($res[0]['id'])->toBe(1);
});

// ---------------------------------------------------------------------------
// tools/list
// ---------------------------------------------------------------------------

it('tools/list mengembalikan semua 8 tools', function () {
    $res = mcp('{"jsonrpc":"2.0","id":2,"method":"tools/list","params":{}}');

    expect($res[0]['result']['tools'])->toHaveCount(8);
});

it('tools/list setiap tool punya name, description, dan inputSchema', function () {
    $res = mcp('{"jsonrpc":"2.0","id":2,"method":"tools/list","params":{}}');

    foreach ($res[0]['result']['tools'] as $tool) {
        expect($tool)->toHaveKeys(['name', 'description', 'inputSchema']);
        expect($tool['inputSchema'])->toHaveKey('type');
    }
});

it('tools/list mengandung tool create_site dengan required keywords dan site', function () {
    $res = mcp('{"jsonrpc":"2.0","id":2,"method":"tools/list","params":{}}');

    $tools = collect($res[0]['result']['tools'])->keyBy('name');

    expect($tools)->toHaveKey('create_site');
    expect($tools['create_site']['inputSchema']['required'])->toContain('keywords');
    expect($tools['create_site']['inputSchema']['required'])->toContain('site');
});

// ---------------------------------------------------------------------------
// tools/call — list_sites & list_tokens (tidak butuh DB/network)
// ---------------------------------------------------------------------------

it('tools/call list_sites mengembalikan teks ketika belum ada sites', function () {
    $origPath = base_path('storage/sites.json');
    $backup   = file_exists($origPath) ? file_get_contents($origPath) : null;
    file_put_contents($origPath, '{}');

    $res = mcp('{"jsonrpc":"2.0","id":3,"method":"tools/call","params":{"name":"list_sites","arguments":{}}}');

    if ($backup !== null) {
        file_put_contents($origPath, $backup);
    }

    expect($res[0]['result']['content'][0]['type'])->toBe('text');
    expect($res[0]['result']['content'][0]['text'])->toContain('No sites');
});

it('tools/call list_sites menampilkan alias yang sudah dikonfigurasi', function () {
    $origPath = base_path('storage/sites.json');
    $backup   = file_exists($origPath) ? file_get_contents($origPath) : null;

    file_put_contents($origPath, json_encode([
        'myblog' => ['platform' => 'netlify', 'site_id' => 'abc', 'api_token' => 'tok'],
    ]));

    $res = mcp('{"jsonrpc":"2.0","id":3,"method":"tools/call","params":{"name":"list_sites","arguments":{}}}');

    file_put_contents($origPath, $backup ?? '{}');

    $text = $res[0]['result']['content'][0]['text'];
    expect($text)->toContain('myblog');
    expect($text)->toContain('netlify');
});

it('tools/call list_tokens mengembalikan teks ketika belum ada tokens', function () {
    $origPath = base_path('storage/tokens.json');
    $backup   = file_exists($origPath) ? file_get_contents($origPath) : null;
    file_put_contents($origPath, '{}');

    $res = mcp('{"jsonrpc":"2.0","id":4,"method":"tools/call","params":{"name":"list_tokens","arguments":{}}}');

    if ($backup !== null) {
        file_put_contents($origPath, $backup);
    }

    expect($res[0]['result']['content'][0]['type'])->toBe('text');
    expect($res[0]['result']['content'][0]['text'])->toContain('No tokens');
});

it('tools/call list_tokens menampilkan provider yang sudah dikonfigurasi', function () {
    $origPath = base_path('storage/tokens.json');
    $backup   = file_exists($origPath) ? file_get_contents($origPath) : null;

    file_put_contents($origPath, json_encode(['groq' => ['tok-1', 'tok-2']]));

    $res = mcp('{"jsonrpc":"2.0","id":4,"method":"tools/call","params":{"name":"list_tokens","arguments":{}}}');

    file_put_contents($origPath, $backup ?? '{}');

    $text = $res[0]['result']['content'][0]['text'];
    expect($text)->toContain('groq');
    expect($text)->toContain('2 token');
});

it('tools/call db_status mengembalikan jumlah posts dan images', function () {
    $res = mcp('{"jsonrpc":"2.0","id":5,"method":"tools/call","params":{"name":"db_status","arguments":{}}}');

    $text = $res[0]['result']['content'][0]['text'];
    expect($text)->toContain('posts');
    expect($text)->toContain('images');
});

// ---------------------------------------------------------------------------
// Error handling
// ---------------------------------------------------------------------------

it('method tidak dikenal mengembalikan error -32601', function () {
    $res = mcp('{"jsonrpc":"2.0","id":9,"method":"unknown/method","params":{}}');

    expect($res[0]['error']['code'])->toBe(-32601);
    expect($res[0]['id'])->toBe(9);
});

it('tool tidak dikenal mengembalikan error -32603', function () {
    $res = mcp('{"jsonrpc":"2.0","id":10,"method":"tools/call","params":{"name":"tool_palsu","arguments":{}}}');

    expect($res[0]['error']['code'])->toBe(-32603);
    expect($res[0]['id'])->toBe(10);
});

it('JSON tidak valid mengembalikan error -32700', function () {
    $res = mcp('ini bukan json {{{');

    expect($res[0]['error']['code'])->toBe(-32700);
});

it('multiple request dalam satu sesi diproses berurutan', function () {
    $res = mcp([
        '{"jsonrpc":"2.0","id":1,"method":"initialize","params":{"protocolVersion":"2024-11-05","capabilities":{},"clientInfo":{"name":"test","version":"1.0"}}}',
        '{"jsonrpc":"2.0","id":2,"method":"tools/list","params":{}}',
        '{"jsonrpc":"2.0","id":3,"method":"tools/call","params":{"name":"db_status","arguments":{}}}',
    ]);

    expect($res)->toHaveCount(3);
    expect($res[0]['id'])->toBe(1);
    expect($res[1]['id'])->toBe(2);
    expect($res[2]['id'])->toBe(3);
});
