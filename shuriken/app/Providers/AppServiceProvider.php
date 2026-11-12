<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $tokensPath = base_path('storage/tokens.json');
        if (file_exists($tokensPath)) {
            $tokens = json_decode(file_get_contents($tokensPath), true) ?? [];
            foreach ($tokens as $provider => $list) {
                $key = "ai.{$provider}_tokens";
                $existing = config($key, []);
                config([$key => array_merge($list, $existing)]);
            }
        }

        $customProvidersPath = base_path('storage/custom_providers.json');
        if (file_exists($customProvidersPath)) {
            $custom = json_decode(file_get_contents($customProvidersPath), true) ?? [];
            config(['ai.custom_providers' => $custom]);
        }

        $promptsPath = base_path('storage/prompts.json');
        if (file_exists($promptsPath)) {
            $custom = json_decode(file_get_contents($promptsPath), true) ?? [];
            foreach ($custom as $name => $template) {
                config(["prompts.{$name}" => $template]);
            }
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
