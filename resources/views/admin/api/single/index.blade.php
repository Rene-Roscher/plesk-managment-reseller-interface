@extends('layouts.admin')

@section('content')
    <div class="">
        <div class="animated fadeIn">
            <div class="row" style="margin: 50px">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <button type="button" class="btn btn-primary col-xs-2" data-toggle="modal" data-target="#option_add" style="margin: 15px">API-Option hinzufügen</button>
                            <table id="table" class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Option</th>
                                    <th>Aktionen</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($api->options2() as $option)
                                    <tr>
                                        <td>{{ $option->id }}</td>
                                        <td>{{ $option->option->name }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('admin.api.single.remove', ['api' => $api, 'option' => $option]) }}">
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

    <?php

    ?>

    <div class="modal fade" id="option_add" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mediumModalLabel">API-Option hinzufügen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('admin.api.single.optionsadd', ['api' => $api]) }}">
                        @csrf
                        <select name="option_id" id="option_id" class="form-control">
                            <?php $val = 0; ?>
                            @foreach(\App\APIOptions::all() as $item)
                                @if(!$api->hasOption($item->id))
                                    <?php $val += 1; ?>
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endif
                            @endforeach
                            @if($val <= 0)
                                <option>Es wurden keine weiteren API-Optionen gefunden....</option>
                            @endif
                        </select>
                        <br>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                            <button type="submit" class="btn btn-primary" @if($val <= 0) readonly disabled @endif >Hinzufügen</button>
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
