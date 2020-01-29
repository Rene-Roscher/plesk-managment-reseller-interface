@extends('layouts.app')

@section('content')
    <div class="container col-md-12">
        <div class="content mt-3">

            <div class="animated fadeIn">

                <div class="row">

                    <div class="col-sm-10">
                        <div class="card" style="border-bottom-right-radius: 50px;">
                            <div class="card-body">
                                <a type="button" class="btn btn-primary col-xs-2" href="{{ route('reseller.api.index') }}" style="margin: 15px">Zurück</a>
                                <a type="button" class="btn btn-outline-dark col-xs-2" href="{{ route('reseller.api.single.logs.index', ['api' => $api]) }}" style="margin: 15px">Logs</a>
                                <a type="button" class="btn btn-outline-dark col-xs-2" href="{{ route('reseller.api.single.whitelist.index', ['api' => $api]) }}" style="margin: 15px">Whitelist</a>
                                <a type="button" class="btn btn-outline-dark col-xs-2" href="{{ route('reseller.api.single.refresh', ['api' => $api]) }}" style="margin: 15px">Token aktualisieren</a>

                                <div class="card-block" style="margin: 50px">
                                    <h3 style="margin-top: 5px"><strong>Infomationen</strong></h3>
                                    <span>Token: <strong>{{ $api->token }}</strong></span><br>
                                    <span>Optionen: <strong>{{ $api->options2()->count().'/'.\App\APIOptions::all()->count() }}</strong></span><br>
                                    <span>Aufrufe: <strong>{{ $api->logs()->count() }}</strong></span><br>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="card" style="border-bottom-right-radius: 50px;">
                            <div class="card-body card-block">
                                <table id="table" class="table table-striped table-bordered">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Option</th>
                                        <th>Aktiv</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach(\App\APIOptions::all() as $option)
                                        <tr>
                                            <td>{{ $option->id }}</td>
                                            <td>{{ $option->name }}</td>
                                            <td>{{ $api->hasOption($option->id) ? 'Ja' : 'Nein' }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#table').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json",
                },
                "aaSorting": [[0, 'desc']],
                buttons: [
                    'excel', 'pdf', 'print'
                ],
            });
        });
    </script>


@endsection
