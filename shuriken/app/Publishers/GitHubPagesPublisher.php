<?php

namespace App\Publishers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class GitHubPagesPublisher implements DeployerInterface
{
    use ExtractsZip;

    private Client $client;
    private string $owner;
    private string $repo;
    private string $branch;

    public function __construct(array $credentials)
    {
        $this->owner  = $credentials['owner'];
        $this->repo   = $credentials['repo'];
        $this->branch = $credentials['branch'] ?? 'gh-pages';

        $this->client = new Client([
            'base_uri' => 'https://api.github.com',
            'headers'  => [
                'Authorization'        => 'Bearer ' . $credentials['token'],
                'Accept'               => 'application/vnd.github+json',
                'X-GitHub-Api-Version' => '2022-11-28',
                'User-Agent'           => 'shuriken-deploy/1.0',
            ],
            'timeout'  => 120,
        ]);
    }

    public function test(): string
    {
        $response = $this->client->get("/repos/{$this->owner}/{$this->repo}");
        $data     = json_decode($response->getBody(), true);

        $fullName = $data['full_name'] ?? "{$this->owner}/{$this->repo}";

        return "{$fullName} → {$this->siteUrl()}";
    }

    public function deploy(string $zipPath): string
    {
        if (!file_exists($zipPath)) {
            throw new \RuntimeException("ZIP file not found: {$zipPath}");
        }

        $files = $this->stripCommonPrefix($this->extractFilesFromZip($zipPath));

        // Prevent Jekyll from processing the site
        $files['.nojekyll'] = '';

        $parentSha = $this->getBranchSha();

        $treeItems = [];
        foreach ($files as $path => $content) {
            $treeItems[] = [
                'path' => $path,
                'mode' => '100644',
                'type' => 'blob',
                'sha'  => $this->createBlob($content),
            ];
        }

        $treeSha   = $this->createTree($treeItems);
        $commitSha = $this->createCommit($treeSha, $parentSha);
        $this->updateRef($commitSha, $parentSha === null);

        return $this->siteUrl();
    }

    // -------------------------------------------------------------------------
    // GitHub Git API helpers
    // -------------------------------------------------------------------------

    private function getBranchSha(): ?string
    {
        try {
            $response = $this->client->get(
                "/repos/{$this->owner}/{$this->repo}/git/ref/heads/{$this->branch}"
            );
            return json_decode($response->getBody(), true)['object']['sha'] ?? null;
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                return null;
            }
            throw $e;
        }
    }

    private function createBlob(string $content): string
    {
        $response = $this->client->post(
            "/repos/{$this->owner}/{$this->repo}/git/blobs",
            ['json' => ['content' => base64_encode($content), 'encoding' => 'base64']]
        );

        return json_decode($response->getBody(), true)['sha'];
    }

    private function createTree(array $treeItems): string
    {
        $response = $this->client->post(
            "/repos/{$this->owner}/{$this->repo}/git/trees",
            ['json' => ['tree' => $treeItems]]
        );

        return json_decode($response->getBody(), true)['sha'];
    }

    private function createCommit(string $treeSha, ?string $parentSha): string
    {
        $payload = ['message' => 'Deploy via Shuriken', 'tree' => $treeSha];

        if ($parentSha !== null) {
            $payload['parents'] = [$parentSha];
        }

        $response = $this->client->post(
            "/repos/{$this->owner}/{$this->repo}/git/commits",
            ['json' => $payload]
        );

        return json_decode($response->getBody(), true)['sha'];
    }

    private function updateRef(string $commitSha, bool $create): void
    {
        if ($create) {
            $this->client->post(
                "/repos/{$this->owner}/{$this->repo}/git/refs",
                ['json' => ['ref' => "refs/heads/{$this->branch}", 'sha' => $commitSha]]
            );
        } else {
            $this->client->patch(
                "/repos/{$this->owner}/{$this->repo}/git/refs/heads/{$this->branch}",
                ['json' => ['sha' => $commitSha, 'force' => true]]
            );
        }
    }

    private function siteUrl(): string
    {
        if ($this->repo === "{$this->owner}.github.io") {
            return "https://{$this->owner}.github.io";
        }

        return "https://{$this->owner}.github.io/{$this->repo}";
    }
}
