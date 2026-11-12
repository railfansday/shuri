<?php

namespace App\Publishers;

use GuzzleHttp\Client;

class SurgePublisher implements DeployerInterface
{
    use ExtractsZip;

    private Client $client;
    private string $domain;

    public function __construct(array $credentials)
    {
        $this->domain = rtrim($credentials['domain'], '/');

        $this->client = new Client([
            'base_uri' => 'https://surge.sh',
            'headers'  => [
                'Authorization' => 'token ' . $credentials['token'],
            ],
            'timeout'  => 120,
        ]);
    }

    public function test(): string
    {
        $response = $this->client->get("/projects/{$this->domain}");
        $data     = json_decode($response->getBody(), true);

        $domain = $data['domain'] ?? $this->domain;
        $plan   = $data['plan']   ?? 'free';

        return "{$domain} [{$plan}]";
    }

    public function deploy(string $zipPath): string
    {
        if (!file_exists($zipPath)) {
            throw new \RuntimeException("ZIP file not found: {$zipPath}");
        }

        $tmpDir  = sys_get_temp_dir() . '/surge-' . uniqid();
        $tarPath = sys_get_temp_dir() . '/surge-' . uniqid() . '.tar';

        try {
            $files = $this->stripCommonPrefix($this->extractFilesFromZip($zipPath));

            foreach ($files as $path => $content) {
                $dest = $tmpDir . '/' . $path;
                if (!is_dir(dirname($dest))) {
                    mkdir(dirname($dest), 0755, true);
                }
                file_put_contents($dest, $content);
            }

            $phar = new \PharData($tarPath);
            $phar->buildFromDirectory($tmpDir);

            $this->client->put("/projects/{$this->domain}", [
                'body'    => fopen($tarPath, 'r'),
                'headers' => ['Content-Type' => 'application/x-tar'],
            ]);
        } finally {
            $this->deleteDir($tmpDir);
            if (file_exists($tarPath)) {
                unlink($tarPath);
            }
        }

        return "https://{$this->domain}";
    }

    private function deleteDir(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        foreach (scandir($dir) as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = $dir . '/' . $item;
            is_dir($path) ? $this->deleteDir($path) : unlink($path);
        }
        rmdir($dir);
    }
}
