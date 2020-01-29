@extends('layouts.app')

@section('content')
    <style>
        table {border-collapse: collapse;}
        table td {padding: 0px}
    </style>
    <div class="container col-md-12">
        <div class="content mt-3">

            <div class="animated fadeIn">

                <div class="row">
                    <div class="col-lg-6">
                        <div class="card cool-boxed">
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">

                                        <form method="POST" action="{{ route('reseller.accounting.add') }}">
                                            @csrf
                                            <div class="form-group">
                                                <label for="amount" class="control-label mb-1">Betrag</label>
                                                <input id="amount" name="amount" class="form-control" value="25.00" step="0.01" type="number" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="cc-exp" class="control-label mb-1">Methode</label>
                                                <select id="type" class="form-control" name="type">
                                                    <option value="PAYPAL">PayPal</option>
                                                    <option value="PAYSAFECARD">paysafecard</option>
                                                    <option value="SOFORT" disabled>Sofort</option>
                                                </select>
                                            </div>
                                            <div>
                                                <button id="payment-button" type="submit" class="btn btn-lg btn-info btn-block">
                                                    <span id="payment-button-amount">Guthaben kostenpflichtig aufladen</span>
                                                </button>
                                            </div>
                                        </form>

                                    </div>
                                </div>

                            </div>
                        </div> <!-- .card -->

                    </div><!--/.col-->

                    <div class="col-md-6 col-sm-6 col-xs-4">
                        <div class="card" style="border-bottom-right-radius: 50px;">
                            <div class="card-body card-block" style="margin: 25px">
                                <div class="col-md-6 col-xs-6">
                                    <h2 style="margin-top: 5px"><strong>Buchhaltung</strong></h2>
                                    <br>
                                    <h4>Guthaben: <strong>{{ Auth::user()->money.' €' }} </strong></h4>
                                    <h4>Verfügungsrahmen: <strong>{{ Auth::user()->credit.' €' }} </strong></h4>
                                    <h4>

                                    </h4>
                                </div>

                                <div class="col-md-6 col-xs-6">
                                    <h2 style="margin-top: 5px"><strong>Einnahmen</strong></h2>
                                    <br>
                                    <h4>Heute: <strong>{{ \App\PaymentHandler::all()->where('user_id', Auth::user()->id)->where('type', '!=', 'INTERN')->where('state', 'SUCCESS')->where('created_at', '>=', \Illuminate\Support\Carbon::now()->startOfDay())->where('created_at', '<=', \Illuminate\Support\Carbon::now()->endOfDay())->sum('amount') }} €</strong></h4>
                                    <h4>Diese Woche: <strong>{{ \App\PaymentHandler::all()->where('user_id', Auth::user()->id)->where('type', '!=', 'INTERN')->where('state', 'SUCCESS')->where('created_at', '>=', \Illuminate\Support\Carbon::now()->startOfWeek())->where('created_at', '<=', \Illuminate\Support\Carbon::now()->endOfWeek())->sum('amount') }} €</strong></h4>
                                    <h4>Diesen Monate: <strong>{{ \App\PaymentHandler::all()->where('user_id', Auth::user()->id)->where('type', '!=', 'INTERN')->where('state', 'SUCCESS')->where('created_at', '>=', \Illuminate\Support\Carbon::now()->startOfMonth())->where('created_at', '<=', \Illuminate\Support\Carbon::now()->endOfMonth())->sum('amount') }} €</strong></h4>
                                    <h4>Dieses Jahr: <strong>{{ \App\PaymentHandler::all()->where('user_id', Auth::user()->id)->where('type', '!=', 'INTERN')->where('state', 'SUCCESS')->where('created_at', '>=', \Illuminate\Support\Carbon::now()->startOfYear())->where('created_at', '<=', \Illuminate\Support\Carbon::now()->endOfYear())->sum('amount') }} €</strong></h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="card" style="border-bottom-right-radius: 50px;">
                            <div class="card-body card-block cool-boxed">
                                <table id="table" class="table table-striped table-bordered">
                                    <thead>
                                    <tr>
                                        <th>Jahr</th>
                                        <th>Monat</th>
                                        <th>Transaktionen</th>
                                        <th>Eingenommen</th>
                                        <th>Aktionen</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(\App\PaymentHandler::all()->where('user_id', Auth::user()->id)->last() != null)
                                        @for($date = \Carbon\Carbon::createFromDate(date_format(Auth::user()->created_at, 'Y'), date_format(Auth::user()->created_at, 'm'), 1); $date->lte(\App\PaymentHandler::all()->where('user_id', Auth::user()->id)->last()->created_at); $date->addMonth())
                                            <tr>
                                                <td>{{ $date->format('Y') }}</td>
                                                <td>{{ $date->format('m') }}</td>
                                                <td>{{ \App\PaymentHandler::all()->where('user_id', Auth::user()->id)->where('type', '!=', 'INTERN')->where('created_at', '>=', \Carbon\Carbon::parse($date->format('Y-m-d')))->where('created_at', '<=', \Carbon\Carbon::createFromDate($date->year, $date->month, 31))->count() }}</td>
                                                <td>{{ \App\PaymentHandler::all()->where('user_id', Auth::user()->id)->where('type', '!=', 'INTERN')->where('state', 'SUCCESS')->where('created_at', '>=', $date)->where('created_at', '<=', \Carbon\Carbon::createFromDate($date->year, $date->month, 31))->sum('amount') }}</td>
                                                <td>
                                                    <a type="button" class="btn btn-primary btn-xs" href="{{ route('reseller.accounting.single.index', ['year' => $date->format('Y'), 'month' => $date->format('m')]) }}">Details</a>
                                                    <a type="button" class="btn btn-primary btn-xs" href="{{ route('reseller.accounting.single.export', ['year' => $date->format('Y'), 'month' => $date->format('m')]) }}">PDF</a>
                                                </td>
                                            </tr>
                                        @endfor
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>


                </div>


            </div><!-- .animated -->
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#table').DataTable({
                "lengthMenu": [
                    [25, 50, 100,500, -1],
                    [25, 50,100,500,"Alle"]
                ],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json",
                },
                "aaSorting": [
                    [0,'desc'],
                    [1,'desc'],
                ],
            });
        });


    </script>

@endsection