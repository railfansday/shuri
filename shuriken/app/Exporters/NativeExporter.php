<?php

namespace App\Exporters;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use PhpZip\ZipFile;

class NativeExporter
{
    public function export(array|Collection $posts, int $index)
    {
        Storage::copy(
            "database/database.sqlite",
            "exports/" . str()->random(5) . ".sqlite"
        );
    }
}
