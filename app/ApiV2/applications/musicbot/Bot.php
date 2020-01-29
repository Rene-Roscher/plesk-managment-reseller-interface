<?php
/**
 * Created by PhpStorm.
 * User: mrlog
 * Date: 20.09.2018
 * Time: 15:54
 */

namespace App\ApiV2\applications\musicbot;


use App\ApiV2\Api;

class Bot extends Api
{

    /**
     * @param string $api
     * @param array $data
     * @return mixed
     */
    public static function call(string $api, array $data)
    {
        return $data['user']->name;
    }
}