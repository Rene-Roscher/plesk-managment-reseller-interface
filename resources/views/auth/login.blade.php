@extends('layouts.website')

@section('content')
    <div class="sufee-login d-flex align-content-center flex-wrap">
        <div class="container">
            <div class="login-content">

                <div class="login-logo">
                    <a href="{{ url('/') }}">
                        <img class="align-content" src="https://prohosting24.de/img/logo.png" height="240" width="240" alt="">
                    </a>
                </div>

                <div class="login-form" style="border-bottom-left-radius: 50px;border-top-right-radius: 50px">
                    <h4><strong>Login</strong></h4>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-group">
                            <label>E-Mail</label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="E-Mail" required>
                        </div>

                        <div class="form-group">
                            <label>Passwort</label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Passwort" required>
                        </div>

                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="remember" id="remember"> Eingeloggt bleiben
                            </label>
                            <label class="pull-right">
                                <a href="{{ route('password.request') }}">Passwort Vergessen?</a>
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary btn-flat m-b-30 m-t-30" style="border-bottom-left-radius: 75px;border-top-right-radius: 75px;">Einloggen</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection

