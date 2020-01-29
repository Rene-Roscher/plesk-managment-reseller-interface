@extends('layouts.app')

@section('content')
    <div class="container col-md-12">

        <div class="container-fluid">
            <div class="col-md-3">
                <div class="card cool-boxed">
                    <div class="card-body">
                        <div class="stat-widget-one">
                            <div class="stat-icon dib"><i class="fa fa-money cool-text-success border-cool-success"></i>
                            </div>
                            <div class="stat-content dib">
                                <div class="stat-text"><strong>Guthaben</strong></div>
                                <div class="stat-digit">1,012</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <a href="{{ route('reseller.api.index') }}">
                <div class="col-md-3">
                    <div class="card cool-boxed">
                        <div class="card-body">
                            <div class="stat-widget-one">
                                <div class="stat-icon dib"><i class="fa fa-users cool-text-success border-cool-success"></i>
                                </div>
                                <div class="stat-content dib">
                                    <div class="stat-text"><strong>Api</strong></div>
                                    <div class="stat-digit">{{ \App\API::all()->where('user_id', Auth::user()->id)->count() }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
            <div class="col-md-3">
                <div class="card cool-boxed">
                    <div class="card-body">
                        <div class="stat-widget-one">
                            <div class="stat-icon dib"><i class="fa fa-money cool-text-success border-cool-success"></i>
                            </div>
                            <div class="stat-content dib">
                                <div class="stat-text"><strong>Traffic</strong></div>
                                <div class="stat-digit">1,012</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card cool-boxed">
                    <div class="card-body">
                        <div class="stat-widget-one">
                            <div class="stat-icon dib"><i class="fa fa-money cool-text-success border-cool-success"></i>
                            </div>
                            <div class="stat-content dib">
                                <div class="stat-text"><strong>adsdass</strong></div>
                                <div class="stat-digit">1,012</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="content mt-3">
        <div class="animated fadeIn">

            <div class="row">

                <div class="col-sm-6">
                    <div class="card" style="border-bottom-right-radius: 50px;">
                        <div class="card-header">
                            <strong>News</strong>
                        </div>
                        <div class="card-body card-block">
                            <div class="alert alert-info">
                                Zurzeit sind keine News vorhanden.
                            </div>
                        </div>
                    </div>
                </div>

                {{--<div class="col-sm-6">--}}
                    {{--<div class="card" style="border-bottom-right-radius: 50px;">--}}
                        {{--<div class="card-header">--}}
                            {{--<strong>API Calls</strong>--}}
                        {{--</div>--}}
                        {{--<div class="card-body card-block">--}}
                            {{--<table id="table" class="table table-striped table-bordered">--}}
                                {{--<thead>--}}
                                {{--<tr>--}}
                                    {{--<th>ID</th>--}}
                                    {{--<th>Name</th>--}}
                                    {{--<th>E-Mail</th>--}}
                                    {{--<th>Guthaben</th>--}}
                                    {{--<th>Aktionen</th>--}}
                                {{--</tr>--}}
                                {{--</thead>--}}
                                {{--<tbody>--}}
                                {{--@foreach(['' => null] as $reseller)--}}
                                    {{--<tr>--}}
                                        {{--<td>1</td>--}}
                                        {{--<td>Rene</td>--}}
                                        {{--<td>ENAI</td>--}}
                                        {{--<td></td>--}}
                                        {{--<td><p data-placement="top" data-toggle="tooltip" title="Edit"><button class="btn btn-primary btn-xs" data-title="Edit" data-toggle="modal" data-target="#edit" ><span class="glyphicon glyphicon-pencil"></span></button></p></td>--}}
                                    {{--</tr>--}}
                                {{--@endforeach--}}
                                {{--</tbody>--}}
                            {{--</table>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}

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
