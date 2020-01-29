<?php

namespace App\Http\Controllers\Admin\API;

use App\API;
use App\APIOptionEntry;
use App\Http\Controllers\Controller;
use App\Mail\AccountCreatingEmail;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tokens = API::all();
        return view('admin.api.index', compact('tokens'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        $validator = Validator::make($request->all(),
            [
                'token' => 'required|max:255|unique:api',
            ]);
        $validator->setAttributeNames(
            [
                'token' => 'API-Token',
            ]);
        if($validator->fails())
            return back()->withErrors($validator->errors())->withInput($request->all());

        $api = new API();
        $api->user_id = $request->user;
        $api->token = $request->token;
        $api->save();

        return back()->withSuccess('Der API-Token wurde erfolgreich angelegt, er benötigt nun seine einzelnen Option.');
    }

    /**
     * Show the resource of a user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(API $api)
    {
        return view('admin.api.single.index', compact('api'));
    }

    public function optionsadd(API $api, Request $request)
    {
        $entry = new APIOptionEntry();
        $entry->token_id = $api->id;
        $entry->option_id = $request->option_id;
        $entry->save();
        return back()->withSuccess('Die API-Option wurde erfolgreich zum API-Token hinzugefügt.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function remove(API $api, APIOptionEntry $option)
    {
        $option->delete();
        return back()->withSuccess('Die API-Option wurde erfolgreich entfernt.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}
