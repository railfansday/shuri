<?php

namespace App\Commands;

use App\Image;
use App\Keyword;
use App\Post;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use LaravelZero\Framework\Commands\Command;
use Spatie\Browsershot\Browsershot;
use ZipArchive;

class DocsScreenshots extends Command
{
    const THEMES = ['default', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven'];

    protected $signature = 'docs:screenshots';
    protected $description = 'Generate theme preview screenshots for documentation';

    public function handle()
    {
        Artisan::call('migrate');

        $seeded = false;
        if (Post::count() === 0) {
            $this->line('<fg=gray>Database kosong. Menyiapkan dummy data...</>');
            $this->seedDummyData();
            $seeded = true;
        }

        $outDir = base_path('docs/html/img/themes');
        if (!is_dir($outDir)) {
            mkdir($outDir, 0755, true);
        }

        $this->line('');
        $this->line('<comment>Generating theme screenshots...</comment>');
        $this->line('');

        foreach (self::THEMES as $theme) {
            $this->task($theme, function () use ($theme, $outDir) {
                Artisan::call('image:export', [
                    '--platform'  => 'html',
                    '--theme'     => $theme,
                    '--site_name' => 'Shuriken Preview',
                ]);

                $zipPath = base_path('exports/html-1.zip');
                if (!file_exists($zipPath)) {
                    throw new \RuntimeException("Export ZIP not found: {$zipPath}");
                }

                $zip = new ZipArchive();
                $zip->open($zipPath);
                $html = $zip->getFromName('index.html');
                $zip->close();

                if ($html === false) {
                    throw new \RuntimeException("index.html not found in ZIP for theme: {$theme}");
                }

                Browsershot::html($html)
                    ->windowSize(1280, 800)
                    ->save("{$outDir}/{$theme}.png");

                unlink($zipPath);
            });
        }

        $this->line('');

        if ($seeded) {
            $this->line('<fg=gray>Dummy data masih ada di database. Jalankan php shuriken clear:data jika ingin membersihkan.</fg=gray>');
        }

        $this->info('Screenshots saved to docs/html/img/themes/');
        $this->line('Jalankan <comment>php shuriken docs:build</comment> untuk rebuild HTML docs.');
    }

    private function seedDummyData(): void
    {
        $faker = \Faker\Factory::create();

        $imagePool = [
            'https://picsum.photos/seed/10/600/400',
            'https://picsum.photos/seed/20/600/400',
            'https://picsum.photos/seed/30/600/400',
            'https://picsum.photos/seed/40/600/400',
            'https://picsum.photos/seed/50/600/400',
            'https://picsum.photos/seed/60/600/400',
            'https://picsum.photos/seed/70/600/400',
            'https://picsum.photos/seed/80/600/400',
        ];

        $thumbPool = [
            'https://picsum.photos/seed/10/300/200',
            'https://picsum.photos/seed/20/300/200',
            'https://picsum.photos/seed/30/300/200',
            'https://picsum.photos/seed/40/300/200',
            'https://picsum.photos/seed/50/300/200',
            'https://picsum.photos/seed/60/300/200',
            'https://picsum.photos/seed/70/300/200',
            'https://picsum.photos/seed/80/300/200',
        ];

        for ($i = 0; $i < 8; $i++) {
            $keyword = Keyword::create([
                'name' => $faker->unique()->words(2, true),
            ]);

            Image::create([
                'keyword_id' => $keyword->id,
                'url'        => $imagePool[$i],
                'thumbnail'  => $thumbPool[$i],
                'title'      => $faker->sentence(4),
                'link'       => 'https://example.com',
                'domain'     => 'example.com',
                'size'       => '600x400',
            ]);

            Post::create([
                'keyword_id'   => $keyword->id,
                'title'        => $faker->sentence(5),
                'content'      => '<p>' . implode('</p><p>', $faker->paragraphs(3)) . '</p>',
                'slug'         => $keyword->id . '-' . str($keyword->name)->slug(),
                'published_at' => Carbon::now()->subDays(rand(1, 30)),
                'category'     => 'Preview',
                'tags'         => [],
            ]);
        }
    }
}
