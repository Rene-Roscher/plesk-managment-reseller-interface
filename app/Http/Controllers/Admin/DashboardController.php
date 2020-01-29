<?php
/**
 * Created by PhpStorm.
 * User: mrlog
 * Date: 11.08.2018
 * Time: 14:22
 */

namespace App\Http\Controllers\Admin;

use App\ApiV2\Handler;
use App\Providers\Ph24\Manager\Payment;
use App\Providers\Ph24\Ph24;
use App\Providers\Ph24\Ph24Facade;
use App\User;
use App\Webspace;
use App\WebspaceHost;
//use Barryvdh\DomPDF\PDF;
//use Dompdf\Canvas;
//use Dompdf\CanvasFactory;
//use Dompdf\Dompdf;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{

    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {

//        $handler = new Handler('payment', ['authToken' => '8b1E-qCvc-teIa-esuP-2a49-ywb6-poEa-oqdz-8N4E-LKTZ-Ls74-zmeI']);
//        return $handler->handle(false);

//        $data = [
//            'api_key' => 'a73d5e7e',
//            'api_secret' => 'AfwCcGWWC5LJRGyZ',
//            'to' => '4915775267786',
//            'from' => 'DeinCloudServer',
//            'text' => 'Wie cool man'
//        ];
//
//        $client = Ph24Facade::client();
//
//        $ph24 = new Ph24($client);
//        return $ph24->post($data, '');

//        $url = 'https://rest.nexmo.com/sms/json?api_key=a73d5e7e&api_secret=AfwCcGWWC5LJRGyZ&to=4915775267786&from=DeinCloudServer&text=Lol Alda';
//        $apiToken = '';
//
//        $paymentData = [
//            'api_key' => 'a73d5e7e',
//            'api_secret' => 'AfwCcGWWC5LJRGyZ',
//            'to' => '4915775267786',
//            'from' => 'DeinCloudServer',
//            'text' => 'Wie cool man'
//        ];
//
//        $headers = [
//            'Accept: application/json',
//            'Authorization: Bearer ' . $apiToken
//        ];
//
//        $curl = curl_init();
//        curl_setopt($curl, CURLOPT_URL, $url);
//        curl_setopt($curl, CURLOPT_POST, true);
//        curl_setopt($curl, CURLOPT_POSTFIELDS, $paymentData);
//        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
//        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//
//        $response = curl_exec($curl);
//
//        curl_close($curl);
//
//        $response = json_decode($response);

//        return json_encode($response);

//        curl -X POST  https://rest.nexmo.com/sms/json \
//-d api_key=a73d5e7e \
//    -d api_secret=AfwCcGWWC5LJRGyZ \
//    -d to=4915775267786 \
//    -d from="NEXMO" \
//    -d text="Hello from Nexmo"


//        $reflectionMethod = new \ReflectionMethod(HelloWorld::class, 'sayHelloTo');
//        echo $reflectionMethod->invokeArgs(new HelloWorld(), array('Mike'));

//        $p = \App\ApiV2\applications\payment\Payment::class;
//        $ref = new \ReflectionMethod(new $p, 'call');
//        return $ref->invokeArgs(NULL, [6, []]);

//        $h = new Handler('asdfphioasf', ['TOKEN', ['start' => 1]]);
//        try {
//            return $h->handle(false);
//        } catch (\Exception $e) {
//
//        }

//        $ph24 = Ph24Facade::client();
//        $payment = new Payment($ph24);
//        $response = $payment->check(46036);
//
//        return $response;

//        $params = [
//            'service-plan' => [
//                'add' => [
//                    'name' => 'rene',
//                    'limits' => [
//                        'overuse' => 'block',
//                        '1limit' => [
//                            'name' => 'disk_space',
//                            'value' => 10000
//                        ],
//                        '2limit' => [
//                            'name' => 'max_traffic',
//                            'value' => 10000
//                        ],
//                        '3limit' => [
//                            'name' => 'max_site',
//                            'value' => 5
//                        ],
//                        '4limit' => [
//                            'name' => 'max_subdom',
//                            'value' => 5
//                        ],
//                        '5limit' => [
//                            'name' => 'max_box',
//                            'value' => 5
//                        ],
//                        '6limit' => [
//                            'name' => 'max_db',
//                            'value' => 5
//                        ],
//                        '7limit' => [
//                            'name' => 'max_subftp_users',
//                            'value' => 5
//                        ]
//                    ],
//                    'performance' => [
//                        'bandwidth' => 1000,
//                        'max_connections' => 20
//                    ]
//                ]
//            ]
//        ];

//        $xml = ArrayToXML::init($params, new \SimpleXMLElement('<packet version="1.6.0.0"></packet>'))->asXML();

//        $xml = $this->to_xml(new \SimpleXMLElement('<packet version="1.6.0.0"></packet>'), $params);

//        $xml = $this->array2xml($params, new \SimpleXMLElement('<packet version="1.6.0.0"></packet>'), 'test')->asXML();

//        $xml = ArrayToXML::init($params, new \SimpleXMLElement('<packet version="1.6.0.0"></packet>'))->asXML();

//        return simplexml_load_string($xml);

//        throw new \Exception('Lol');

//        return $this->array2XML($params, 'root');

        return view('admin.dashboard');

//        $webspace = new Webspace();
////        try {
//        try {
//            $webhost = WebspaceHost::getFreeHost();
//        } catch (\ErrorException $e) {
//        }
//        $webspace->user()->associate(Auth::user());
//        $webspace->webhost()->associate($webhost);
//        $webspace->webhost_id = $webhost->id;
////        $webspace->save();
//
//        $webhost->createWebspace($webspace, [
//            'id' => $webspace->id + 1000,
//            'plan-name' => 'Webspace Basic',
//        ]);
//
//        $webhost->update([
//            'webspaces' => $webhost->webspaces + 1,
//        ]);
//        $webspace->save();
//        } catch (\ErrorException $e) {
//            echo 'fehler...';
//        }

        /*
         * PDF -> WICHTIG
         */

//        $pdf = PDF::loadView(view('pdf.invoice'), ['test' => 0]);
//        return $pdf->download('rn-2178.pdf');
//        return view('pdf.invoice');
    }

}


class HelloWorld {

    public static function sayHelloTo($name) {
        return 'Hello ' . $name;
    }

}