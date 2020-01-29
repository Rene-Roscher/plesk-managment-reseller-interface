<?php
/**
 * Created by PhpStorm.
 * User: mrlog
 * Date: 14.09.2018
 * Time: 14:30
 */

namespace App\ApiV2;


use App\APIOptions;
use App\ApiV2\applications\musicbot\Bot;
use App\ApiV2\applications\payment\Payment;
use App\ApiV2\applications\test\TestCase;
use App\ApiV2\applications\webspace\features\GenerateWebspaceSession;
use App\ApiV2\applications\webspace\order\OrderWebspace;
use App\ApiV2\applications\webspace\service\Service;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Validator;

class Handler extends Controller
{

    public $api;
    public $app;
    public $data;
    public $user;
    public $authToken;
    public $option;

    public function __construct(string $app, array $data)
    {
        $this->api = [
            'payment' => new Payment(), /* Payment create : check */
            'orderwebspace' => new OrderWebspace(), /* Webspace : order */
            'generatewebspacesession' => new GenerateWebspaceSession(), /* Webspace : generate session key */
            'webspaceservice' => new Service(), /* Webspace : service (renew, reconfigure) */
            'musicbot' => new Bot(),
            'testcase' => new TestCase(),
        ];
        $data += ['app' => $app];
        $validator = Validator::make($data, [
            'app' => 'required|exists:api_options,name',
            'authToken' => 'required|exists:api,token'
        ]);
        if($validator->fails())
            abort(400);

        $this->app = $app;
        $this->data = $data;
        $this->authToken = \App\API::all()->where('token', $data['authToken'])->first();
        $this->user = User::all()->where('id', $this->authToken->user_id)->first();
        $this->data += ['user' => $this->user];
        $this->data += ['authToken' => $this->authToken];
        $this->option = APIOptions::all()->where('name', $this->app)->first();
    }

    /**
     * @param bool $single
     * @return mixed
     * @throws \Exception
     */
    public function handle()
    {
        if($this->authToken == null)
            abort(401);
        if(!$this->option)
            abort(404);
        if(!$this->getApi()->hasOption($this->option->id))
            abort(403);
        if(!$this->getApi()->isWhitelisted(request()->ip()))
            abort(410);
        if($this->option->state != 'ACCESSIBLE')
            abort(400);
        try {
            foreach ($this->api as $item => $value)
            {
                if ($this->app == $item)
                {
                    try { $ref = new \ReflectionMethod(new $value,'call'); } catch (\ReflectionException $e) {}
                    return $ref->invokeArgs(NULL, ['api' => $this->authToken, 'data' => $this->data]);
                }
            }
        } catch (\Exception $exception){
        }

    }

    public function getApi() : \App\API
    {
        return $this->authToken;
    }

}