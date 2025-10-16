<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="body" style="background-color: yellow">

<!-- Sidebar -->
<aside class="sidebar" style="background-color: red">
    @yield('side-menu')
</aside>

<!-- Contenedor principal -->
<div class="main-container" style="background-color: green">
    <!-- Header -->
    <header class="header">
        @yield('top-bar')
    </header>

    <!-- Contenido -->
    <main class="main-content">
        <div style="height: 130vh">
            @yield('content')
        </div>
    </main>

    <footer class="footer" style="background-color: blue">
        @yield('footer')
    </footer>
</div>
</body>
</html>
