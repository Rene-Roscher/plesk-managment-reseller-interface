@extends('layouts.admin')

@section('content')
    <div class="">
        <div class="animated fadeIn">
            <div class="row" style="margin: 50px">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <button type="button" class="btn btn-primary col-xs-2" data-toggle="modal" data-target="#reseller_create" style="margin: 15px">Benutzer erstellen</button>
                            <table id="table" class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>E-Mail</th>
                                    <th>Guthaben</th>
                                    <th>Verfügungsrahmen</th>
                                    <th>Reserviert</th>
                                    <th>Aktionen</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($resellers as $reseller)
                                        <tr>
                                            <td>{{ $reseller->id }}</td>
                                            <td>{{ $reseller->name }}</td>
                                            <td>{{ $reseller->email }}</td>
                                            <td>{{ $reseller->money }}</td>
                                            <td>{{ $reseller->credit }}</td>
                                            <td>{{ $reseller->reserved }}</td>
                                            <td><a class="btn btn-primary btn-xs" href="{{ route('admin.resellers.single.index', ['user' => $reseller]) }}">Details</a></td>
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

    <div class="modal fade" id="reseller_create" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mediumModalLabel">Reseller erstellen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('admin.reseller.create') }}">
                        @csrf
                        <input id="name" name="name" class="form-control" placeholder="Name">
                        <br>
                        <input id="email" name="email" class="form-control" type="email" placeholder="E-Mail">
                        <br>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                            <button type="submit" class="btn btn-primary">Erstellen</button>
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
                "aaSorting": [[0, 'asc']],
            });
        });

    </script>

@endsection
