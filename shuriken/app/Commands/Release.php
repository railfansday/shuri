<?php

namespace App\Commands;

use Illuminate\Support\Facades\Artisan;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Process\Process;

class Release extends Command
{
    protected $signature = 'release {version? : Version number, e.g. 7.0.2 (default: auto-increment patch)}';
    protected $description = 'Build a release zip for the member area';

    public function handle()
    {
        $version = $this->argument('version') ?? $this->resolveVersion(base_path());
        $zipName = "shuriken-{$version}.zip";
        $root    = base_path();

        $this->info("Releasing Shuriken v{$version}");
        $this->newLine();

        // 1. Delete old release zips
        $this->task('Hapus zip rilis lama', function () use ($root) {
            foreach (glob("{$root}/shuriken-*.zip") as $zip) {
                unlink($zip);
            }
        });

        // 2. Stage, generate commit message via AI, confirm, commit
        $this->runProcess(['git', 'add', '-A'], $root);

        $commitMessage = $this->generateCommitMessage($root, $version);

        $this->newLine();
        $this->line("  <fg=yellow>Commit message:</> {$commitMessage}");
        $commitMessage = $this->ask('  Edit atau Enter untuk pakai ini', $commitMessage);
        $this->newLine();

        $this->task('Catat changelog', function () use ($version, $root, $commitMessage) {
            $entry    = "## v{$version} — " . date('Y-m-d') . "\n- {$commitMessage}\n\n";
            $file     = "{$root}/CHANGELOG.md";
            $existing = file_exists($file) ? file_get_contents($file) : '';
            file_put_contents($file, $entry . $existing);
        });

        $this->task('Commit, tag dan push', function () use ($version, $root, $commitMessage) {
            $this->runProcess(['git', 'add', 'CHANGELOG.md'], $root);
            $this->runProcess(['git', 'commit', '-m', $commitMessage], $root);
            $tagExists = (new Process(['git', 'tag', '-l', "v{$version}"], $root))->mustRun()->getOutput();
            if (trim($tagExists)) {
                $this->runProcess(['git', 'tag', '-d', "v{$version}"], $root);
                $this->runProcess(['git', 'push', 'origin', "--delete", "v{$version}"], $root);
            }
            $this->runProcess(['git', 'tag', "v{$version}"], $root);
            $this->runProcess(['git', 'push', 'origin', 'main'], $root);
            $this->runProcess(['git', 'push', 'origin', "v{$version}"], $root);
        });

        // 3. Deploy docs
        $this->task('Deploy docs', function () use ($root) {
            $process = new Process(
                ['npx', 'wrangler', 'pages', 'deploy', 'docs/html', '--project-name=shuriken-docs'],
                $root
            );
            $process->setTimeout(120);
            $process->mustRun();
        });

        // 4. Delete builds/
        $this->task('Hapus builds/', function () use ($root) {
            if (is_dir("{$root}/builds")) {
                $this->deleteDirectory("{$root}/builds");
            }
        });

        // 4. Clear data
        $this->task('Clear data', function () {
            Artisan::call('clear:data');
        });

        // 5. Zip
        $this->task("Buat {$zipName}", function () use ($root, $zipName) {
            $exclude = [
                '--exclude=*.zip',
                '--exclude=*.DS_Store',
                '--exclude=shuriken/.git/*',
                '--exclude=shuriken/.git',
                '--exclude=shuriken/.claude/*',
                '--exclude=shuriken/.claude',
                '--exclude=shuriken/.idea/*',
                '--exclude=shuriken/.idea',
                '--exclude=shuriken/.wrangler/*',
                '--exclude=shuriken/.wrangler',
                '--exclude=shuriken/.phpunit.result.cache',
                '--exclude=shuriken/.gitignore',
                '--exclude=shuriken/node_modules/*',
                '--exclude=shuriken/node_modules',
                '--exclude=shuriken/exports/*',
                '--exclude=shuriken/omakase/*',
                '--exclude=shuriken/builds/*',
                '--exclude=shuriken/builds',
                '--exclude=shuriken/bing.html',
                '--exclude=shuriken/CLAUDE.md',
                '--exclude=shuriken/scripts/*',
                '--exclude=shuriken/scripts',
            ];

            $cmd = array_merge(
                ['zip', '-r', "{$root}/{$zipName}", 'shuriken'],
                $exclude
            );

            $this->runProcess($cmd, dirname($root));
        });

        $this->task("Upload {$zipName} ke R2", function () use ($root, $zipName, $version) {
            $this->runProcess(
                ['node', 'scripts/upload_r2.cjs', "{$root}/{$zipName}", "products/{$zipName}"],
                $root
            );
        });

        $this->newLine();
        $this->info("✓ {$zipName} uploaded ke R2: products/{$zipName}");
    }

    private function resolveVersion(string $root): string
    {
        $process = new Process(['git', 'describe', '--tags', '--abbrev=0'], $root);
        $process->run();

        if (!$process->isSuccessful()) {
            return '1.0.0';
        }

        $tag  = ltrim(trim($process->getOutput()), 'v');
        $parts = explode('.', $tag);
        $parts[2] = (int) ($parts[2] ?? 0) + 1;

        return implode('.', $parts);
    }

    private function generateCommitMessage(string $root, string $version): string
    {
        $diffProcess = new Process(['git', 'diff', '--cached', '--stat'], $root);
        $diffProcess->run();
        $stat = trim($diffProcess->getOutput());

        if (empty($stat)) {
            return "release: v{$version}";
        }

        $prompt = "Based on this git diff stat, write a single concise commit message (max 72 chars, imperative mood, no period). Output only the commit message, nothing else.\n\n{$stat}";

        $claudeProcess = new Process(['claude', '-p', $prompt], $root);
        $claudeProcess->setTimeout(30);
        $claudeProcess->run();

        if ($claudeProcess->isSuccessful()) {
            $message = trim($claudeProcess->getOutput());
            if (!empty($message) && strlen($message) <= 100) {
                return $message;
            }
        }

        // Fallback: summarize changed files manually
        return "release: v{$version}";
    }

    private function runProcess(array $cmd, string $cwd): void
    {
        $process = new Process($cmd, $cwd);
        $process->setTimeout(300);
        $process->mustRun();
    }

    private function deleteDirectory(string $path): void
    {
        $process = new Process(['rm', '-rf', $path]);
        $process->mustRun();
    }
}
