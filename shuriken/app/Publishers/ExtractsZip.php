<?php

namespace App\Publishers;

use PhpZip\ZipFile;

trait ExtractsZip
{
    /**
     * Extract all files from a ZIP as path => content array.
     */
    protected function extractFilesFromZip(string $zipPath): array
    {
        $files = [];
        $zip   = new ZipFile();
        $zip->openFromString(file_get_contents($zipPath));

        foreach ($zip->getEntries() as $entry) {
            if (!$entry->isDirectory()) {
                $files[$entry->getName()] = $zip->getEntryContents($entry->getName());
            }
        }

        $zip->close();

        return $files;
    }

    /**
     * Strip common root folder from paths (e.g. "html-1/index.html" → "index.html").
     */
    protected function stripCommonPrefix(array $files): array
    {
        if (empty($files)) {
            return $files;
        }

        $paths    = array_keys($files);
        $firstSep = strpos($paths[0], '/');

        if ($firstSep === false) {
            return $files;
        }

        $candidate = substr($paths[0], 0, $firstSep + 1);
        $allMatch  = array_reduce(
            $paths,
            fn($carry, $p) => $carry && str_starts_with($p, $candidate),
            true
        );

        if (!$allMatch) {
            return $files;
        }

        $result = [];
        foreach ($files as $path => $content) {
            $result[substr($path, strlen($candidate))] = $content;
        }

        return $result;
    }

    protected function mimeType(string $path): string
    {
        return match (pathinfo($path, PATHINFO_EXTENSION)) {
            'html'        => 'text/html',
            'css'         => 'text/css',
            'js'          => 'application/javascript',
            'xml'         => 'application/xml',
            'json'        => 'application/json',
            'png'         => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'svg'         => 'image/svg+xml',
            'ico'         => 'image/x-icon',
            default       => 'text/plain',
        };
    }

    protected function isBinary(string $ext): bool
    {
        return in_array($ext, [
            'png', 'jpg', 'jpeg', 'gif', 'ico', 'webp',
            'woff', 'woff2', 'ttf', 'eot', 'pdf',
        ]);
    }
}
