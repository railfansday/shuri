<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;

class Omakase extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = "omakase";

    public static array $source = [
        "id" => "Google Trends Indonesia",
        "za" => "Google Trends Afrika Selatan",
        "us" => "Google Trends Amerika Serikat",
        "sa" => "Google Trends Arab Saudi",
        "ar" => "Google Trends Argentina",
        "au" => "Google Trends Australia",
        "at" => "Google Trends Austria",
        "nl" => "Google Trends Belanda",
        "be" => "Google Trends Belgia",
        "br" => "Google Trends Brasil",
        "cl" => "Google Trends Cile",
        "dk" => "Google Trends Denmark",
        "ph" => "Google Trends Filipina",
        "fi" => "Google Trends Finlandia",
        "hu" => "Google Trends Hungaria",
        "in" => "Google Trends India",
        "gb" => "Google Trends Inggris",
        "il" => "Google Trends Israel",
        "it" => "Google Trends Italia",
        "jp" => "Google Trends Jepang",
        "de" => "Google Trends Jerman",
        "ca" => "Google Trends Kanada",
        "ke" => "Google Trends Kenya",
        "co" => "Google Trends Kolombia",
        "kr" => "Google Trends Korea Selatan",
        "my" => "Google Trends Malaysia",
        "mx" => "Google Trends Meksiko",
        "eg" => "Google Trends Mesir",
        "ng" => "Google Trends Nigeria",
        "no" => "Google Trends Norwegia",
        "pl" => "Google Trends Polandia",
        "pt" => "Google Trends Portugal",
        "fr" => "Google Trends Prancis",
        "cz" => "Google Trends Republik Cheska",
        "ro" => "Google Trends Rumania",
        "ru" => "Google Trends Rusia",
        "sg" => "Google Trends Singapura",
        "es" => "Google Trends Spanyol",
        "se" => "Google Trends Swedia",
        "ch" => "Google Trends Swiss",
        "tw" => "Google Trends Taiwan",
        "th" => "Google Trends Thailand",
        "tr" => "Google Trends Turki",
        "ua" => "Google Trends Ukraina",
        "vn" => "Google Trends Vietnam",
        "gr" => "Google Trends Yunani",
    ];

    public array $platforms = [
        "wordpress" => "xml",
        "blogger" => "xml",
        "hugo" => "zip",
        "csv" => "csv",
        "native" => "sqlite",
        "html" => "zip",
    ];

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = "Jalankan shuriken tanpa konfigurasi. Langsung jalan, langsung ekspor, tinggal nyepamnya aja.";

    public static function trend()
    {
        return collect(self::$source)
            ->keys()
            ->random();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->call("image:scrape", [
            "--clear_data" => true,
            "--concurrency" => 100,
        ]);

        $id = str()->random(5);
        foreach ($this->platforms as $platform => $filetype) {
            $this->call("image:export", ["--platform" => $platform]);

            $this->task("Export " . $platform, function () use ($id) {
                foreach (Storage::files("exports") as $file) {
                    $new_file = str($file)
                        ->replace("exports", "omakase")
                        ->replace("/", "/" . date("Y-m-d") . "/")
                        ->replace(".", "-" . $id . ".")
                        ->toString();

                    $this->info("Generating " . $new_file);
                    Storage::move($file, $new_file);
                }
            });
        }
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
