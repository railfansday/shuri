<?php

namespace App\Publishers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class CloudflarePagesPublisher implements DeployerInterface
{
    use ExtractsZip;

    private Client $client;
    private string $accountId;
    private string $projectName;

    public function __construct(array $credentials)
    {
        $this->accountId   = $credentials['account_id'];
        $this->projectName = $credentials['project_name'];

        $this->client = new Client([
            'base_uri' => 'https://api.cloudflare.com',
            'headers'  => [
                'Authorization' => 'Bearer ' . $credentials['api_token'],
            ],
            'timeout'  => 120,
        ]);
    }

    public function test(): string
    {
        $response = $this->client->get(
            "/client/v4/accounts/{$this->accountId}/pages/projects/{$this->projectName}"
        );
        $data = json_decode($response->getBody(), true);

        $name   = $data['result']['name'] ?? $this->projectName;
        $domain = $data['result']['domains'][0] ?? "{$this->projectName}.pages.dev";

        return "{$name} | https://{$domain}";
    }

    public function deploy(string $zipPath): string
    {
        if (!file_exists($zipPath)) {
            throw new \RuntimeException("ZIP file not found: {$zipPath}");
        }

        // Extract files from ZIP and compute SHA256 hashes
        $files    = $this->extractFilesFromZip($zipPath);
        $manifest = [];
        foreach ($files as $path => $content) {
            $manifest['/' . ltrim($path, '/')] = hash('sha256', $content);
        }

        // Step 1: Create deployment with manifest
        try {
            $response = $this->client->post(
                "/client/v4/accounts/{$this->accountId}/pages/projects/{$this->projectName}/deployments",
                [
                    'multipart' => [
                        [
                            'name'     => 'manifest',
                            'contents' => json_encode($manifest),
                        ],
                    ],
                ]
            );
        } catch (ClientException $e) {
            $body = json_decode($e->getResponse()->getBody(), true);
            throw new \RuntimeException($body['errors'][0]['message'] ?? $e->getMessage());
        }

        $deployment = json_decode($response->getBody(), true);
        $deployId   = $deployment['result']['id'];

        // Step 2: Upload missing files
        $missing = $deployment['result']['file_upload_required'] ?? array_values($manifest);
        if (!empty($missing)) {
            $uploadBatch = [];
            foreach ($missing as $hash) {
                $path = array_search($hash, $manifest);
                if ($path && isset($files[ltrim($path, '/')])) {
                    $uploadBatch[] = [
                        'key'      => $hash,
                        'value'    => base64_encode($files[ltrim($path, '/')]),
                        'metadata' => ['contentType' => $this->mimeType($path)],
                        'base64'   => true,
                    ];
                }
            }

            if (!empty($uploadBatch)) {
                $this->client->post('/client/v4/pages/assets/upload', [
                    'json' => $uploadBatch,
                ]);
            }
        }

        // Step 3: Finalize
        $this->client->post(
            "/client/v4/accounts/{$this->accountId}/pages/projects/{$this->projectName}/deployments/{$deployId}/finalize"
        );

        $url = $deployment['result']['url'] ?? "https://{$this->projectName}.pages.dev";
        return $url;
    }
}
