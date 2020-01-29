<?php
/**
 * Created by PhpStorm.
 * User: mrlog
 * Date: 20.09.2018
 * Time: 18:35
 */

namespace App\ApiV2\applications\test;


use App\ApiV2\Api;

class TestCase extends Api
{

    /**
     * @param string $api
     * @param array $data
     * @return mixed
     */
    public static function call(string $api, array $data)
    {
        if(!isset($data['test']))
            return self::sendError(['test not found']);

        return self::sendSuccess(['SUPPER' => $data['test']]);
    }
}