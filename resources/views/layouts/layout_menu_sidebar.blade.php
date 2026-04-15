<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Dashboard')</title>
    
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    {{-- Vite Assets --}}
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<body>

<!-- Sidebar Overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    @include('partials.sidebar-menu')
</aside>

<!-- Contenedor principal -->
<div class="main-container">
    <!-- Header -->
    <header class="header">
        @include('partials.top-bar')
    </header>

    <!-- Contenido -->
    <main class="main-content">
        <div class="container-fluid">
            @if(session('impersonator_id'))
                @php
                    $stopRoute = \App\ProjectManager::getCurrentProject()?->getPrefix() . '.admin.users.stop-impersonation';
                @endphp
                <div class="alert alert-warning d-flex align-items-center justify-content-between mb-3 border border-warning shadow-sm" role="alert">
                    <div>
                        <i class="bi bi-person-fill-gear me-2"></i>
                        <strong>Modo suplantación:</strong>
                        Estás actuando como <strong>{{ auth()->user()?->name }}</strong>.
                    </div>
                    @if(\Illuminate\Support\Facades\Route::has($stopRoute))
                        <form method="POST" action="{{ route($stopRoute) }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-warning fw-semibold">
                                <i class="bi bi-box-arrow-left me-1"></i>Detener suplantación
                            </button>
                        </form>
                    @endif
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <span>&copy; {{ date('Y') }} {{ config('app.name', 'Tu Aplicación') }}. Todos los derechos reservados.</span>
                </div>
                <div class="col-md-6 text-md-end">
                    @yield('footer')
                </div>
            </div>
        </div>
    </footer>
</div>

{{-- Alert System --}}
<x-alert />

{{-- Sidebar Toggle Script --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const toggleBtn = document.getElementById('sidebarToggle');
        
        // Toggle sidebar
        toggleBtn?.addEventListener('click', function() {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        });
        
        // Close sidebar when clicking overlay
        overlay?.addEventListener('click', function() {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
        
        // Close sidebar on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            }
        });
    });
</script>

{{-- Scripts Stack --}}
@stack('scripts')

</body>
</html>
