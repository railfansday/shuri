<?php

namespace App\Publishers;

use App\Post;

interface PublisherInterface
{
    public function test(): string;
    public function publish(Post $post, array $options = []): int;
}
