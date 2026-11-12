<?php

namespace App\Publishers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class VercelPublisher implements DeployerInterface
{
    use ExtractsZip;

    private Client $client;
    private string $projectName;
    private ?string $teamId;

    public function __construct(array $credentials)
    {
        $this->projectName = $credentials['project_name'];
        $this->teamId      = $credentials['team_id'] ?? null;

        $this->client = new Client([
            'base_uri' => 'https://api.vercel.com',
            'headers'  => [
                'Authorization' => 'Bearer ' . $credentials['api_token'],
                'Content-Type'  => 'application/json',
            ],
            'timeout'  => 120,
        ]);
    }

    public function test(): string
    {
        $query = $this->teamId ? "?teamId={$this->teamId}" : '';

        $response = $this->client->get("/v9/projects/{$this->projectName}{$query}");
        $data = json_decode($response->getBody(), true);

        $name      = $data['name'] ?? $this->projectName;
        $accountId = $data['accountId'] ?? '';

        return "{$name} ({$accountId})";
    }

    public function deploy(string $zipPath): string
    {
        if (!file_exists($zipPath)) {
            throw new \RuntimeException("ZIP file not found: {$zipPath}");
        }

        $files   = $this->extractFilesFromZip($zipPath);
        $payload = [];

        foreach ($files as $path => $content) {
            $ext      = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $isBinary = $this->isBinary($ext);

            $payload[] = [
                'file'     => $path,
                'data'     => $isBinary ? base64_encode($content) : $content,
                'encoding' => $isBinary ? 'base64' : 'utf-8',
            ];
        }

        $query = $this->teamId ? "?teamId={$this->teamId}" : '';

        try {
            $response = $this->client->post("/v13/deployments{$query}", [
                'json' => [
                    'name'   => $this->projectName,
                    'target' => 'production',
                    'files'  => $payload,
                ],
            ]);
        } catch (ClientException $e) {
            $body = json_decode($e->getResponse()->getBody(), true);
            throw new \RuntimeException($body['error']['message'] ?? $e->getMessage());
        }

        $data = json_decode($response->getBody(), true);
        $url  = $data['url'] ?? "{$this->projectName}.vercel.app";

        return "https://{$url}";
    }
}
