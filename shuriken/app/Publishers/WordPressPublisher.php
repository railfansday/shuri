<?php

namespace App\Publishers;

use App\Post;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class WordPressPublisher implements PublisherInterface
{
    private Client $client;
    private string $baseUrl;

    public function __construct(array $credentials)
    {
        $this->baseUrl = rtrim($credentials['url'], '/');

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'auth'     => [$credentials['username'], $credentials['password']],
            'headers'  => ['Content-Type' => 'application/json'],
            'timeout'  => 30,
        ]);
    }

    public function test(): string
    {
        $response = $this->client->get('/wp-json/wp/v2/users/me');
        $user = json_decode($response->getBody(), true);
        return $user['name'] ?? $user['slug'] ?? 'unknown';
    }

    public function publish(Post $post, array $options = []): int
    {
        $status   = $options['status'] ?? 'publish';
        $content  = $post->ai_content ?? $post->content;
        $category = $post->category ?? 'Uncategorized';
        $tags     = $post->tags ?? [];

        $payload = [
            'title'      => $post->title,
            'content'    => $content,
            'slug'       => $post->slug,
            'status'     => $status,
            'date'       => $post->published_at?->toIso8601String(),
            'categories' => [$this->resolveCategory($category)],
            'tags'       => array_map(fn($t) => $this->resolveTag($t), array_filter((array) $tags)),
        ];

        try {
            $response = $this->client->post('/wp-json/wp/v2/posts', ['json' => $payload]);
            $data = json_decode($response->getBody(), true);
            return $data['id'];
        } catch (ClientException $e) {
            $body = json_decode($e->getResponse()->getBody(), true);
            throw new \RuntimeException($body['message'] ?? $e->getMessage());
        }
    }

    private function resolveCategory(string $name): int
    {
        return $this->resolveTermId('categories', $name);
    }

    private function resolveTag(string $name): int
    {
        return $this->resolveTermId('tags', $name);
    }

    private function resolveTermId(string $taxonomy, string $name): int
    {
        $response = $this->client->get("/wp-json/wp/v2/{$taxonomy}", [
            'query' => ['search' => $name, 'per_page' => 1],
        ]);
        $terms = json_decode($response->getBody(), true);

        if (!empty($terms)) {
            return $terms[0]['id'];
        }

        $response = $this->client->post("/wp-json/wp/v2/{$taxonomy}", [
            'json' => ['name' => $name],
        ]);
        $term = json_decode($response->getBody(), true);
        return $term['id'];
    }
}
