<?php

namespace App\Commands;

use App\Spiders\BrainlySpider;
use Illuminate\Console\Scheduling\Schedule;

class BrainlyScraper extends ImageScraper
{
    public $spider = BrainlySpider::class;
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = "brainly:scrape
    {--language=id : The target language}
    {--country=id : The target country}
    {--proxy= : Use proxy to scrape}
    {--keywords_txt : Enable keywords from keyword.txt}
    {--trends= : Enable trending keywords, see https://tools.dojo.cc/tools/trending-keywords for country code}
    {--keywords= : Enable import keywords from cli, separated by comma}
    {--max= : Maximum keywords to be scraped}
    {--execute_javascript : Get more relevant data but slower scraping time}
    {--concurrency= : Scrape in parallel}
    {--requestDelay= : Delay between scraping}
    {--append_query= : Additional query which will be appended each request}
    {--clear_data : Clear database before run}
    {--timeout= : maximum wait time each scraping in seconds}
    ';";

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = "scrape questions and answers from brainly";

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
