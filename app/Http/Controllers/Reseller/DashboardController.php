<?php
/**
 * Created by PhpStorm.
 * User: mrlog
 * Date: 11.08.2018
 * Time: 14:22
 */

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('reseller.dashboard');
    }

    public function undoLogin() {
        if (!Session::has('admin_login'))
            abort(404);
        $session = Session::get('admin_login');
        Session::put('admin_login', null);
        Auth::loginUsingId($session['user_id']);
        return redirect($session['url'])->withSuccess('Sie sind nun wieder in ihren Account.');
    }

}
