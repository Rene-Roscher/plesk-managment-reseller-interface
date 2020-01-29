@extends('layouts.app')

@section('content')
    <div class="container col-md-12">
        <div class="content mt-3">

            <div class="animated fadeIn">

                <div class="row">
                    <div class="col-lg-6" style="margin: 75px">
                        <div class="card cool-boxed">
                            <div class="card-body">

                                <div class="card-body">
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

                                            <span id="price">{{ $product->price }}</span>

                                            <button type="button" onclick="sendStep(1, this)" class="btn btn-info" style="float: right">
                                                <span>Bestellung überprüfen</span>
                                            </button>
                                        </form>

                                    </div>
                                </div>

                            </div>
                        </div>

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

@endsection