<?php
/**
 * Created by PhpStorm.
 * User: mrlog
 * Date: 07.09.2018
 * Time: 23:55
 */

namespace App\Http\Controllers\Reseller\Orders;


use App\Http\Controllers\Controller;
use App\Order;
use Illuminate\Support\Facades\Auth;

class OrdersController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $orders = Order::all()->where('user_id', Auth::user()->id);
        return view('reseller.orders.index', compact('orders'));
    }

}