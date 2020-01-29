@extends('layouts.app')

@section('content')
    <div class="container col-md-12">
        <div class="content mt-3">

            <div class="animated fadeIn">

                <div class="row">

                    <div class="col-sm-12">
                        <div class="card" style="border-bottom-right-radius: 50px;">
                            <div class="card-body card-block">
                                <table id="table" class="table table-striped table-bordered">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Token</th>
                                        <th>Anfragen</th>
                                        <th>Erstelldatum</th>
                                        <th>Aktualisierungsdatum</th>
                                        <th>Aktionen</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($apis as $api)
                                        <tr>
                                            <td>{{ $api->id }}</td>
                                            <td>{{ $api->token }}</td>
                                            <td>{{ $api->logs()->count() }}</td>
                                            <td>{{ $api->created_at->format('d.m.Y H:i:s') }}</td>
                                            <td>{{ $api->updated_at->format('d.m.Y H:i:s') }}</td>
                                            <td>
                                                <a type="button" class="btn btn-primary btn-xs" href="{{ route('reseller.api.single.index', ['api' => $api]) }}">Details</a>
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
