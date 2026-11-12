<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;

class PromptManager extends Command
{
    const BUILT_IN = ['image', 'recipe', 'list', 'review', 'faq', 'tutorial', 'comparison', 'news', 'roundup', 'guide'];

    protected $signature = 'prompt
        {action : list, add, or remove}
        {name? : Prompt name (slug, e.g. my-niche)}
        {template? : Prompt template text}
    ';

    protected $description = 'Manage custom AI prompt templates (add, remove, list)';

    public function handle()
    {
        $action = $this->argument('action');

        match ($action) {
            'list'   => $this->actionList(),
            'add'    => $this->actionAdd(),
            'remove' => $this->actionRemove(),
            default  => $this->error("Unknown action: {$action}. Use: add, remove, list"),
        };
    }

    private function actionList(): void
    {
        $builtIn  = array_keys(config('prompts', []));
        $custom   = $this->loadPrompts();

        $this->line('');
        $this->line('<comment>Built-in Prompts</comment>');
        $this->line(str_repeat('─', 50));
        foreach (self::BUILT_IN as $name) {
            $this->line("  <info>{$name}</info>");
        }

        $this->line('');
        $this->line('<comment>Custom Prompts</comment>');
        $this->line(str_repeat('─', 50));

        if (empty($custom)) {
            $this->line('  <fg=gray>No custom prompts.</>');
        } else {
            foreach ($custom as $name => $template) {
                $preview = \Illuminate\Support\Str::of($template)->limit(60);
                $this->line("  <info>{$name}</info> <fg=gray>{$preview}</>");
            }
        }

        $this->line('');
        $this->line('<fg=gray>Usage: php shuriken image:scrape --ai --prompt={name}</>');
        $this->line('');
    }

    private function actionAdd(): void
    {
        $name     = $this->argument('name');
        $template = $this->argument('template');

        if (!$name || !$template) {
            $this->error('Usage: php shuriken prompt add {name} {template}');
            return;
        }

        if (!preg_match('/^[a-z0-9\-]+$/', $name)) {
            $this->error('Prompt name must be lowercase letters, numbers, and hyphens only.');
            return;
        }

        $builtIn = self::BUILT_IN;
        if (in_array($name, $builtIn)) {
            $this->error("'{$name}' is a built-in prompt and cannot be overwritten.");
            $this->line('Use a different name.');
            return;
        }

        $custom = $this->loadPrompts();

        if (isset($custom[$name])) {
            $this->warn("Prompt '{$name}' already exists. Overwriting.");
        }

        $custom[$name] = $template;
        $this->savePrompts($custom);

        $this->line("<info>✓</info> Prompt <comment>{$name}</comment> saved.");
    }

    private function actionRemove(): void
    {
        $name = $this->argument('name');

        if (!$name) {
            $this->error('Usage: php shuriken prompt remove {name}');
            return;
        }

        $builtIn = self::BUILT_IN;
        if (in_array($name, $builtIn)) {
            $this->error("'{$name}' is a built-in prompt and cannot be removed.");
            return;
        }

        $custom = $this->loadPrompts();

        if (!isset($custom[$name])) {
            $this->error("Prompt '{$name}' not found.");
            if (!empty($custom)) {
                $this->line('Custom prompts: ' . implode(', ', array_keys($custom)));
            }
            return;
        }

        unset($custom[$name]);
        $this->savePrompts($custom);

        $this->line("<info>✓</info> Prompt <comment>{$name}</comment> removed.");
    }

    private function loadPrompts(): array
    {
        $path = base_path('storage/prompts.json');
        if (!file_exists($path)) {
            return [];
        }
        return json_decode(file_get_contents($path), true) ?? [];
    }

    private function savePrompts(array $prompts): void
    {
        $path = base_path('storage/prompts.json');
        file_put_contents($path, json_encode($prompts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n");
    }
}
