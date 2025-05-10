<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Pinterest Clone') }} - @yield('title', 'Home')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('styles')
</head>

<body class="layout-container">
    @include('layouts.header')

    <!-- Mini Popup Notification -->
    @if (session('success'))
        <x-notification type="success" :message="session('success')" />
    @endif
    @if (session('error'))
        <x-notification type="error" :message="session('error')" />
    @endif
    <!-- End Mini Popup Notification -->

    <!-- Main Content -->
    <main class="container mx-auto px-4 pt-20 pb-10">
        @yield('content')
    </main>

    @include('layouts.footer')

    @yield('scripts')
</body>

</html>
