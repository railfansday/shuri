<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;

class ClearData extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = "clear:data";

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = "Clear images, keywords, posts and exports data";

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->task("clearing database", function () {
            file_put_contents(base_path("database/database.sqlite"), "");

            Artisan::call("migrate:refresh");
        });
        $this->task("clean up exports directory", function () {
            File::cleanDirectory("exports");
        });
        $this->task("clean up omakase directory", function () {
            File::cleanDirectory("omakase");
        });
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
