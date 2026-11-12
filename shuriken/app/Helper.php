<?php
namespace App;

use GuzzleHttp\Client;
use League\HTMLToMarkdown\HtmlConverter;
use RoachPHP\Http\Request;
use RoachPHP\Http\Response;

class Helper
{
    public static function markdownify($html)
    {
        $converter = new HtmlConverter();

        return $converter->convert($html);
    }
    public static function getResponse($url): Response
    {
        $client = new Client();
        $request = new Request("GET", $url, static fn() => yield from []);

        return new Response($client->send($request->getPsrRequest()), $request);
    }

    public static function mergeConfig(string $string, $options)
    {
        $config = config($string);
        $options = array_filter($options);
        config([$string => array_merge($config, $options)]);
    }

    public static function spintax($text)
    {
        return preg_replace_callback(
            "/\{(((?>[^\{\}]+)|(?R))*)\}/x",
            [Helper::class, "spintaxReplace"],
            $text
        );
    }

    public static function spintaxReplace($text)
    {
        $text = Helper::spintax($text[1]);
        $parts = explode("|", $text);
        return $parts[array_rand($parts)];
    }
}
