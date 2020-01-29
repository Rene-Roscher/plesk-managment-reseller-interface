@extends('layouts.app')

@section('content')
    <div class="container col-md-12">
        <div class="content mt-3">

            <div class="animated fadeIn">

                <div class="row">

                    <div class="col-sm-12">
                        <div class="card" style="border-bottom-right-radius: 50px;">
                            <div class="card-body card-block">
                                <table id="table" class="table table-striped table-bordered">
                                    <thead>
                                    <tr>
                                        {{--'user_id', 'service_id', 'product_id', 'amount', 'interval', 'type', 'state',--}}
                                        <th>ID</th>
                                        <th>Produkt</th>
                                        <th>Betrag</th>
                                        <th>Status</th>
                                        <th>Intervall</th>
                                        <th>Typ</th>
                                        <th>Erstelldatum</th>
                                        <th>Aktualisierungsdatum</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($orders as $order)
                                        <tr>
                                            <td>{{ $order->id }}</td>
                                            <td>{{ $order->product->name }}</td>
                                            <td>{{ $order->amount }}</td>
                                            <td>{{ str_replace('ERROR', 'Fehler', str_replace('PENDING', 'Ausstehend', str_replace('SUCCESS', 'Erfolgreich', $order->state))) }}</td>
                                            <td>
                                                @if($order->interval == '- / -' || $order->interval == 0)
                                                    - / -
                                                @else
                                                    {{ $order->interval }} Tage
                                                @endif
                                            </td>
                                            <td>{{ str_replace('UPGRADE', 'Upgrade', str_replace('DOWNGRADE', 'Downgrade', str_replace('NEW', 'Neu', str_replace('RENEW', 'Erneuert', $order->type)))) }}</td>
                                            <td>{{ $order->created_at->format('d.m.Y H:i:s') }}</td>
                                            <td>{{ $order->updated_at->format('d.m.Y H:i:s') }}</td>
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
