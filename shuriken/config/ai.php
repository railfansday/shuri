<?php
return [
    'image_count' => 2,
    'tones' => [
        'personal',
        'professional',
        'engaging',
        'black',
        'white',
        'asian',
        'funny'
    ],
    'openai_options' => [
        'model' => 'gpt-4.1-mini',
        'max_tokens' => 1000,
    ],
    'openai_tokens' => [],

    'gemini_options' => [
        'model' => 'gemini-2.5-flash',
    ],
    'gemini_tokens' => [],

    'claude_options' => [
        'model' => 'claude-haiku-4-5',
        'max_tokens' => 1024,
    ],
    'claude_tokens' => [],

    'deepseek_options' => [
        'model' => 'deepseek-chat',
    ],
    'deepseek_tokens' => [],

    'groq_options' => [
        'model' => 'llama-3.3-70b-versatile',
    ],
    'groq_tokens' => [],

    'openrouter_options' => [
        'model' => 'google/gemini-2.5-flash',
    ],
    'openrouter_tokens' => [],

    'kimi_options' => [
        'model' => 'moonshot-v1-8k',
    ],
    'kimi_tokens' => [],

    'grok_options' => [
        'model' => 'grok-2-1212',
    ],
    'grok_tokens' => [],

    'mistral_options' => [
        'model' => 'mistral-small-latest',
    ],
    'mistral_tokens' => [],

    'together_options' => [
        'model' => 'meta-llama/Llama-3-8b-instruct',
    ],
    'together_tokens' => [],

    'perplexity_options' => [
        'model' => 'llama-3.1-sonar-small-128k-online',
    ],
    'perplexity_tokens' => [],

    'fireworks_options' => [
        'model' => 'accounts/fireworks/models/llama-v3p3-70b-instruct',
    ],
    'fireworks_tokens' => [],

    'cerebras_options' => [
        'model' => 'llama-3.3-70b',
    ],
    'cerebras_tokens' => [],

    'siliconflow_options' => [
        'model' => 'Qwen/Qwen2.5-7B-Instruct',
    ],
    'siliconflow_tokens' => [],

    'novita_options' => [
        'model' => 'meta-llama/llama-3.3-70b-instruct',
    ],
    'novita_tokens' => [],

    // Local providers — tidak butuh token
    'ollama_options' => [
        'model'    => 'llama3.2',
        'base_url' => 'http://localhost:11434/v1',
    ],
    'lmstudio_options' => [
        'model'    => 'local-model',
        'base_url' => 'http://localhost:1234/v1',
    ],

    'claude_cli_options' => [
        'command' => 'claude',
    ],
    'gemini_cli_options' => [
        'command' => 'gemini',
    ],
];
