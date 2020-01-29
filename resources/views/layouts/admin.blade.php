<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <title>ProHosting24 - Reseller</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Sufee Admin - HTML5 Admin Template">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="{{ url('/css/normalize.css') }}">
    <link rel="stylesheet" href="{{ url('/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ url('/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ url('/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ url('/css/flag-icon.min.css') }}">
    <link rel="stylesheet" href="{{ url('/css/cs-skin-elastic.css') }}">
    <link rel="stylesheet" href="{{ url('/css/lib/datatable/dataTables.bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ url('/scss/style.css') }}">
    <script src="https://code.jquery.com/jquery-2.2.0.min.js" type="text/javascript"></script>
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800' rel='stylesheet' type='text/css'>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="{{ asset('js/jquery.countdown.min.js') }}" type="text/javascript"></script>
</head>

<body>

<aside id="left-panel" class="left-panel">
    <nav class="navbar navbar-expand-sm navbar-default">
        <h3 class="menu-title"></h3>
        <div class="navbar-header">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main-menu" aria-controls="main-menu" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fa fa-bars"></i>
            </button>
            <a class="navbar-brand" style="margin: 25px"><img src="https://prohosting24.de/img/logo.png" alt="Logo"></a>
        </div>

        <div id="main-menu" class="main-menu collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li @if(Route::current()->getName() == 'admin.dashboard') class="active" @endif>
                    <a href="{{ url('/admin') }}"> <i class="menu-icon fa fa-dashboard"></i>Dashboard </a>
                </li>

                <li @if(Route::current()->getName() == 'admin.resellers.index') class="active" @endif>
                    <a href="{{ url('/admin/resellers') }}"> <i class="menu-icon fa fa-users"></i>Resellers </a>
                </li>

                <li @if(Route::current()->getName() == 'admin.api.index') class="active" @endif>
                    <a href="{{ url('/admin/api') }}"> <i class="menu-icon fa fa-wifi"></i>Api </a>
                </li>

                <li @if(Route::current()->getName() == 'admin.apioptions.index') class="active" @endif>
                    <a href="{{ url('/admin/apioptions') }}"> <i class="menu-icon fa fa-align-center"></i>ApiOptions </a>
                </li>

                <li @if(Route::current()->getName() == 'admin.apilogs.index') class="active" @endif>
                    <a href="{{ url('/admin/apilogs') }}"> <i class="menu-icon fa fa-list-ul"></i>ApiLogs </a>
                </li>

                <li @if(Route::current()->getName() == 'admin.transactions.index') class="active" @endif>
                    <a href="{{ url('/admin/transactions') }}"> <i class="menu-icon fa fa-dashboard"></i>Transaktionen </a>
                </li>

                <li class="active">
                    <a href="{{ url('reseller') }}"> <i class="menu-icon fa fa-undo"></i>Reseller </a>
                </li>

            </ul>
        </div>
    </nav>
</aside><!-- /#left-panel -->

<!-- Left Panel -->

<!-- Right Panel -->

<div id="right-panel" class="right-panel">

    <header id="header" class="header">

        <div class="header-menu">

            <div class="col-sm-12">
                <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();" class="pull-right">Abmelden</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>

        </div>

    </header><!-- /header -->
    <!-- Header-->

    @if (\Session::has('success'))
        <script>
            swal({
                title: "Erfolgreich",
                text: "<?php echo Session::get('success'); ?>",
                icon: "success",
            });
        </script>
    @endif
    @if (count($errors) > 0)
        <script>
            swal({
                title: "Error",
                text: "<?php foreach ($errors->all() as $error) { echo $error; } ?>",
                icon: "error",
            });
        </script>
    @endif

    @yield('content')

</div>

</body>

<script src="{{ url('/js/vendor/jquery-2.1.4.min.js') }}"></script>
<script src="{{ url('/js/popper.min.js') }}"></script>
<script src="{{ url('/js/plugins.js') }}"></script>
<script src="{{ url('/js/main.js') }}"></script>


<script src="{{ url('/js/lib/data-table/datatables.min.js') }}"></script>
<script src="{{ url('/js/lib/data-table/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ url('/js/lib/data-table/dataTables.buttons.min.js') }}"></script>
<script src="{{ url('/js/lib/data-table/buttons.bootstrap.min.js') }}"></script>
<script src="{{ url('/js/lib/data-table/jszip.min.js') }}"></script>
<script src="{{ url('/js/lib/data-table/pdfmake.min.js') }}"></script>
<script src="{{ url('/js/lib/data-table/vfs_fonts.js') }}"></script>
<script src="{{ url('/js/lib/data-table/buttons.html5.min.js') }}"></script>
<script src="{{ url('/js/lib/data-table/buttons.print.min.js') }}"></script>
<script src="{{ url('/js/lib/data-table/buttons.colVis.min.js') }}"></script>
<script src="{{ url('/js/lib/data-table/datatables-init.js') }}"></script>