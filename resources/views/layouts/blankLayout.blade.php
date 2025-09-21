<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <link rel="icon" href="{{ asset('assets/img/logo-laundry.png') }}" type="image/png" width="100px">

    <title>@yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Styles --}}
    @include('layouts.sections.styles')
</head>

<body class="blank-layout">
    @yield('content')

    {{-- Scripts --}}
    @include('layouts.sections.scripts')
    @stack('scripts')
</body>

</html>
