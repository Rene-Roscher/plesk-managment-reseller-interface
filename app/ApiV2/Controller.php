<?php
/**
 * Created by PhpStorm.
 * User: mrlog
 * Date: 16.09.2018
 * Time: 19:17
 */

namespace App\ApiV2;


use App\ApiV2\Handler;
use Illuminate\Http\Request;

class Controller
{

    /**
     * @param $endpoint
     * @param Request $request
     * @return mixed
     */
    public function api($endpoint, Request $request)
    {
        $handler = new Handler($endpoint, $request->all());
        try {
            return $handler->handle();
        } catch (\Exception $e) {
            return 'Error: '.$e->getCode().'<br>Message: '.$e->getMessage();
        }
    }

}