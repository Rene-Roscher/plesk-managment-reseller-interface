@extends('layouts.admin')

@section('content')
    <div class="">
        <div class="animated fadeIn">
            <div class="row" style="margin: 50px">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <button type="button" class="btn btn-primary col-xs-2" data-toggle="modal" data-target="#reseller_create" style="margin: 15px">API-Option erstellen</button>
                            <table id="table" class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Option</th>
                                    <th>Status</th>
                                    <th>Aktionen</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($options as $option)
                                    <tr>
                                        <td>{{ $option->id }}</td>
                                        <td>{{ $option->name }}</td>
                                        <td>{{ $option->state }}</td>
                                        <td>
                                            <a type="button" class="btn btn-primary btn-group-lg" href="{{ route('admin.apioption.toggleState', ['apioption' => $option->id]) }}">Ein/ausschalten</a>
                                            <a type="button" class="btn btn-danger btn-group-lg" href="{{ route('admin.apioption.destroy', ['apioption' => $option->id]) }}">Löschen</a>
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

    <div class="modal fade" id="reseller_create" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mediumModalLabel">API-Option erstellen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('admin.apioption.create') }}">
                        @csrf
                        <input name="name" id="name" class="form-control" placeholder="Option" type="text">
                        <br>
                        <select name="state" id="state" class="form-control">
                            <option selected>ACCESSIBLE</option>
                            <option>INACCESSIBLE</option>
                        </select>
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
                "aaSorting": [[0, 'desc']],
            });
        });

    </script>

@endsection
