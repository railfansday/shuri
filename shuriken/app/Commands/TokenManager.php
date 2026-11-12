<?php

namespace App\Commands;

use Anthropic\Client as AnthropicClient;
use Gemini;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;
use OpenAI;

class TokenManager extends Command
{
    const PROVIDERS = [
        'openai', 'gemini', 'claude', 'deepseek', 'groq',
        'openrouter', 'kimi', 'grok', 'mistral', 'together', 'perplexity',
        'fireworks', 'cerebras', 'siliconflow', 'novita',
    ];

    const BASE_URLS = [
        'deepseek'    => 'https://api.deepseek.com/v1',
        'groq'        => 'https://api.groq.com/openai/v1',
        'openrouter'  => 'https://openrouter.ai/api/v1',
        'kimi'        => 'https://api.moonshot.cn/v1',
        'grok'        => 'https://api.x.ai/v1',
        'mistral'     => 'https://api.mistral.ai/v1',
        'together'    => 'https://api.together.xyz/v1',
        'perplexity'  => 'https://api.perplexity.ai',
        'fireworks'   => 'https://api.fireworks.ai/inference/v1',
        'cerebras'    => 'https://api.cerebras.ai/v1',
        'siliconflow' => 'https://api.siliconflow.com/v1',
        'novita'      => 'https://api.novita.ai/openai',
    ];

    protected $signature = 'token
        {action : add, remove, list, or test}
        {provider? : AI provider name}
        {token? : The API token}
        {--base_url= : Base URL for custom OpenAI-compatible provider (e.g. https://my-server.com/v1)}
        {--model= : Default model for custom provider}
    ';

    protected $description = 'Manage AI provider tokens (add, remove, list, test)';

    public function handle()
    {
        $action = $this->argument('action');

        match ($action) {
            'list'   => $this->actionList(),
            'add'    => $this->actionAdd(),
            'remove' => $this->actionRemove(),
            'test'   => $this->actionTest(),
            default  => $this->error("Unknown action: {$action}. Use: add, remove, list, test"),
        };
    }

    private function actionList(): void
    {
        $stored  = $this->loadTokens();
        $custom  = $this->loadCustomProviders();
        $allProviders = array_unique(array_merge(self::PROVIDERS, array_keys($custom)));

        $this->line('');
        $this->line('<comment>AI Token List</comment>');
        $this->line(str_repeat('─', 50));

        foreach ($allProviders as $provider) {
            $tokens = $stored[$provider] ?? [];
            $count  = count($tokens);
            $label  = str_pad($provider, 12);
            $tag    = isset($custom[$provider]) ? ' <fg=cyan>[custom]</>' : '';

            if ($count === 0) {
                $this->line("  <fg=gray>{$label}</>{$tag} <fg=red>no tokens</>");
            } else {
                $this->line("  <info>{$label}</info>{$tag} <comment>{$count} token(s)</comment>");
                foreach ($tokens as $i => $token) {
                    $masked = Str::of($token)->limit(8, '***');
                    $this->line("    <fg=gray>[{$i}]</> {$masked}");
                }
            }

            if (isset($custom[$provider])) {
                $this->line("    <fg=gray>base_url:</> {$custom[$provider]['base_url']}");
                $this->line("    <fg=gray>model:   </> " . ($custom[$provider]['model'] ?? '(not set)'));
            }
        }

        $this->line('');
    }

    private function actionAdd(): void
    {
        $provider = $this->argument('provider');
        $token = $this->argument('token');

        if (!$provider || !$token) {
            $this->error('Usage: php shuriken token add {provider} {token}');
            return;
        }

        if (!in_array($provider, self::PROVIDERS)) {
            $baseUrl = $this->option('base_url');
            if (!$baseUrl) {
                $this->error("Unknown provider: {$provider}.");
                $this->line('For built-in providers: ' . implode(', ', self::PROVIDERS));
                $this->line('For custom OpenAI-compatible providers, add --base_url=https://...');
                return;
            }
            $this->saveCustomProvider($provider, $baseUrl, $this->option('model'));
            $this->line("<info>✓</info> Custom provider <info>{$provider}</info> registered (base_url: {$baseUrl}).");
        }

        $stored = $this->loadTokens();
        $stored[$provider] = $stored[$provider] ?? [];

        if (in_array($token, $stored[$provider])) {
            $this->warn("Token already exists for {$provider}.");
            return;
        }

        $stored[$provider][] = $token;
        $this->saveTokens($stored);

        $masked = Str::of($token)->limit(8, '***');
        $this->line("<info>✓</info> Token <comment>{$masked}</comment> added to <info>{$provider}</info>.");
    }

    private function actionRemove(): void
    {
        $provider = $this->argument('provider');
        $token = $this->argument('token');

        if (!$provider || !$token) {
            $this->error('Usage: php shuriken token remove {provider} {token}');
            return;
        }

        $custom = $this->loadCustomProviders();
        if (!in_array($provider, self::PROVIDERS) && !isset($custom[$provider])) {
            $this->error("Unknown provider: {$provider}");
            return;
        }

        $stored = $this->loadTokens();
        $list = $stored[$provider] ?? [];

        $filtered = array_values(array_filter($list, fn($t) => $t !== $token));

        if (count($filtered) === count($list)) {
            $this->error("Token not found for {$provider}.");
            if (!empty($list)) {
                $this->line('Current tokens:');
                foreach ($list as $i => $t) {
                    $this->line('  [' . $i . '] ' . Str::of($t)->limit(8, '***'));
                }
            }
            return;
        }

        $stored[$provider] = $filtered;
        $this->saveTokens($stored);

        $masked = Str::of($token)->limit(8, '***');
        $this->line("<info>✓</info> Token <comment>{$masked}</comment> removed from <info>{$provider}</info>.");
    }

    private function actionTest(): void
    {
        $provider = $this->argument('provider');
        $token = $this->argument('token');

        if (!$provider) {
            $this->error('Usage: php shuriken token test {provider} [token]');
            return;
        }

        $custom = $this->loadCustomProviders();
        if (!in_array($provider, self::PROVIDERS) && !isset($custom[$provider])) {
            $this->error("Unknown provider: {$provider}");
            $this->line('Valid providers: ' . implode(', ', self::PROVIDERS));
            return;
        }

        if (!$token) {
            $stored = $this->loadTokens();
            $tokens = $stored[$provider] ?? [];
            if (empty($tokens)) {
                $this->error("No tokens configured for {$provider}. Add one with: php shuriken token add {$provider} {your-token}");
                return;
            }
            $token = $tokens[0];
        }

        $masked = Str::of($token)->limit(8, '***');
        $this->line("Testing <info>{$provider}</info> with token <comment>{$masked}</comment>...");

        $start = microtime(true);

        try {
            $response = $this->callProvider($provider, $token);
            $ms = round((microtime(true) - $start) * 1000);
            $this->line("<info>✓</info> Success ({$ms}ms): <fg=gray>" . Str::of($response)->limit(80) . "</>");
        } catch (\Throwable $e) {
            $ms = round((microtime(true) - $start) * 1000);
            $this->error("✗ Failed ({$ms}ms): " . $e->getMessage());
        }
    }

    private function callProvider(string $provider, string $token): string
    {
        $prompt = 'Reply with just the word: OK';

        if ($provider === 'gemini') {
            $model = config('ai.gemini_options.model', 'gemini-2.5-flash');
            $client = Gemini::client($token);
            $result = $client->generativeModel(model: $model)->generateContent($prompt);
            return $result->text();
        }

        if ($provider === 'claude') {
            $model = config('ai.claude_options.model', 'claude-haiku-4-5');
            $client = new AnthropicClient($token);
            $result = $client->messages->create(
                maxTokens: 16,
                messages: [['role' => 'user', 'content' => $prompt]],
                model: $model,
            );
            return $result->content[0]->text;
        }

        if ($provider === 'openai') {
            $model = config('ai.openai_options.model', 'gpt-4.1-mini');
            $client = OpenAI::client($token);
            $result = $client->chat()->create([
                'model'    => $model,
                'messages' => [['role' => 'user', 'content' => $prompt]],
            ]);
            return $result->choices[0]->message->content;
        }

        // OpenAI-compatible providers
        $customProviders = $this->loadCustomProviders();
        $baseUrl = self::BASE_URLS[$provider] ?? $customProviders[$provider]['base_url'] ?? null;
        if (!$baseUrl) {
            throw new \RuntimeException("No base URL configured for provider: {$provider}");
        }

        $model = config("ai.{$provider}_options.model")
            ?? $customProviders[$provider]['model']
            ?? 'default';
        $client = OpenAI::factory()
            ->withApiKey($token)
            ->withBaseUri($baseUrl)
            ->make();

        $result = $client->chat()->create([
            'model'    => $model,
            'messages' => [['role' => 'user', 'content' => $prompt]],
        ]);

        return $result->choices[0]->message->content;
    }

    private function loadTokens(): array
    {
        $path = base_path('storage/tokens.json');
        if (!file_exists($path)) {
            return [];
        }
        return json_decode(file_get_contents($path), true) ?? [];
    }

    private function saveTokens(array $tokens): void
    {
        $path = base_path('storage/tokens.json');
        $tokens = array_filter($tokens, fn($list) => !empty($list));
        file_put_contents($path, json_encode($tokens, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n");
    }

    private function loadCustomProviders(): array
    {
        $path = base_path('storage/custom_providers.json');
        if (!file_exists($path)) {
            return [];
        }
        return json_decode(file_get_contents($path), true) ?? [];
    }

    private function saveCustomProvider(string $provider, string $baseUrl, ?string $model): void
    {
        $providers = $this->loadCustomProviders();
        $providers[$provider] = array_filter([
            'base_url' => $baseUrl,
            'model'    => $model,
        ]);
        file_put_contents(
            base_path('storage/custom_providers.json'),
            json_encode($providers, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n"
        );
    }
}
