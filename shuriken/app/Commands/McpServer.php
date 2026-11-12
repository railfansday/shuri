<?php

namespace App\Commands;

use Illuminate\Support\Facades\DB;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Console\Output\BufferedOutput;

class McpServer extends Command
{
    protected $signature = 'mcp:serve';

    protected $description = 'Start MCP (Model Context Protocol) server for AI CLI integration';

    public function handle(): void
    {
        $stdin = fopen('php://stdin', 'r');

        while (($line = fgets($stdin)) !== false) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            $message = json_decode($line, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $response = $this->errorResponse(null, -32700, 'Parse error');
                echo json_encode($response) . "\n";
                flush();
                continue;
            }

            $response = $this->dispatch($message);

            // Notifications don't get a response
            if ($response === null) {
                continue;
            }

            echo json_encode($response) . "\n";
            flush();
        }

        fclose($stdin);
    }

    private function dispatch(array $message): ?array
    {
        $method = $message['method'] ?? '';
        $id = $message['id'] ?? null;

        return match ($method) {
            'initialize'                => $this->handleInitialize($message),
            'tools/list'                => $this->handleToolsList($message),
            'tools/call'                => $this->handleToolsCall($message),
            'notifications/initialized' => null,
            default                     => $this->errorResponse($id, -32601, 'Method not found'),
        };
    }

    private function handleInitialize(array $message): array
    {
        return [
            'jsonrpc' => '2.0',
            'id'      => $message['id'],
            'result'  => [
                'protocolVersion' => '2024-11-05',
                'capabilities'    => ['tools' => new \stdClass()],
                'serverInfo'      => ['name' => 'shuriken', 'version' => '7.0.0'],
            ],
        ];
    }

    private function handleToolsList(array $message): array
    {
        $tools = [
            [
                'name'        => 'scrape',
                'description' => 'Scrape gambar dan generate konten AI untuk keyword/niche tertentu.',
                'inputSchema' => [
                    'type'       => 'object',
                    'properties' => [
                        'keywords'   => ['type' => 'string',  'description' => 'Kata kunci / niche, pisahkan dengan koma'],
                        'language'   => ['type' => 'string',  'description' => 'Kode bahasa (misal: id, en). Default: id'],
                        'country'    => ['type' => 'string',  'description' => 'Kode negara (misal: id, us). Default: id'],
                        'provider'   => ['type' => 'string',  'description' => 'AI provider: groq, openai, gemini, claude, dll'],
                        'max'        => ['type' => 'integer', 'description' => 'Max gambar per keyword. Default: 10'],
                        'clear_data' => ['type' => 'boolean', 'description' => 'Hapus database sebelum scrape. Default: false'],
                    ],
                    'required' => ['keywords'],
                ],
            ],
            [
                'name'        => 'export',
                'description' => 'Export konten database ke HTML dengan tema pilihan.',
                'inputSchema' => [
                    'type'       => 'object',
                    'properties' => [
                        'theme'       => ['type' => 'string', 'description' => 'Nama tema HTML. Default: five-dark'],
                        'site_name'   => ['type' => 'string', 'description' => 'Nama site. Default: keyword pertama'],
                        'description' => ['type' => 'string', 'description' => 'Deskripsi site. Default: auto'],
                    ],
                    'required' => [],
                ],
            ],
            [
                'name'        => 'deploy',
                'description' => 'Deploy hasil export HTML ke Netlify, Cloudflare Pages, atau Vercel.',
                'inputSchema' => [
                    'type'       => 'object',
                    'properties' => [
                        'site' => ['type' => 'string', 'description' => 'Nama site target (dari list_sites)'],
                    ],
                    'required' => ['site'],
                ],
            ],
            [
                'name'        => 'create_site',
                'description' => 'Workflow lengkap satu langkah: scrape → export HTML → deploy. Gunakan ini untuk membuat dan publish website dari nol.',
                'inputSchema' => [
                    'type'       => 'object',
                    'properties' => [
                        'keywords'  => ['type' => 'string',  'description' => 'Kata kunci / niche, pisahkan dengan koma'],
                        'site'      => ['type' => 'string',  'description' => 'Nama site target (dari list_sites)'],
                        'theme'     => ['type' => 'string',  'description' => 'Tema HTML. Default: five-dark'],
                        'provider'  => ['type' => 'string',  'description' => 'AI provider: groq, openai, gemini, claude, dll'],
                        'language'  => ['type' => 'string',  'description' => 'Kode bahasa. Default: id'],
                        'country'   => ['type' => 'string',  'description' => 'Kode negara. Default: id'],
                        'max'       => ['type' => 'integer', 'description' => 'Max gambar per keyword. Default: 10'],
                        'site_name' => ['type' => 'string',  'description' => 'Nama site untuk HTML. Default: keyword pertama'],
                    ],
                    'required' => ['keywords', 'site'],
                ],
            ],
            [
                'name'        => 'list_sites',
                'description' => 'Tampilkan semua deployment targets yang sudah dikonfigurasi (Netlify, Cloudflare, Vercel, dll).',
                'inputSchema' => [
                    'type'       => 'object',
                    'properties' => new \stdClass(),
                    'required'   => [],
                ],
            ],
            [
                'name'        => 'list_tokens',
                'description' => 'Tampilkan semua AI provider yang tersedia beserta jumlah token yang dikonfigurasi.',
                'inputSchema' => [
                    'type'       => 'object',
                    'properties' => new \stdClass(),
                    'required'   => [],
                ],
            ],
            [
                'name'        => 'db_status',
                'description' => 'Tampilkan jumlah posts dan gambar yang ada di database saat ini.',
                'inputSchema' => [
                    'type'       => 'object',
                    'properties' => new \stdClass(),
                    'required'   => [],
                ],
            ],
            [
                'name'        => 'clear_data',
                'description' => 'Reset database dan folder exports. Hapus semua posts, gambar, dan file export.',
                'inputSchema' => [
                    'type'       => 'object',
                    'properties' => new \stdClass(),
                    'required'   => [],
                ],
            ],
        ];

        return [
            'jsonrpc' => '2.0',
            'id'      => $message['id'],
            'result'  => ['tools' => $tools],
        ];
    }

    private function handleToolsCall(array $message): array
    {
        $id        = $message['id'] ?? null;
        $toolName  = $message['params']['name'] ?? '';
        $input     = $message['params']['arguments'] ?? [];

        fwrite(STDERR, "[shuriken-mcp] tool_call: {$toolName}\n");

        try {
            $text = match ($toolName) {
                'scrape'      => $this->toolScrape($input),
                'export'      => $this->toolExport($input),
                'deploy'      => $this->toolDeploy($input),
                'create_site' => $this->toolCreateSite($input),
                'list_sites'  => $this->toolListSites(),
                'list_tokens' => $this->toolListTokens(),
                'db_status'   => $this->toolDbStatus(),
                'clear_data'  => $this->toolClearData(),
                default       => throw new \RuntimeException("Unknown tool: {$toolName}"),
            };
        } catch (\Throwable $e) {
            fwrite(STDERR, "[shuriken-mcp] error: " . $e->getMessage() . "\n");
            return $this->errorResponse($id, -32603, $e->getMessage());
        }

        return [
            'jsonrpc' => '2.0',
            'id'      => $id,
            'result'  => [
                'content' => [
                    ['type' => 'text', 'text' => $text],
                ],
            ],
        ];
    }

    // -------------------------------------------------------------------------
    // Tool implementations
    // -------------------------------------------------------------------------

    private function toolScrape(array $input): string
    {
        $args = [
            '--keywords' => $input['keywords'],
            '--language' => $input['language'] ?? 'id',
            '--country'  => $input['country']  ?? 'id',
            '--ai'       => true,
            '--max'      => $input['max']      ?? 10,
        ];

        if (!empty($input['provider'])) {
            $args['--provider'] = $input['provider'];
        }

        if (!empty($input['clear_data'])) {
            $args['--clear_data'] = true;
        }

        $output = new BufferedOutput();
        $this->callArtisan('image:scrape', $args, $output);

        return $output->fetch();
    }

    private function toolExport(array $input): string
    {
        $args = [
            '--platform' => 'html',
            '--theme'    => $input['theme']    ?? 'five-dark',
        ];

        if (!empty($input['site_name'])) {
            $args['--site_name'] = $input['site_name'];
        }

        if (!empty($input['description'])) {
            $args['--description'] = $input['description'];
        }

        $output = new BufferedOutput();
        $this->callArtisan('image:export', $args, $output);

        return $output->fetch();
    }

    private function toolDeploy(array $input): string
    {
        $args = ['--site' => $input['site']];

        $output = new BufferedOutput();
        $this->callArtisan('site:deploy', $args, $output);

        return $output->fetch();
    }

    private function toolCreateSite(array $input): string
    {
        $results = [];

        // Step 1: Scrape
        $results[] = "=== SCRAPE ===";
        $results[] = $this->toolScrape([
            'keywords' => $input['keywords'],
            'language' => $input['language'] ?? 'id',
            'country'  => $input['country']  ?? 'id',
            'provider' => $input['provider'] ?? null,
            'max'      => $input['max']      ?? 10,
        ]);

        // Step 2: Export
        $results[] = "=== EXPORT ===";
        $results[] = $this->toolExport([
            'theme'     => $input['theme']     ?? 'five-dark',
            'site_name' => $input['site_name'] ?? '',
        ]);

        // Step 3: Deploy
        $results[] = "=== DEPLOY ===";
        $results[] = $this->toolDeploy(['site' => $input['site']]);

        return implode("\n", $results);
    }

    private function toolListSites(): string
    {
        $path = base_path('storage/sites.json');

        if (!file_exists($path)) {
            return "No sites configured. Use `php shuriken site add` to add a deployment target.";
        }

        $sites = json_decode(file_get_contents($path), true) ?? [];

        if (empty($sites)) {
            return "No sites configured.";
        }

        $lines = ["Configured deployment sites:", ""];
        foreach ($sites as $alias => $config) {
            $platform = $config['platform'] ?? 'unknown';
            $extra    = match ($platform) {
                'netlify'          => "site_id: " . ($config['site_id'] ?? '-'),
                'cloudflare-pages' => "project: " . ($config['project_name'] ?? '-'),
                'vercel'           => "project: " . ($config['project_name'] ?? '-'),
                'wordpress'        => "url: " . ($config['url'] ?? '-'),
                default            => '',
            };
            $lines[] = "  [{$alias}] platform={$platform}" . ($extra ? ", {$extra}" : '');
        }

        return implode("\n", $lines);
    }

    private function toolListTokens(): string
    {
        $path = base_path('storage/tokens.json');

        if (!file_exists($path)) {
            return "No tokens configured. Use `php shuriken token add` to add an AI provider token.";
        }

        $tokens = json_decode(file_get_contents($path), true) ?? [];

        if (empty($tokens)) {
            return "No tokens configured.";
        }

        $lines = ["Configured AI providers:", ""];
        foreach ($tokens as $provider => $tokenList) {
            $count   = is_array($tokenList) ? count($tokenList) : 1;
            $lines[] = "  [{$provider}] {$count} token(s)";
        }

        return implode("\n", $lines);
    }

    private function toolDbStatus(): string
    {
        try {
            $posts  = DB::table('posts')->count();
            $images = DB::table('images')->count();

            return implode("\n", [
                "Database status:",
                "  posts:  {$posts}",
                "  images: {$images}",
            ]);
        } catch (\Throwable $e) {
            return "DB error: " . $e->getMessage();
        }
    }

    private function toolClearData(): string
    {
        $output = new BufferedOutput();
        $this->callArtisan('clear:data', [], $output);

        return $output->fetch();
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function callArtisan(string $command, array $args, BufferedOutput $output): int
    {
        return \Illuminate\Support\Facades\Artisan::call($command, $args, $output);
    }

    private function errorResponse(?int $id, int $code, string $message): array
    {
        return [
            'jsonrpc' => '2.0',
            'id'      => $id,
            'error'   => ['code' => $code, 'message' => $message],
        ];
    }
}
