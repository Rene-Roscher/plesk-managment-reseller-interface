<?php
/**
 * Created by PhpStorm.
 * User: mrlog
 * Date: 07.09.2018
 * Time: 18:34
 */

namespace App\Jobs\Webspace;


use App\Order;
use App\PaymentHandler;
use App\Service;
use App\User;
use App\Webspace;
use App\WebspaceHost;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Exception;

class OrderWebspace implements ShouldQueue
{

    use InteractsWithQueue, Queueable, SerializesModels;

    public $maxTries = 32;

    public $timeout = 120;

    protected $webspace;
    protected $webhost;
    protected $order;
    protected $transaction;
    protected $user;
    protected $service;

    /**
     * OrderWebspace constructor.
     * @param $webspace
     * @param $webhost
     * @param $order
     * @param $transaction
     * @param $user
     * @param $service
     */
    public function __construct(Webspace $webspace, WebspaceHost $webhost, Order $order, PaymentHandler $transaction, User $user, Service $service)
    {
        $this->webspace = $webspace;
        $this->webhost = $webhost;
        $this->order = $order;
        $this->transaction = $transaction;
        $this->user = $user;
        $this->service = $service;
        $this->handle();
    }



    /**
     * @param Webspace $webspace
     */
    public function handle()
    {
        $id = $this->webspace->id + 1000;
        $data = json_decode($this->webspace->configuration);

        if(!$this->user->canOrderWithAmount($this->order->amount)) {
            throw new Exception('The reseller hasnt enough money.');
        }

        $this->webhost->createPlan('web'.$id, $data->disk, $data->site, $data->subdom, $data->mail, $data->db, $data->ftp);

        if(isset($data->domain)) {
            $this->webhost->createWebspace($this->webspace, [
                'id' => $id,
                'plan-name' => 'web'.$id,
                'domain' => $data->domain,
            ]);
        } else {
            $this->webhost->createWebspace($this->webspace, [
                'id' => $id,
                'plan-name' => 'web'.$id,
            ]);
        }

        $this->webhost->update([
            'webspaces' => $this->webhost->webspaces + 1,
        ]);

        $this->webspace->save();

        $this->service->service_id = $this->webspace->id;
        $this->service->save();

        $this->order->state = 'SUCCESS';
        $this->order->save();

        $this->user->money -= $this->order->amount;
        $this->user->save();

        $this->transaction->state = 'SUCCESS';
        $this->transaction->save();
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        // Send notification of failure, etc...
    }

}