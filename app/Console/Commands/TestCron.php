<?php

namespace App\Console\Commands;

use App\Storehouse;
use Illuminate\Console\Command;

class TestCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:crone';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'search not pay entries';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Storehouse::create(['name' => 'TEST_STORE']);
        return 'OGFFF';
    }
}
