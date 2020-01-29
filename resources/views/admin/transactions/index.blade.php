@extends('layouts.admin')

@section('content')
    <div class="">
        <div class="animated fadeIn">
            <div class="row" style="margin: 50px">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">

{{--                            {{ $transactions->where('type', '!=', 'INTERN')->where('type', 'SUCCESS')->whereBetween('created_at', [\Carbon\Carbon::now()->startOfDay(), \Carbon\Carbon::now()->endOfDay()])->sum('amount') }}--}}

                            <table id="table" class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Reseller</th>
                                    <th>Betrag</th>
                                    <th>Art</th>
                                    <th>Typ</th>
                                    <th>Status</th>
                                    <th>MTID</th>
                                    <th>Erstelldatum</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($transactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->id }}</td>
                                        <td><a href="{{ route('admin.resellers.single.index', ['user' => $transaction->user]) }}">{{ $transaction->user->name.' #'.$transaction->user->id }}</a></td>
                                        <td>{{ \App\Helper\FormatHelper::money($transaction->amount) }}</td>
                                        <td>{{ $transaction->type }}</td>
                                        <td>{{ str_replace('API', 'Externe Transaktion', str_replace('OWN', 'Interne Transaktion', $transaction->typ)) }}</td>
                                        <td>{{ $transaction->state }}</td>
                                        <td>{{ $transaction->mtid }}</td>
                                        <td>{{ $transaction->created_at->format('d.m.Y H:i:s') }}</td>
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
