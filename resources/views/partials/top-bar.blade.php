{{-- Toggle button for mobile --}}
<button class="sidebar-toggle" id="sidebarToggle" type="button">
    <i class="bi bi-list fs-4"></i>
</button>

<div id="topbar-content" class="d-flex justify-content-between align-items-center w-100">
    {{-- Page Title --}}
    <div class="topbar-left">
        <h1 class="h5 mb-0 fw-semibold text-dark">{{ $topbarData['title'] ?? 'Dashboard' }}</h1>
    </div>
    
    {{-- User Actions --}}
    <div class="topbar-right d-flex align-items-center gap-3">
        @if(isset($topbarData['notifications']) && count($topbarData['notifications']) > 0)
            <div class="dropdown">
                <button class="btn btn-light position-relative" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-bell fs-5"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        {{ count($topbarData['notifications']) }}
                        <span class="visually-hidden">notificaciones</span>
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><h6 class="dropdown-header">Notificaciones</h6></li>
                    @foreach(array_slice($topbarData['notifications'], 0, 5) as $notification)
                        <li><a class="dropdown-item" href="#">{{ $notification }}</a></li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        @if(isset($topbarData['user']))
            <div class="dropdown">
                <button class="btn btn-light d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                         style="width: 32px; height: 32px; font-size: 0.875rem; font-weight: 600;">
                        {{ strtoupper(substr($topbarData['user']->name ?? 'U', 0, 1)) }}
                    </div>
                    <span class="d-none d-md-inline fw-medium">{{ $topbarData['user']->name ?? 'Usuario' }}</span>
                    <i class="bi bi-chevron-down small"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Perfil</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Configuración</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</a></li>
                </ul>
            </div>
        @endif
    </div>
</div>
