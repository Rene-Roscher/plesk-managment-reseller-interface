<?php

namespace App\Http\Controllers\Admin\APIOptions;

use App\API;
use App\APIOptionEntry;
use App\APIOptions;
use App\Http\Controllers\Controller;
use App\Mail\AccountCreatingEmail;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ApiOptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $options = APIOptions::all();
        return view('admin.apioptions.index', compact('options'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        $validator = Validator::make($request->all(),
            [
                'name' => 'required|max:255|unique:api_options',
                'state' => 'required|',
            ]);

        $validator->setAttributeNames(
            [
                'name' => 'API-Option',
                'state' => 'Status',
            ]);
        if($validator->fails())
            return back()->withErrors($validator->errors())->withInput($request->all());

        $option = new APIOptions();
        $option->name = $request->name;
        $option->state = $request->state;
        $option->save();

        return back()->withSuccess('Die API-Option wurde erfolgreich angelegt.');
    }

    public function toggleState(APIOptions $apioption)
    {
        if ($apioption->state == 'ACCESSIBLE') {
            $state = 'INACCESSIBLE';
            $apioption->state = $state;
            $apioption->save();
        } else {
            $state = 'ACCESSIBLE';
            $apioption->state = $state;
            $apioption->save();
        }
        return back()->withSuccess('Der Status von der API-Option wurde geändert.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(APIOptions $apioption)
    {
        /*foreach (APIOptionEntry::all()->where('option_id', $option->id) as $item){
            $item->delete();
        }*/
        $apioption->delete();
        return back()->withSuccess('Die API-Option wurde erfolgreich gelöscht.');
    }
}
