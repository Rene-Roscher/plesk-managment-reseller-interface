@extends('layouts.app')

@section('content')
    <div class="container col-md-12">
        <div class="content mt-3">

            <div class="animated fadeIn">

                <div class="row">

                    <div class="col-sm-12">
                        <a type="button" class="btn btn-primary col-xs-2" href="{{ route('reseller.api.single.index', ['api' => $api]) }}" style="margin: 15px">Zurück</a>
                        <div class="card" style="border-bottom-right-radius: 50px;">
                            <div class="card-body">
                                <button type="button" class="btn btn-primary col-xs-2" data-toggle="modal" data-target="#address_add" style="margin: 15px">IP hinzufügen</button>
                                <div class="card-block">

                                    <table id="table" class="table table-striped table-bordered">
                                        <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>IP</th>
                                            <th>Anfragen</th>
                                            <th>Erstelldatum</th>
                                            <th>Aktionen</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($api->whitelist() as $whitelist)
                                            <tr>
                                                <td>{{ $whitelist->id }}</td>
                                                <td>{{ $whitelist->address }}</td>
                                                <td>{{ $api->logs()->where('ip', $whitelist->address)->count() }}</td>
                                                <td>{{ $whitelist->created_at->format('d.m.Y H:i:s') }}</td>
                                                <td>
                                                    <form method="POST" action={{ route('reseller.api.single.whitelist.remove', ['api' => $api, 'whitelist' => $whitelist]) }}>
                                                        @csrf
                                                        <button type="submit" class="btn btn-primary btn-group-lg">Entfernen</button>
                                                    </form>
                                                </td>
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
    </div>

    <div class="modal fade" id="address_add" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mediumModalLabel">IP hinzufügen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('reseller.api.single.whitelist.add', ['api' => $api]) }}">
                        @csrf
                        <input name="address" id="address" class="form-control" placeholder="IP-Adresse" type="text">
                        <br>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                            <button type="submit" class="btn btn-primary">Hinzufügen</button>
                        </div>
                    </form>
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
