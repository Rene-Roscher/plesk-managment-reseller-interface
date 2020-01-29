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

                                    <form id="orderForm" action="{{ route('reseller.service.reconfigure', ['service' => $service]) }}" method="post">
                                        @csrf

                                        @foreach ($upgrades as $upgrade)
                                            <div class="form-group">

                                                <label for="upgrade{{ $upgrade->id }}" class="control-label">{{ $upgrade->title }}</label>
                                                <select class="form-control upgrades" name="{{ $upgrade->upgrade }}" id="upgrade{{ $upgrade->id }}" onchange="calculate();">
                                                    @foreach($upgrade->entries() as $entry)
                                                        <option @if(object_get(json_decode($data), str_replace('ftpusers', 'ftp', str_replace('box', 'mail', $upgrade->upgrade))) == $entry->data) selected @endif value="{{ $entry->id }}" data-price="{{ $entry->price }}">{{ $entry->entry }} (+{{ \App\Helper\FormatHelper::money($entry->price, 2, ',', '') }} €)</option>
                                                    @endforeach
                                                </select>

                                            </div>
                                        @endforeach

                                        <span id="price">0.00 €</span>

                                        <button type="submit" id="submit-button" onclick="onClick(this);" class="btn btn-info" style="float: right">
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
        }
        function calculate() {
            let price = 0;
            price -= '<?php $i = 0; foreach ($upgrades as $upgrade) { foreach ($upgrade->entries() as $entry) { if (object_get(json_decode($data), str_replace('ftpusers', 'ftp', str_replace('box', 'mail', $upgrade->upgrade))) == $entry->data){ $i += $entry->price; } } } echo $i ?>';
            $('#orderForm .form-group .upgrades').each(function() {
                price += parseFloat($(this).children(':selected').attr('data-price'));
            });
            price = (price / 30 / 24 / 60 / 60 ) * "<?php echo $service->getLeftTime('seconds'); ?>";
            $('#price').html(price.toFixed(2)+" €");
        }
        calculate();
    </script>

@endsection