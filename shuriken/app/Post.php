<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Anthropic\Client as AnthropicClient;
use Gemini;
use OpenAI;
use Symfony\Component\Process\Process;

class Post extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = [
        "published_at" => "datetime",
        "tags" => "array",
    ];

    const TEMPLATES = ["category", "title", "json_ld", "content", "tags"];


    public static function regenerate(Keyword $keyword, $path = "image"): Post
    {
        $post = new Post();
        $post->keyword_id = $keyword->id;

        $post->load(['keyword', 'keyword.images']);

        $post = $post->setPublicationDate();


        if (config('shuriken.ai') && is_null($post->ai_content)) {
            $providers = self::getFallbackProviders();
            $lastException = null;

            foreach ($providers as $provider) {
                try {
                    config(['shuriken.provider' => $provider]);
                    $post->ai_content = trim(backoff(function () use ($post) {
                        return match (config('shuriken.provider')) {
                            'gemini'                                       => self::getContentFromGeminiAI($post),
                            'claude'                                       => self::getContentFromClaudeAI($post),
                            'claude-cli', 'gemini-cli'                     => self::getContentFromCLI($post, config('shuriken.provider')),
                            'deepseek', 'groq', 'openrouter',
                            'kimi', 'grok', 'mistral', 'together',
                            'perplexity', 'ollama', 'lmstudio',
                            'fireworks', 'cerebras', 'siliconflow', 'novita' => self::getContentFromOpenAICompatible($post, config('shuriken.provider')),
                            default => array_key_exists(config('shuriken.provider'), config('ai.custom_providers', []))
                                ? self::getContentFromOpenAICompatible($post, config('shuriken.provider'))
                                : self::getContentFromOpenAI($post),
                        };
                    }));
                    break;
                } catch (\Throwable $e) {
                    $lastException = $e;
                    config('shuriken.cli')?->warn("    Provider <comment>{$provider}</comment> failed: " . $e->getMessage());
                    config('shuriken.cli')?->line("    <fg=gray>→</> Trying next provider...");
                }
            }

            if (is_null($post->ai_content)) {
                throw new \RuntimeException('All AI providers failed. Last error: ' . $lastException?->getMessage());
            }

            // Extract <title>...</title> from AI content if present
            if (preg_match('/<title>(.*?)<\/title>/is', $post->ai_content, $matches)) {
                $post->ai_title = trim(strip_tags($matches[1]));
                $post->ai_content = trim(preg_replace('/<title>.*?<\/title>/is', '', $post->ai_content));
            }
        }

        foreach (self::TEMPLATES as $template) {
            $template = $path . "." . $template;
            $post = self::generate($template, $post);

            $post = self::spin($template, $post);
        }

        $post = $post->markdownify();

        $post->slug = str(
            $post->id . "-" . htmlspecialchars_decode($post->keyword->name)
        )->slug("-", config("shuriken.language"));

        $post->category = config("export.category");
        $post->save();

        return $post;
    }

    public function markdownify()
    {
        $this->markdown = Helper::markdownify($this->content);
        return $this;
    }

    public static function spin($template, $post)
    {
        $post->{self::getColumn($template)} = Helper::spintax(
            $post->{self::getColumn($template)}
        );

        return $post;
    }

    public function setPublicationDate()
    {
        if (is_string(config("export.start"))) {
            config([
                "export.start" => Carbon::createFromFormat(
                    "Y-m-d",
                    config("export.start")
                ),
            ]);
        }

        if (is_string(config("export.end"))) {
            config([
                "export.end" => Carbon::createFromFormat(
                    "Y-m-d",
                    config("export.end")
                ),
            ]);
        }
        $start = config("export.start")->timestamp;
        $end = config("export.end")->timestamp;

        $date = Carbon::createFromTimestamp(rand($start, $end));

        $this->published_at = $date;

        return $this;
    }

    public static function generate(string $template, Post $post)
    {
        if (str($template)->contains("tags")) {
            $tags = [];

            foreach ($post->keyword->related_keywords as $related_keyword) {
                $tags[] = trim(
                    view($template, ["keyword" => $related_keyword])->render()
                );
            }

            $post->tags = $tags;

            return $post;
        }


        $content = view($template, [
            "post" => $post,
            "keyword" => $post->keyword,
            "images" => $post->keyword->images,
            "answer" => $post->keyword->answer,
        ])->render();

        $post->{self::getColumn($template)} = trim($content);

        return $post;
    }

    private static function generatePrompt($post): string
    {
        $images = $post->keyword->images()
            ->select('url', 'title')
            ->get()
            ->shuffle()
            ->take(config('ai.image_count'));

        $promptTemplate = config('prompts.' . config('shuriken.prompt'));
        $replacements = [
            '{{images_json}}' => $images->toJSON(),
            '{{keyword}}' => $post->keyword->name,
            '{{tone}}' => collect(config('ai.tones'))->random(),
            '{{language}}' => config('shuriken.language'),
            '{{country}}' => config('shuriken.country'),
        ];

        foreach (config('shuriken') as $key => $option) {
            if (is_string($option)) {
                $replacements['{{' . $key . '}}'] = $option;
            }
        }

        $prompt = str_replace(array_keys($replacements), array_values($replacements), $promptTemplate);

        return $prompt;
    }

    public static function getContentFromGeminiAI($post)
    {
        $tokens = self::requireTokens('gemini');
        $index = cache('ai_gemini_token_index', 0);
        $apikey = $tokens[$index % count($tokens)];
        cache(['ai_gemini_token_index' => $index + 1]);
        config('shuriken.cli')?->line('    <question>-></question> GetContentFromGeminiAI <comment>(Token: ' . Str::of($apikey)->limit(10, '***') . ')</comment>');

        $client = Gemini::client($apikey);
        $prompt = self::generatePrompt($post);
        $model = config('shuriken.model') ?: config('ai.gemini_options.model');

        $result = $client->generativeModel(model: $model)->generateContent($prompt);
        
        $content = $result->text(); 
        $content = self::ai_cleanup($content);

        return $content;
    }

    public static function getContentFromOpenAI($post)
    {
        $tokens = self::requireTokens('openai');
        $index = cache('ai_openai_token_index', 0);
        $token = $tokens[$index % count($tokens)];
        cache(['ai_openai_token_index' => $index + 1]);
        config('shuriken.cli')?->line('    <question>-></question> GetContentFromOpenAI <comment>(Token: ' . Str::of($token)->limit(10, '***') . ')</comment>');

        $client = OpenAI::client($token);

        $prompt = self::generatePrompt($post);
        $model = config('shuriken.model') ?: config('ai.openai_options.model');

        if (str($model)->contains('gpt')) {
            $result = $client->chat()->create([
                'model' => $model,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ]
            ]);

            $content = $result->choices[0]->message->content;
        } else {
            $options = config('ai.openai_options');
            $options['model'] = $model;
            $options['prompt'] = $prompt;
            $result = $client->completions()->create($options);

            $content = $result['choices'][0]['text'];
        }

        $content = self::ai_cleanup($content);

        return trim($content);
    }

    public static function getContentFromClaudeAI($post): string
    {
        $tokens = self::requireTokens('claude');
        $index = cache('ai_claude_token_index', 0);
        $token = $tokens[$index % count($tokens)];
        cache(['ai_claude_token_index' => $index + 1]);
        config('shuriken.cli')?->line('    <question>-></question> GetContentFromClaudeAI <comment>(Token: ' . Str::of($token)->limit(10, '***') . ')</comment>');

        $model = config('shuriken.model') ?: config('ai.claude_options.model');

        $client = new AnthropicClient($token);
        $result = $client->messages->create(
            maxTokens: config('ai.claude_options.max_tokens', 1024),
            messages: [['role' => 'user', 'content' => self::generatePrompt($post)]],
            model: $model,
        );

        return self::ai_cleanup($result->content[0]->text);
    }

    private static function getContentFromOpenAICompatible($post, string $provider): string
    {
        $localProviders = ['ollama', 'lmstudio'];
        if (in_array($provider, $localProviders)) {
            $token = 'local';
            config('shuriken.cli')?->line('    <question>-></question> GetContentFrom' . ucfirst($provider) . ' <comment>(Local)</comment>');
        } else {
            $tokens = self::requireTokens($provider);
            $index = cache("ai_{$provider}_token_index", 0);
            $token = $tokens[$index % count($tokens)];
            cache(["ai_{$provider}_token_index" => $index + 1]);
            config('shuriken.cli')?->line('    <question>-></question> GetContentFrom' . ucfirst($provider) . 'AI <comment>(Token: ' . Str::of($token)->limit(10, '***') . ')</comment>');
        }

        $customProviders = config('ai.custom_providers', []);

        $baseUrls = [
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
            'ollama'      => config('ai.ollama_options.base_url', 'http://localhost:11434/v1'),
            'lmstudio'    => config('ai.lmstudio_options.base_url', 'http://localhost:1234/v1'),
        ];

        $model = config('shuriken.model')
            ?: config("ai.{$provider}_options.model")
            ?: ($customProviders[$provider]['model'] ?? null);

        $baseUrl = $baseUrls[$provider] ?? $customProviders[$provider]['base_url'] ?? null;
        if (!$baseUrl) {
            throw new \RuntimeException("No base URL configured for provider: {$provider}");
        }

        $client = OpenAI::factory()
            ->withApiKey($token)
            ->withBaseUri($baseUrl)
            ->make();

        $result = $client->chat()->create([
            'model'    => $model,
            'messages' => [['role' => 'user', 'content' => self::generatePrompt($post)]],
        ]);

        return self::ai_cleanup($result->choices[0]->message->content);
    }

    private static function getContentFromCLI($post, string $provider): string
    {
        $commands = [
            'claude-cli' => [config('ai.claude_cli_options.command', 'claude'), '--print'],
            'gemini-cli' => [config('ai.gemini_cli_options.command', 'gemini'), 'prompt', '-'],
        ];

        config('shuriken.cli')?->line('    <question>-></question> GetContentFromCLI <comment>(Provider: ' . $provider . ')</comment>');

        $process = new Process($commands[$provider]);
        $process->setInput(self::generatePrompt($post));
        $process->setTimeout(120);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException("CLI provider '$provider' failed: " . $process->getErrorOutput());
        }

        return self::ai_cleanup(trim($process->getOutput()));
    }

    private static function getFallbackProviders(): array
    {
        $primary = config('shuriken.provider');

        $apiProviders = [
            'openai', 'gemini', 'claude', 'groq', 'deepseek', 'openrouter',
            'kimi', 'grok', 'mistral', 'together', 'perplexity',
            'fireworks', 'cerebras', 'siliconflow', 'novita',
        ];
$list = $primary ? [$primary] : [];

        foreach ($apiProviders as $p) {
            if ($p !== $primary && !empty(config("ai.{$p}_tokens", []))) {
                $list[] = $p;
            }
        }

        foreach (array_keys(config('ai.custom_providers', [])) as $p) {
            if ($p !== $primary && !empty(config("ai.{$p}_tokens", []))) {
                $list[] = $p;
            }
        }

        return $list;
    }

    private static function requireTokens(string $provider): array
    {
        $tokens = config("ai.{$provider}_tokens", []);
        if (empty($tokens)) {
            throw new \RuntimeException(
                "No tokens configured for '{$provider}'. Run: php shuriken token add {$provider} <your-token>"
            );
        }
        return $tokens;
    }

    public static function ai_cleanup($content)
    {
        $content = preg_replace('/<\/p>/', '</p><!--more-->', $content, 1);
        $content = str($content)->remove(['```html', '```'])->toString();

        return trim($content);
    }

    public static function getColumn(string $template): string
    {
        return str($template)
            ->after(".")
            ->toString();
    }

    public function keyword()
    {
        return $this->belongsTo(Keyword::class);
    }

    public function getPreviousAttribute()
    {
        return Post::where("id", "<", $this->id)
            ->orderBy("id", "desc")
            ->first();
    }

    public function getParentAttribute()
    {
        $parent_keyword = $this->keyword->parent;
        if (is_null($parent_keyword)) {
            return null;
        }

        return Post::where("keyword_id", $parent_keyword->id)->first();
    }

    public function getNextAttribute()
    {
        return Post::where("id", ">", $this->id)
            ->orderBy("id", "asc")
            ->whereNotNull("content")
            ->first();
    }

    public function getRelatedAttribute()
    {
        return Post::where("keyword_id", $this->keyword->id)->get();
    }
}