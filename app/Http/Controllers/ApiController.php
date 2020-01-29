<?php

namespace App\Http\Controllers;

use App\API;
use App\API\Payment\PaymentAPI;
use App\API\Payment\Transactions;
use App\APILogs;
use App\APIOptions;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{

    public function index($token, $methode, Request $request)
    {
        if ($token == null) {
            return response(['success' => false, 'errors' => array('The token was not found.')], 500);
        }
        if ($methode == null) {
            return response(['success' => false, 'errors' => array('The methode was not found.')], 500);
        }
        $api = API::all()->where('token', $token)->first();
        $options = APIOptions::all()->where('name', $methode)->first();

        if (!$api)
            return response(['success' => false, 'errors' => array('The token was not found.')], 500);

        $ip = '';

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        $log = $this->initLog($api, $ip, $methode, $token, $request);

        if (!$options) {
            return $this->sendError(array('The method was not found.'), $log);
        }

        $whitelisted = false;

        foreach ($api->whitelist() as $item) {
            if($item->address == $ip) {
                $whitelisted = true;
                break;
            }
        }

        if($whitelisted == false)
            return $this->sendError(array('Your Address is not Whitelisted.'), $log);

        if ($api->user->state != 'ACTIVATED') {
            return $this->sendError(array('The user is temporarily locked.'), $log);
        }

        if ($options->state == 'INACCESSIBLE') {
            return $this->sendError(array('The method is inaccessible.'), $log);
        }

        if ($methode === 'Payment') {
            if ($api->hasOption($options->id)) {
                $validator = Validator::make($request->all(),
                    ['type' => 'required|in:PAYPAL,PAYSAFECARD', 'amount' => 'required|numeric|min:0.01', 'description' => 'required', 'okurl' => 'required', 'nokurl' => 'required', 'notifyurl' => 'required']);
                if ($validator->fails()) {
                    return $this->sendError(array($validator->errors()), $log);
                }
                $this->setLog('ACCEPTED', 200, $log);
                $response = new PaymentAPI($request->type, $request->amount, $api->user->id, $request->description, $request->okurl, $request->nokurl, $request->notifyurl);
                return response(['success' => true, 'errors' => [], 'response' => $response->create()], 200);
            }
            return $this->sendError(array('Permission denied for selected methode.'), $log);
        } else if ($methode === 'Transactions') {
            if ($api->hasOption($options->id)) {
                $this->setLog('ACCEPTED', 200, $log);
                $response = new Transactions($token);
                if ($request->mtid)
                    return $response->callSingle($request->mtid);
                return $response->call();
            } else {
                $this->setLog('ERROR', 500, $log);
                return $this->sendError(array('Permission denied for selected methode.'), $log);
            }
        } else if ($methode === 'OrderWebspace') {
            if ($api->hasOption($options->id)) {
                $this->setLog('ACCEPTED', 200, $log);
                $response = new API\Webspace\Order\OrderWebspaceAPI($request->all(), User::all()->where('id', $api->user_id)->first());
                if ($request->id) {
                    return $response->callSingle($request->id);
                }
                $validator = Validator::make($request->all(),
                    [
                        'disk' => 'required|integer',
                        'site' => 'required|integer',
                        'subdom' => 'required|integer',
                        'mail' => 'required|integer',
                        'db' => 'required|integer',
                        'ftp' => 'required|integer',
                        'runtime' => 'required',
                        'domain' => 'required',
                    ]);
                if($validator->fails()) {
                    return $this->sendError(array($validator->errors()), $log);
                }
                return $response->call();
            } else {
                $this->setLog('ERROR', 500, $log);
                return $this->sendError(array('Permission denied for selected methode.'), $log);
            }
        } else if ($methode === 'ServiceRenew') {
            if ($api->hasOption($options->id)) {
                $this->setLog('ACCEPTED', 200, $log);
                $response = new API\Service\RenewServiceAPI($request->all(), User::all()->where('id', $api->user_id)->first());
                $validator = Validator::make($request->all(),
                    [
                        'service_id' => 'required|integer|exists:services,id',
                        'runtime' => 'required|integer|min:1',
                    ]);
                if($validator->fails()) {
                    return $this->sendError(array($validator->errors()), $log);
                }
                return $response->call();
            } else {
                $this->setLog('ERROR', 500, $log);
                return $this->sendError(array('Permission denied for selected methode.'), $log);
            }
        }

        return $this->sendError(array('No methode select.'), $log);
    }

    public function initLog(API $api, string $ip, string $methode, $token, Request $request): APILogs
    {
        $log = new APILogs();
        $log->token_id = $api->id;
        $log->client = ' lol ';
        $log->ip = $ip;
        $log->uri = str_replace('{methode}', $methode, str_replace('{token}', $token, $request->route()->uri()));
        $log->respocode = 0;
        $log->save();
        return $log;
    }

    public function sendError(array $errors, APILogs $log)
    {
        $this->setLog('ACCEPTED', 200, $log);
        return $this->response(false, $errors, [], 500);
    }

    public function setLog(string $state, int $code, APILogs $log)
    {
        $log->state = $state;
        $log->respocode = $code;
        $log->save();
    }

    public function response(bool $success = false, array $errors = null, array $response = [], int $code = 500)
    {
        return response(['success' => false, 'errors' => $errors, 'response' => $response], 500);
    }

    public function sendSuccess(array $response, APILogs $log)
    {
        $this->setLog('ERROR', 500, $log);
        return $this->response(true, [], $response, 200);
    }

}