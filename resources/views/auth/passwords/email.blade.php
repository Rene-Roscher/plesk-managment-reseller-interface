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
                    <h4><strong>Passwort Zurücksetzen</strong></h4>
                    <form method="POST" action="{{ route('password.email') }}" aria-label="{{ __('Reset Password') }}">
                        @csrf
                        <div class="form-group">
                            <label>E-Mail</label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="E-Mail" required>
                        </div>
                        @if ($errors->has('email'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif

                        <button type="submit" class="btn btn-primary btn-flat m-b-30 m-t-30" style="border-bottom-left-radius: 75px;border-top-right-radius: 75px;">Zurücksetzen</button>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
