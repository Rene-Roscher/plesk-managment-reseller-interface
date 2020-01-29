@extends('layouts.app')

@section('content')
    <div class="container col-md-12">
        <div class="content mt-3">
2
            <div class="animated fadeIn">

                <div class="row">

                    <div class="col-sm-12">
                        <a type="button" class="btn btn-primary col-xs-2" href="{{ route('reseller.api.single.index', ['api' => $api]) }}" style="margin: 15px">Zurück</a>
                        <div class="card" style="border-bottom-right-radius: 50px;">
                            <div class="card-body">
                                <div class="card-block">
                                    <table id="table" class="table table-striped table-bordered">
                                        <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>IP</th>
                                            <th>URI</th>
                                            <th>Status</th>
                                            <th>Code</th>
                                            <th>Erstelldatum</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($api->logs as $option)
                                            <tr>
                                                <td>{{ $option->id }}</td>
                                                <td>{{ $option->ip }}</td>
                                                <td>{{ $option->uri }}</td>
                                                <td>{{ $option->state }}</td>
                                                <td>{{ $option->respocode }}</td>
                                                <td>{{ $option->created_at->format('d.m.Y H:i:s') }}</td>
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
