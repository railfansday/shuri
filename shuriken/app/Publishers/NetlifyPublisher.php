<?php

namespace App\Publishers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class NetlifyPublisher implements DeployerInterface
{
    private Client $client;
    private string $siteId;

    public function __construct(array $credentials)
    {
        $this->siteId = $credentials['site_id'];

        $this->client = new Client([
            'base_uri' => 'https://api.netlify.com',
            'headers'  => [
                'Authorization' => 'Bearer ' . $credentials['api_token'],
            ],
            'timeout'  => 120,
        ]);
    }

    public function test(): string
    {
        $response = $this->client->get("/api/v1/sites/{$this->siteId}");
        $data     = json_decode($response->getBody(), true);

        $name = $data['name'] ?? $this->siteId;
        $url  = $data['ssl_url'] ?? $data['url'] ?? '';

        return "{$name} | {$url}";
    }

    public function deploy(string $zipPath): string
    {
        if (!file_exists($zipPath)) {
            throw new \RuntimeException("ZIP file not found: {$zipPath}");
        }

        try {
            $response = $this->client->post("/api/v1/sites/{$this->siteId}/deploys", [
                'headers' => ['Content-Type' => 'application/zip'],
                'body'    => fopen($zipPath, 'r'),
            ]);

            $data = json_decode($response->getBody(), true);

            return $data['deploy_ssl_url'] ?? $data['ssl_url'] ?? $data['url'] ?? '';
        } catch (ClientException $e) {
            $body = json_decode($e->getResponse()->getBody(), true);
            throw new \RuntimeException($body['message'] ?? $e->getMessage());
        }
    }
}
