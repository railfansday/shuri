<?php

use App\Publishers\ExtractsZip;

// Anonymous class to expose trait methods for testing
$make = function () {
    return new class {
        use ExtractsZip;

        public function extract(string $path): array
        {
            return $this->extractFilesFromZip($path);
        }

        public function normalize(array $files): array
        {
            return $this->stripCommonPrefix($files);
        }

        public function mime(string $path): string
        {
            return $this->mimeType($path);
        }

        public function binary(string $ext): bool
        {
            return $this->isBinary($ext);
        }
    };
};

function makeZip(array $entries): string
{
    $path = sys_get_temp_dir() . '/test-' . uniqid() . '.zip';
    $zip  = new \PhpZip\ZipFile();
    foreach ($entries as $name => $content) {
        $zip->addFromString($name, $content);
    }
    file_put_contents($path, $zip->outputAsString());
    $zip->close();
    return $path;
}

// ---------------------------------------------------------------------------
// extractFilesFromZip
// ---------------------------------------------------------------------------

it('extractFilesFromZip membaca semua file dari ZIP', function () use ($make) {
    $zip = makeZip([
        'index.html' => '<h1>hello</h1>',
        'style.css'  => 'body{}',
    ]);

    $files = $make()->extract($zip);
    unlink($zip);

    expect($files)->toHaveKey('index.html');
    expect($files)->toHaveKey('style.css');
    expect($files['index.html'])->toBe('<h1>hello</h1>');
    expect($files['style.css'])->toBe('body{}');
});

it('extractFilesFromZip tidak menyertakan direktori', function () use ($make) {
    $zip = makeZip([
        'img/logo.png' => 'fakepng',
        'index.html'   => '<h1>hi</h1>',
    ]);

    $files = $make()->extract($zip);
    unlink($zip);

    foreach (array_keys($files) as $path) {
        expect($path)->not->toEndWith('/');
    }
});

it('extractFilesFromZip mempertahankan path file dalam subfolder', function () use ($make) {
    $zip = makeZip([
        'assets/js/app.js' => 'console.log(1)',
        'index.html'       => '<h1>hi</h1>',
    ]);

    $files = $make()->extract($zip);
    unlink($zip);

    expect($files)->toHaveKey('assets/js/app.js');
    expect($files['assets/js/app.js'])->toBe('console.log(1)');
});

// ---------------------------------------------------------------------------
// stripCommonPrefix
// ---------------------------------------------------------------------------

it('stripCommonPrefix membuang root folder tunggal', function () use ($make) {
    $files = [
        'html-1/index.html' => '<h1>hi</h1>',
        'html-1/style.css'  => 'body{}',
        'html-1/img/a.png'  => 'png',
    ];

    $result = $make()->normalize($files);

    expect($result)->toHaveKey('index.html');
    expect($result)->toHaveKey('style.css');
    expect($result)->toHaveKey('img/a.png');
    expect($result)->not->toHaveKey('html-1/index.html');
});

it('stripCommonPrefix tidak mengubah apapun jika tidak ada prefix umum', function () use ($make) {
    $files = [
        'index.html' => '<h1>hi</h1>',
        'style.css'  => 'body{}',
    ];

    $result = $make()->normalize($files);

    expect($result)->toEqual($files);
});

it('stripCommonPrefix tidak membuang prefix jika file ada yang berbeda root', function () use ($make) {
    $files = [
        'html-1/index.html' => '<h1>hi</h1>',
        'other/style.css'   => 'body{}',
    ];

    $result = $make()->normalize($files);

    expect($result)->toHaveKey('html-1/index.html');
    expect($result)->toHaveKey('other/style.css');
});

it('stripCommonPrefix mengembalikan array kosong jika input kosong', function () use ($make) {
    expect($make()->normalize([]))->toEqual([]);
});

it('extractFilesFromZip + stripCommonPrefix: ZIP dengan root folder menjadi flat', function () use ($make) {
    $zip = makeZip([
        'html-1/index.html' => '<h1>hi</h1>',
        'html-1/style.css'  => 'body{}',
        'html-1/img/a.png'  => 'png',
    ]);

    $extractor = $make();
    $files     = $extractor->normalize($extractor->extract($zip));
    unlink($zip);

    expect($files)->toHaveKey('index.html');
    expect($files)->toHaveKey('style.css');
    expect($files)->toHaveKey('img/a.png');
    expect(array_keys($files))->not->toContain('html-1/index.html');
});

// ---------------------------------------------------------------------------
// mimeType
// ---------------------------------------------------------------------------

it('mimeType mengembalikan mime yang benar untuk ekstensi umum', function () use ($make) {
    $m = $make();

    expect($m->mime('index.html'))->toBe('text/html');
    expect($m->mime('style.css'))->toBe('text/css');
    expect($m->mime('app.js'))->toBe('application/javascript');
    expect($m->mime('data.json'))->toBe('application/json');
    expect($m->mime('feed.xml'))->toBe('application/xml');
    expect($m->mime('logo.png'))->toBe('image/png');
    expect($m->mime('photo.jpg'))->toBe('image/jpeg');
    expect($m->mime('photo.jpeg'))->toBe('image/jpeg');
    expect($m->mime('icon.ico'))->toBe('image/x-icon');
    expect($m->mime('logo.svg'))->toBe('image/svg+xml');
});

it('mimeType mengembalikan text/plain untuk ekstensi tidak dikenal', function () use ($make) {
    expect($make()->mime('file.xyz'))->toBe('text/plain');
});

// ---------------------------------------------------------------------------
// isBinary
// ---------------------------------------------------------------------------

it('isBinary mengembalikan true untuk format binary', function () use ($make) {
    $b = $make();

    foreach (['png', 'jpg', 'jpeg', 'gif', 'ico', 'webp', 'woff', 'woff2', 'ttf', 'eot', 'pdf'] as $ext) {
        expect($b->binary($ext))->toBeTrue("expected {$ext} to be binary");
    }
});

it('isBinary mengembalikan false untuk format teks', function () use ($make) {
    $b = $make();

    foreach (['html', 'css', 'js', 'json', 'xml', 'txt', 'md'] as $ext) {
        expect($b->binary($ext))->toBeFalse("expected {$ext} to not be binary");
    }
});
