@extends('layouts.app')

@section('content')
    <div class="container col-md-12">
        <div class="content mt-3">

            <div class="animated fadeIn">

                <div class="row">

                    <div class="col-sm-10">
                        <div class="card" style="border-bottom-right-radius: 50px;">
                            <div class="card-body">
                                <a type="button" class="btn btn-primary col-xs-2" href="{{ route('reseller.webspaces.index') }}" style="margin: 15px">Zurück</a>
                                <a type="button" class="btn btn-primary col-xs-2" href="{{ route('reseller.webspace.single.automaticLogin', ['webspace' => $webspace]) }}" style="margin: 15px" target="_blank">Einloggen</a>

                                <div class="card-block" style="margin: 50px">
                                    <h3 style="margin-top: 5px"><strong>Infomationen</strong></h3>
                                    <span>Webspace: <strong>{{ $webspace->id }}</strong></span><br>
                                    <span>Plan: <strong>{{ $webspace->plan }}</strong></span><br>
                                    <span>Domainname: <strong>{{ $webspace->plesk_url }}</strong></span><br>
                                    <span>Benutzer: <strong>{{ $webspace->plesk_username }}</strong></span><br>
                                    <span>Passwort: <strong> <?php try { echo decrypt($webspace->plesk_password); } catch (Exception $exception) { echo $webspace->plesk_password; } ?> </strong></span><br>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
