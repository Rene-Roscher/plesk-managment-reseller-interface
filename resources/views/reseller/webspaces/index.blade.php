@extends('layouts.app')

@section('content')
    <div class="container col-md-12">
        <div class="content mt-3">

            <div class="animated fadeIn">

                <div class="row">


                    <div class="col-sm-12">
                        <div class="card" style="border-bottom-right-radius: 50px;">
                            <div class="card-body card-block">
                                <button type="button" class="btn btn-primary col-xs-2" data-toggle="modal" data-target="#order_webspace" style="margin: 15px">Webspace bestellen</button>
                                <table id="table" class="table table-striped table-bordered">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Domainname</th>
                                        <th>PlanID</th>
                                        <th>Erstelldatum</th>
                                        <th>Ablaufdatum</th>
                                        <th>Aktionen</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($webspaces as $webspace)
                                        <tr>
                                            <td>{{ $webspace->id }}</td>
                                            <td>{{ $webspace->plesk_url }}</td>
                                            <td>{{ $webspace->plan }}</td>
                                            <td>{{ $webspace->created_at->format('d.m.Y H:i:s') }}</td>
                                            <td>
                                                @if($webspace->service)
                                                    {{ date_format(date_create($webspace->service->expire_at), 'd.m.Y H:i') }}</td>
                                                @endif
                                            <td>
                                                @if($webspace->service)
                                                @if(!$webspace->service->expired_at)
                                                    @if($webspace->installed)
                                                        <a type="button" class="btn btn-primary btn-xs" href="{{ route('reseller.webspace.single.index', ['api' => $webspace]) }}">Details</a>
                                                    @else
                                                        <a class="btn btn-outline-warning"><i class="fa fa-fw fa-spin fa-spinner"></i>Wird eingerichtet...</a>
                                                    @endif
                                                @else
                                                    <a class="btn btn-outline-danger">Ausgelaufen</a>
                                                @endif
                                                <a type="button" class="btn btn-outline-info btn-xs" href="{{ route('reseller.service.extends', ['service' => $webspace->service]) }}">Verlängern</a>
                                                <a type="button" class="btn btn-outline-info btn-xs" href="{{ route('reseller.service.reconfigure', ['service' => $webspace->service]) }}">Up/Downgrade</a>
                                                @endif
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

    <div class="modal fade" id="order_webspace" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mediumModalLabel">Webspace bestellen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div id="orderDiv">

                        <form id="orderForm" method="post">
                            {{ csrf_field() }}

                            @foreach ($upgrades as $upgrade)
                                <div class="form-group">
                                    <label class="control-label">{{ $upgrade->title }}</label>

                                    <select class="form-control upgrades" name="upgrade{{ $upgrade->id }}" id="upgrade{{ $upgrade->id }}" onchange="calculate();">
                                        @foreach($upgrade->entries() as $entry)
                                            <option value="{{ $entry->id }}" data-price="{{ $entry->price }}">{{ $entry->entry }} (+{{ \App\Helper\FormatHelper::money($entry->price, 2, ',', '') }} €)</option>
                                        @endforeach
                                    </select>

                                </div>
                            @endforeach

                            <div class="form-group">
                                <label class="control-label">Vorauszahlung</label>

                                <select class="form-control" name="runtime" id="runtime" onchange="calculate();">
                                    @foreach (json_decode($product->data) as $runtimeEntry)
                                        <option value="{{ str_replace_first(' ', '', str_replace_first('days', '', $runtimeEntry->runtime)) }}" data-factor="{{ $runtimeEntry->price }}" @if($runtimeEntry->default) selected="selected" @endif>{{ str_replace_first('days', '', $runtimeEntry->runtime) }} Tage</option>
                                    @endforeach
                                </select>

                            </div>

                            <br><br>

                            <div class="modal-footer">
                                <span id="price" style="margin-right: 50%;font-size: 140%;">
                                    {{ $product->price }}
                                </span>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                                <button type="button" onclick="sendStep(1, this)" class="btn btn-primary">Überprüfen</button>
                            </div>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function calculate() {
            let price = 0;
            $('#orderForm .form-group .upgrades').each(function() {
                price += parseFloat($(this).children(':selected').attr('data-price'));
            });
            price = price * $('#runtime').children(':selected').attr('data-factor');
            $('#price').html(price.toFixed(2)+" €");
        }

        function sendStep(step, btn) {
            if (btn != null) {
                $(btn).attr('disabled', 'disabled');
                $(btn).html('<i class="fa fa-spinner fa-spin"></i> L&auml;dt...');
            }
            if (step == '0') {
                $('#orderDiv').html(inner);
            } else {
                $.post('{{ url('reseller/webspace/step') }}' + step, $('#orderForm').serialize(), function (data) {
                    $('#orderDiv').html(data);
                }, "json");
            }
        }
        calculate();
    </script>

    <script>
        $(document).ready(function () {
            $('#table').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json",
                },
                "aaSorting": [[0, 'desc']]
            });
        });
    </script>


@endsection
