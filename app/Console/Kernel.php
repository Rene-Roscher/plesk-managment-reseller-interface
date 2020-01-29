<?php

namespace App\Console;

use App\Console\Job\JobCommand;
use App\Console\Services\ServiceCommand;
use App\Jobs\Webspace\OrderWebspace;
use App\oJobs;
use App\Order;
use App\PaymentHandler;
use App\Service;
use App\User;
use App\Webspace;
use App\WebspaceHost;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
//        \App\Console\Services\ServiceCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $jobHandler = new JobCommand();
            $jobHandler->handle();
            $serviceHandler = new ServiceCommand();
            $serviceHandler->handle();
        })->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
