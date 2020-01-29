@extends('layouts.app')

@section('content')
    <div class="container col-md-12">
        <div class="content mt-3">

            <div class="animated fadeIn">

                <div class="row">

                    <a type="button" class="btn btn-primary col-xs-2" href="{{ route('reseller.accounting.index') }}" style="margin: 15px">Zurück</a>

                    <div class="col-sm-12">
                        <div class="card" style="border-bottom-right-radius: 50px;">
                            <div class="card-body card-block cool-boxed">
                                <table id="table" class="table table-striped table-bordered">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Betrag</th>
                                        <th>Beschreibung</th>
                                        <th>Art</th>
                                        <th>Typ</th>
                                        <th>Status</th>
                                        <th>Erstelldatum</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($transactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->id }}</td>
                                            <td>{{ $transaction->amount }}</td>
                                            <td>{{ $transaction->description }}</td>
                                            <td>{{ $transaction->type }}</td>
                                            <td>{{ str_replace('API', 'Externe Transaktion', str_replace('OWN', 'Interne Transaktion', $transaction->typ)) }}</td>
                                            <td>{{ str_replace('ERROR', 'Fehlerhaft', str_replace('PENDING', 'Ausstehend', str_replace('SUCCESS', 'Erfolgreich', $transaction->state))) }}</td>
                                            <td>{{ $transaction->created_at->format('d.m.Y H:i:s') }}</td>
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