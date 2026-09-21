<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    @include('includes.head')

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-600 antialiased">

    @include('includes.header')


    <!-- Main Content Start -->

    {{ $slot }}

    <!-- Main Content End -->

    @include('includes.footer')

    @include('includes.script')

</body>

</html>