@extends('layouts.admin')

@section('content')
    <div class="">
        <div class="animated fadeIn">
            <div class="row" style="margin: 50px">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <table id="table" class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>API</th>
                                    <th>IP</th>
                                    <th>URI</th>
                                    <th>Status</th>
                                    <th>Code</th>
                                    <th>Erstelldatum</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($logs as $item => $log)
                                    <tr>
                                        <td>{{ $log->id }}</td>
                                        <td><a href="{{ route('admin.api.single.index', ['api' => $log->token_id]) }}"><i>Aufrufen</i></a></td>
                                        <td>{{ $log->ip }}</td>
                                        <td>{{ $log->uri }}</td>
                                        <td>{{ $log->state }}</td>
                                        <td>{{ $log->respocode }}</td>
                                        <td>{{ $log->created_at->format('d.m.Y H:i:s') }}</td>
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

    <script>
        $(document).ready(function () {
            $('#table').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json",
                },
                "aaSorting": [[0, 'desc']],
            });
        });
    </script>

@endsection
