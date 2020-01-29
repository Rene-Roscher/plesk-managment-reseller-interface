<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>ProHosting24 | Reseller</title>
    <meta name="description" content="Sufee Admin - HTML5 Admin Template">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="{{ asset('css/normalize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/flag-icon.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cs-skin-elastic.css') }}">
    <link rel="stylesheet" href="{{ asset('scss/style.css') }}">
    <link href="{{ asset('css/lib/vector-map/jqvmap.min.css') }}" rel="stylesheet">

    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800' rel='stylesheet' type='text/css'>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="{{ asset('js/jquery.countdown.min.js') }}" type="text/javascript"></script>
</head>

<body class="bg-dark">

@yield('content')

</body>

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
            title: "Fehler",
            text: "<?php foreach ($errors->all() as $error) { echo $error; } ?>",
            icon: "error",
        });
    </script>
@endif


<script src="{{ asset('js/vendor/jquery-2.1.4.min.js') }}"></script>
<script src="{{ asset('assets/js/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins.js') }}"></script>
<script src="{{ asset('assets/js/main.js') }}"></script>

</html>