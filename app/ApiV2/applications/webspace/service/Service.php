<?php
/**
 * Created by PhpStorm.
 * User: mrlog
 * Date: 19.09.2018
 * Time: 22:09
 */

namespace App\ApiV2\applications\webspace\service;


use App\ApiV2\Api;
use App\ApiV2\applications\webspace\service\gateways\Extend;
use App\Webspace;
use Illuminate\Support\Facades\Validator;

class Service extends Api
{

    /**
     * @param string $api
     * @param array $data
     * @return mixed
     */
    public static function call(string $api, array $data)
    {

        $validator = Validator::make($data, [
            'webspace_id' => 'required',
            'type' => 'required|in:extend,reconfigure',
            'days' => 'required|integer',
        ]);
        if($validator->fails())
            return self::sendError(array($validator->errors()));

        $service = \App\Webspace::all()->where('user_id', $data['user']->id)->where('id', $data['webspace_id'])->first()->service;

        switch ($data['type'])
        {
            case 'extend':
                $extend = new Extend($data['user'], $service, $data['days']);
                return $extend->extend();
        }
    }
}