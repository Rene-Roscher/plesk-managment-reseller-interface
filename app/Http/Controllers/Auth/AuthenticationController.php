<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Debug\Exception\FatalThrowableError;

class AuthenticationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'token' => 'required',
            ]);

        $validator->setAttributeNames(
            [
                'token' => 'Token',
            ]);
        if($validator->fails())
            return back()->withErrors($validator->errors())->withInput($request->all());

        $user = User::all()->where('remember_token', $request->token)->first();

        if($user != null){
            $user->state = 'ACTIVATED';
            $user->save();
            Auth::guard()->login($user);
            return view('reseller.dashboard')->withErrors('Hallo '.$user->name.', ihr Konto wurde erfolgreich freigeschaltet.');
        }

        return new FatalThrowableError('test');
    }
}
