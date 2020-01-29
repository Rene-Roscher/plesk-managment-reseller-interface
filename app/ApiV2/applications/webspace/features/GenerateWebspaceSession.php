<?php
/**
 * Created by PhpStorm.
 * User: mrlog
 * Date: 18.09.2018
 * Time: 17:47
 */

namespace App\ApiV2\applications\webspace\features;


use App\ApiV2\Api;
use App\Webspace;

class GenerateWebspaceSession extends Api
{

    /**
     * @param string $api
     * @param array $data
     * @return mixed
     */
    public static function call(string $api, array $data)
    {
        if(!isset($data['ip']))
            return self::sendError(['ip is not defined.']);

        $webspace = Webspace::all()->where('user_id', $data['user']->id)->where('id', $data['webspace_id'])->first();

        if($webspace)
            return self::sendSuccess(['url' => self::generateSession($webspace, $data['ip'])]);

        return self::sendError(['webspace not found.']);
    }

    static function generateSession(Webspace $webspace, $ip) : string
    {
        return 'https://'.$webspace->webhost->ip_address.':8443/enterprise/rsession_init.php?PLESKSESSID='.$webspace->webhost->api()->server()->createSession($webspace->plesk_username, $ip);
    }

}