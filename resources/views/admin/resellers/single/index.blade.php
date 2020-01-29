@extends('layouts.admin')

@section('content')
    <div class="">
        <div class="animated fadeIn">
            <div class="row" style="margin: 50px">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">

                            <div class="col-md-4">
                                <form method="POST" action="{{ route('admin.reseller.edit', ['user' => $user]) }}">
                                    @csrf
                                    <label class="form-control-label">Name</label>
                                    <input id="name" name="name" class="form-control" value="{{ $user->name }}">
                                    <br>
                                    <label class="form-control-label">E-Mail</label>
                                    <input id="email" name="email" class="form-control" type="email" value="{{ $user->email }}">
                                    <br>
                                    <label class="form-control-label">Guthaben</label>
                                    <input id="money" name="money" class="form-control" value="{{ $user->money }}">
                                    <br>
                                    <label class="form-control-label">Verfügungsrahmen</label>
                                    <input id="credit" name="credit" class="form-control" value="{{ $user->credit }}">
                                    <br>
                                    <label class="form-control-label">Rolle</label>
                                    <select id="role" name="role" class="form-control">
                                        <option id="{{ $user->role }}" name="{{ $user->role }}">{{ $user->role }}</option>
                                        @if($user->role != 'ADMIN')
                                            <option id="ADMIN" name="ADMIN">ADMIN</option>
                                        @elseif($user->role != 'RESELLER')
                                            <option id="RESELLER" name="RESELLER">RESELLER</option>
                                        @endif
                                    </select>
                                    <br>
                                    <label class="form-control-label">Status</label>
                                    <select id="state" name="state" class="form-control">
                                        <option id="{{ $user->state }}" name="{{ $user->state }}">{{ $user->state }}</option>
                                        @if($user->state != 'ACTIVATED')
                                            <option id="ACTIVATED" name="ACTIVATED">ACTIVATED</option>
                                        @elseif($user->state != 'DEACTIVATED')
                                            <option id="DEACTIVATED" name="DEACTIVATED">DEACTIVATED</option>
                                        @endif
                                    </select>

                                    <br>
                                    <button type="submit" class="btn btn-primary">Speichern</button>
                                </form>
                                <a type="button" class="btn btn-outline-primary col-xs-2" href="{{ route('admin.resellers.single.login', ['user' => $user]) }}" style="margin: 15px">Einloggen</a>
                                <button type="button" class="btn btn-info col-xs-2" data-toggle="modal" data-target="#reseller_edit_money" style="margin: 15px">Guthaben anpassen</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="reseller_edit_money" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mediumModalLabel">Guthaben anpassen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('admin.reseller.editmoney', ['user' => $user]) }}">
                        @csrf
                        <input id="money" name="money" class="form-control" placeholder="-/+ (0.01)" type="number" step="0.01">
                        <br>
                        <input id="description" name="description" class="form-control" placeholder="Grund">
                        <br>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                            <button type="submit" class="btn btn-primary">Anpassen</button>
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
