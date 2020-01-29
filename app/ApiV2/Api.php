<?php
/**
 * Created by PhpStorm.
 * User: mrlog
 * Date: 14.09.2018
 * Time: 14:34
 */

namespace App\ApiV2;

use App\APILogs;
use Illuminate\Http\Request;

abstract class Api
{

    /**
     * @param string $api
     * @param array $data
     * @return mixed
     */
    abstract public static function call(string $api, array $data);

    /**
     * @param Api $api
     * @param Request $request
     * @return APILogs
     */
    public static function logger(API $api, Request $request, string $state, int $code): APILogs
    {
        $log = new APILogs();
        $log->token_id = $api->id;
        $log->client = '- / -';
        $log->ip = $request->ip();
        $log->uri = \request()->url();
        $log->respocode = 0;
        $log->state = $state;
        $log->respocode = $code;
        $log->save();
        return $log;
    }

    /**
     * @param bool $success
     * @param array $errors
     * @param array $response
     * @param int $code
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    private static function response(bool $success = false, $errors = [], array $response = [], int $code = 500)
    {
        return response(['success' => $success, 'errors' => $errors, 'response' => $response], $code);
    }

    /**
     * @param array $response
     * @param APILogs $log
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public static function sendSuccess($response)
    {
        return self::response(true, [], $response, 200);
    }

    /**
     * @param array $errors
     * @param APILogs $log
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public static function sendError($errors)
    {
        return self::response(false, $errors, [], 500);
    }


}