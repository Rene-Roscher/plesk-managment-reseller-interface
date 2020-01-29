@extends('layouts.app')

@section('content')
    <div class="container col-md-12">
        <div class="content mt-3">

            <div class="animated fadeIn">

                <div class="row">
                    <div class="col-lg-6" style="margin: 75px">
                        <div class="card cool-boxed">
                            <div class="card-body">
                            <h4>Restlaufzeit: <span id="countdown" data-date="{{ $service->expire_at }}"></span></h4>

                                <div class="card-body">


                                    <form action="{{ route('reseller.service.extend', ['service' => $service]) }}" method="post">
                                        @csrf

                                        <div class="form-group">
                                            <label class="control-label">Vorauszahlung</label>

                                            <select class="form-control" name="runtime" id="runtime" onchange="calculate();">
                                                @foreach (json_decode($product->data) as $runtimeEntry)
                                                    <option value="{{ str_replace_first(' ', '', str_replace_first('days', '', $runtimeEntry->runtime)) }}" data-factor="{{ $runtimeEntry->price }}" @if($runtimeEntry->default) selected="selected" @endif>{{ str_replace_first('days', '', $runtimeEntry->runtime) }} Tage</option>
                                                @endforeach
                                            </select>

                                        </div>

                                        <span id="price">{{ \App\Helper\FormatHelper::money($service->getExtendsPrice()) }} €</span>

                                        <button type="submit" onclick="onClick(this);" class="btn btn-info" style="float: right">
                                            <span>Verlängern</span>
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
    <script>
        function onClick(btn) {
            $(btn).attr('disabled', 'disabled');
            $(btn).html('<i class="fa fa-spinner fa-spin"></i> L&auml;dt...');
            $(btn).submit();
        }
        function calculate() {
            let price = '{{ $service->getExtendsPrice() }}';
            price = price * $('#runtime').children(':selected').attr('data-factor');
            $('#price').html(price.toFixed(2)+" €");
        }
        $('#countdown').countdown("{{ $service->expire_at }}", function(event) {
            $(this).text(
                event.strftime('%D Tage %H Stunden %M Minuten und %S Sekunden')
            );
        });
    </script>

@endsection