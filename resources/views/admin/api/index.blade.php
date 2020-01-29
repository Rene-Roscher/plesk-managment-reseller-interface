@extends('layouts.admin')

@section('content')
    <div class="">
        <div class="animated fadeIn">
            <div class="row" style="margin: 50px">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <button type="button" class="btn btn-primary col-xs-2" data-toggle="modal" data-target="#reseller_create" style="margin: 15px">API-Token erstellen</button>
                            <table id="table" class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Token</th>
                                    <th>Aktionen</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($tokens as $token)
                                    <tr>
                                        <td>{{ $token->id }}</td>
                                        <td><a href="{{ route('admin.resellers.single.index', ['api' => $token->user->id]) }}"><i>{{ $token->user->name }}</i></a></td>
                                        <td>{{ $token->token }}</td>
                                        <td><a type="button" class="btn btn-primary btn-group-lg" href="{{ route('admin.api.single.index', ['api' => $token]) }}">Details</a></td>
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
                    <h5 class="modal-title" id="mediumModalLabel">API-Token erstellen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('admin.api.create') }}">
                        @csrf
                        <div class="input-group input-group">
                            <input name="token" id="token" class="form-control" readonly="" type="text">
                            <div class="input-group-btn">
                                <span class="btn btn-dark" onclick="generateToken();">Generieren</span>
                            </div>
                        </div>
                        <br>
                        <select name="user" id="user" class="form-control">
                            @foreach(\App\User::all() as $item)
                                <option value="{{ $item->id }}">{{ $item->name.' #'.$item->id }}</option>
                            @endforeach
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
                "aaSorting": [[0, 'asc']],
            });
        });

    </script>

    <script>
        function generateToken() {
            var chars = 'abcdefghijglmnopyxzreneistvollcoolpsdercodeistdochtollduskiderABCDEFGHIJKLMNOPMANUISTCOOL';
            var key = "";
            for (var x = 0; x <= 38+5+5+5+5; x++) {
                if (x == 4 || x == 9 || x == 14 || x == 19 || x == 24 || x == 29 || x == 34 || x == 34+5 || x == 34+5+5 || x == 34+5+5+5 || x == 34+5+5+5+5 || x == 34+5+5+5+5+5) {
                    key += '-';
                } else {
                    var i = Math.floor(Math.random() * chars.length);
                    key += chars.charAt(i);
                }
            }
            $('#token').val(key);
        }
    </script>

@endsection
