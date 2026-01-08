<?php

namespace App\Console\Commands;

use App\Jobs\SendDailySalesReport;
use Illuminate\Console\Command;

class SendDailyReportTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:daily-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        dispatch(new SendDailySalesReport());
        $this->info('Daily sales report job dispatched!');
    }
}
