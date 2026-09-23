<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name', 'Laravel'))</title>
</head>
<body>
@include('components.dashboard-navbar')

@yield('content')

@include('components.footer')
</body>
</html>