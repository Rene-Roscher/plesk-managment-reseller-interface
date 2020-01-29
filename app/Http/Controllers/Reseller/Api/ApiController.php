<?php

namespace App\Http\Controllers\Reseller\Api;

use App\API;
use App\APIWhitelist;
use App\PaymentHandler;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $apis = API::all()->where('user_id', Auth::user()->id);
        return view('reseller.api.index', compact('apis'));
    }

    public function show(API $api)
    {
        if(!$this->own($api))
            return abort(404);
        return view('reseller.api.single.index', compact('api'));
    }

    public function logs(API $api)
    {
        if(!$this->own($api))
            return abort(404);
        return view('reseller.api.single.logs.index', compact('api'));
    }

    public function whitelist(API $api)
    {
        if(!$this->own($api))
            return abort(404);
        return view('reseller.api.single.whitelist.index', compact('api'));
    }

    public function addAddress(API $api, Request $request)
    {
        if(!$this->own($api))
            return abort(404);

        $validator = Validator::make($request->all(),
            [
                'address' => 'required',
            ]);

        $validator->setAttributeNames(
            [
                'address' => 'IP',
            ]);

        if ($validator->fails())
            return back()->withErrors($validator->errors())->withInput($request->all());

        $whitelist = new APIWhitelist();
        $whitelist->token_id = $api->id;
        $whitelist->address = $request->address;
        $whitelist->save();

        return back()->withSuccess('Die IP wurde erfolgreich zu Whitelist des API-Tokens hinzugefügt.');
    }

    public function removeAddress(API $api, APIWhitelist $whitelist)
    {
        $whitelist->delete();
        return back()->withSuccess('Die IP wurde erfolgreich entfernt');
    }

    public function refresh(API $api)
    {

        $key = str_random(48);
        $chars = [];
        $strLength = strlen($key);
        for ($i = 0; $i < $strLength; $i++) {
            $resultArr[$i] = $key[$i];
        }

        $token = '';
        for ($x = 0; $x <= 38+5+5+5+5; $x++) {
            $value = str_random(1);
            if ($x == 4 || $x == 9 || $x == 14 || $x == 19 || $x == 24 || $x == 29 || $x == 34 || $x == 34+5 || $x == 34+5+5 || $x == 34+5+5+5 || $x == 34+5+5+5+5 || $x == 34+5+5+5+5+5) {
                $token .= '-';
            } else {
                $token .= $value;
            }
        }

        $api->update(['token' => $token]);

        return back()->withSuccess('Der Token wurde erfolgreich neu generiert.');
    }

}
