<?php
namespace App\Spiders;

use RoachPHP\Downloader\DownloaderMiddlewareInterface;
use RoachPHP\Downloader\Middleware\RequestMiddlewareInterface;
use RoachPHP\Http\Request;
use RoachPHP\Http\Response;
use RoachPHP\Support\Configurable;

class RequestLimiterMiddleware implements DownloaderMiddlewareInterface
{
    use Configurable;

    public function handleRequest(Request $request): Request
    {
        return $this->limit($request);
    }

    public function handleResponse(Response $response): Response
    {
        return $this->limit($response);
    }

    private function limit(Response|Request $r)
    {
        $last_id = cache("keywords_count");
        $max = config("shuriken.max");

        if ($last_id >= $max) {
            return $r->drop("Maximum $max content reached");
        }
        return $r;
    }
}
