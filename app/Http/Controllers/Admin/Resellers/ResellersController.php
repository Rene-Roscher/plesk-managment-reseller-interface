<?php

namespace App\Http\Controllers\Admin\Resellers;

use App\Http\Controllers\Controller;
use App\Mail\AccountCreatingEmail;
use App\PaymentHandler;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class ResellersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $resellers = User::all();
        return view('admin.resellers.index', compact('resellers'));
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        $validator = Validator::make($request->all(),
            [
                'name' => 'required|max:255',
                'email' => 'required|email|max:255|unique:users',
            ]);

        $validator->setAttributeNames(
            [
                'name' => 'Name',
                'email' => 'E-Mail',
            ]);
        if($validator->fails())
            return back()->withErrors($validator->errors())->withInput($request->all());

        $username = $request->name;
        $email = $request->email;
        $password = str_random(16);

        $user = User::create(['name' => $username, 'email' => $email, 'password' => Hash::make($password)]);
        $mail = new AccountCreatingEmail($user, $password);

//        Mail::to($email)->send($mail)->from('no-reply@controlserv.de')->subject('Reseller Zugangsdaten');

        Mail::send($mail, ['user' => $user, 'password' => $password], function ($m) use ($user)
        {
            $m->from('no-reply@controlserv.de', 'ControlServ - Support');
            $m->to($user->email)->subject('Reseller Zugangsdaten');
        });

        return back()->withSuccess('Der Reseller wurde erfolgreich angelegt, er erhielt seine Daten über die angegebende E-Mail Adresse.');
    }

    /**
     * Show the resource of a user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return view('admin.resellers.single.index', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user, Request $request)
    {
        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->credit = $request->get('credit');
        $user->role = $request->get('role');
        $user->state = $request->get('state');
        $user->save();

        return back()->withSuccess('Der Reseller wurde erfolgreich bearbeitet.');
    }

    public function editMoney(User $user, Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'money' => 'required|numeric',
                'description' => 'required',
            ]);

        $validator->setAttributeNames(
            [
                'money' => 'Betrag',
                'description' => 'Grund',
            ]);
        if($validator->fails())
            return back()->withErrors($validator->errors())->withInput($request->all());

        $transaction = new PaymentHandler();
        $transaction->user_id = Auth::id();
        $transaction->type = $request->type;
        $transaction->amount = $request->money;
        $transaction->description = $request->description;
        $transaction->mtid = 0;
        $transaction->type = 'INTERN';
        $transaction->state = 'SUCCESS';
        $transaction->save();

        $user->money += $request->money;
        $user->save();

        return back()->withSuccess('Der Reseller wurde erfolgreich bearbeitet.');
    }

    /**
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function login(User $user)
    {
        Session::put('admin_login', [
            'user_id' => Auth::id(),
            'url' => URL::previous()
        ]);
        Auth::loginUsingId($user->id);
        return redirect('reseller');
    }

}
