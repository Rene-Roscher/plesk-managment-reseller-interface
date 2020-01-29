<?php
/**
 * Created by PhpStorm.
 * User: mrlog
 * Date: 09.09.2018
 * Time: 18:52
 */

namespace App\Providers\Ph24\Manager;


use App\Providers\Ph24\Ph24;

class Payment
{

    /**
     * @var Ph24
     */
    private $ph24;

    /**
     * Payment constructor.
     * @param Ph24 $ph24
     */
    public function __construct(Ph24 $ph24)
    {
        $this->ph24 = $ph24;
    }

    /**
     * @param string $type
     * @param int $amount
     * @param $description
     * @param $ok_url
     * @param $nok_url
     * @param $notify_url
     */
    public function create($type = 'PAYPAL', $amount = 5, $description, $ok_url, $nok_url, $notify_url)
    {
        return $this->ph24->get([
            'type' => $type,
            'amount' => $amount,
            'description' => $description,
            'okurl' => $ok_url,
            'nokurl' => $nok_url,
            'notifyurl' => $notify_url
        ], 'Payment');
    }

    public function check($mtid) : bool
    {
        $repsonse = $this->ph24->get([
            'mtid' => $mtid
        ], 'Transactions');

        return $repsonse->response->state == 'SUCCESS';
    }

}