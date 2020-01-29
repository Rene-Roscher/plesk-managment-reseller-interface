<?php

namespace App\Http\Controllers\Admin\APILogs;

use App\APILogs;
use App\Http\Controllers\Controller;

class ApiLogsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $logs = APILogs::all();
        return view('admin.apilogs.index', compact('logs'));
    }

}
