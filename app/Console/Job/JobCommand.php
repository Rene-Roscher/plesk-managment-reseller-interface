<?php
/**
 * Created by PhpStorm.
 * User: mrlog
 * Date: 08.09.2018
 * Time: 20:25
 */

namespace App\Console\Job;


use App\Jobs\Webspace\OrderWebspace;
use App\oJobs;
use App\Order;
use App\PaymentHandler;
use App\Service;
use App\User;
use App\Webspace;
use App\WebspaceHost;
use Illuminate\Console\Command;

class JobCommand extends Command
{

    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {

        $jobs = oJobs::all()->where('state', 'PENDING');

        foreach ($jobs as $job) {
            $job->tries += 1;
            $job->save();

            $payload = json_decode($job->payload);

            if($job->queue == 'webspace')
            {
                $this->initWebspaceOrder($payload, $job);
            }

        }

    }

    public function initWebspaceOrder($payload, $job)
    {
        try {
            new OrderWebspace(Webspace::all()->where('id', $payload->webspace)->first(), WebspaceHost::all()->where('id', $payload->webhost)->first(), Order::all()->where('id', $payload->order)->first(), PaymentHandler::all()->where('id', $payload->transaction)->first(), User::all()->where('id', $payload->user)->first(),  Service::all()->where('id', $payload->service)->first());
            $job->state = 'SUCCESS';
        } catch (\Exception $ex) {
            $job->exception = $ex->getMessage();
            $job->state = 'PENDING';
        }
        $job->save();
    }

}