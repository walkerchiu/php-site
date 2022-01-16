<?php

namespace WalkerChiu\Site\Console\Commands;

use WalkerChiu\Core\Console\Commands\Cleaner;

class SiteCleaner extends Cleaner
{
    /**
     * The name and signature of the console command.
     *
     * @var String
     */
    protected $signature = 'command:SiteCleaner';

    /**
     * The console command description.
     *
     * @var String
     */
    protected $description = 'Truncate tables';

    /**
     * Execute the console command.
     *
     * @return Mixed
     */
    public function handle()
    {
        parent::clean('site');
    }
}
