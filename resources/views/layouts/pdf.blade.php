@extends('layouts.app')
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
    <style>
        table {border-collapse: collapse;}
        table td {padding: 0px}
        span a {
            font-style:normal;
            font-weight:bold;
            font-size:12pt;
            font-family:"Helvetica Neue";
            color:#545454;
        }
        span b {
            font-style:normal;
            font-weight:bold;
            font-size:12pt;
            font-family:"Helvetica Neue";
            color:#555555;
        }
    </style>
</head>
    <body>
        @section('content')
            @yield('content')
        @endsection
    </body>
</html>