<?php
return [
    "platform" => "wordpress",
    "category" => "Uncategorized",
    "start" => \Carbon\Carbon::now()->subYear(),
    "end" => \Carbon\Carbon::now(),
    "chunk" => 50000,
    "without_featured_image" => false,
    "theme" => "default",
    "site_name" => "",
    "description" => "",
    "clear_post" => false,
];
