<?php

namespace App\Http\Controllers\Admin\Transactions;

use App\API;
use App\APIOptionEntry;
use App\Http\Controllers\Controller;
use App\Mail\AccountCreatingEmail;
use App\PaymentHandler;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class TransactionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transactions = PaymentHandler::all();
        return view('admin.transactions.index', compact('transactions'));
    }

}
