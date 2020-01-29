@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card cool-boxed">

                    <div class="card-body">

                        <a href="{{ url('admin/resellers') }}">
                            <div class="col-md-4">
                                <div class="card cool-boxed">
                                    <div class="card-body">
                                        <div class="stat-widget-one">
                                            <div class="stat-icon dib"><i class="fa fa-users cool-text-success border-cool-success"></i>
                                            </div>
                                            <div class="stat-content dib">
                                                <div class="stat-text"><strong>Resellers</strong></div>
                                                <div class="stat-digit">{{ \App\User::all()->count() }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>

                        <a href="{{ url('admin/api') }}">
                            <div class="col-md-4">
                                <div class="card cool-boxed">
                                    <div class="card-body">
                                        <div class="stat-widget-one">
                                            <div class="stat-icon dib"><i class="fa fa-wifi cool-text-success border-cool-success"></i>
                                            </div>
                                            <div class="stat-content dib">
                                                <div class="stat-text"><strong>API-Tokens</strong></div>
                                                <div class="stat-digit">{{ \App\API::all()->count() }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>

                        <a href="{{ url('admin/apioptions') }}">
                            <div class="col-md-4">
                                <div class="card cool-boxed">
                                    <div class="card-body">
                                        <div class="stat-widget-one">
                                            <div class="stat-icon dib"><i class="fa fa-wifi cool-text-success border-cool-success"></i>
                                            </div>
                                            <div class="stat-content dib">
                                                <div class="stat-text"><strong>API-Options</strong></div>
                                                <div class="stat-digit">{{ \App\APIOptions::all()->count() }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>

                        <a href="{{ url('admin/transactions') }}">
                            <div class="col-md-4">
                                <div class="card cool-boxed">
                                    <div class="card-body">
                                        <div class="stat-widget-one">
                                            <div class="stat-icon dib"><i class="fa fa-dollar cool-text-success border-cool-success"></i>
                                            </div>
                                            <div class="stat-content dib">
                                                <div class="stat-text"><strong>Transaktionen</strong></div>
                                                <div class="stat-digit">{{ \App\PaymentHandler::all()->count() }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>

                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
