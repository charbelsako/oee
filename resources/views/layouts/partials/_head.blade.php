<head>
    <base href="" />
    <title>OEE</title>
    <meta charset="utf-8" />
    <meta name="description" content="OEE" />
    <meta name="keywords" content="metronic, , " />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="cms" />
    <meta property="og:title" content="OEE" />
    <meta property="og:url" content="https://oee.com" />
    <meta property="og:site_name" content="OEE" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="canonical" href="https://oee.com" />
    <link rel="shortcut icon" href="" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link href="{{ asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/toast/jquery.toast.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@7.29.2/dist/sweetalert2.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@7.29.2/dist/sweetalert2.css" rel="stylesheet">
    @yield('css')
    @stack('css')
    @yield('js')
</head>
