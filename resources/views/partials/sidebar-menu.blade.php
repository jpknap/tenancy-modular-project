<div id="sidebar-content">
    {{-- Brand / Logo --}}
    <div class="sidebar-brand">
        <h4>{{ $menuBuilder->title }}</h4>
        <small>Dashboard</small>
    </div>

    {{-- Navigation Menu --}}
    <nav class="sidebar-nav">
        <ul class="nav flex-column">
            @foreach($menuBuilder->items as $item)
                <li class="nav-item">
                    <a href="{{ $item->url }}"
                       class="nav-link {{ request()->url() === $item->url ? 'active' : '' }}">
                        <i class="bi bi-circle-fill"></i>
                        <span>{{ $item->label }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>

    {{--
    <div class="sidebar-section">Configuración</div>
    <nav class="sidebar-nav">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-gear-fill"></i>
                    <span>Ajustes</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Cerrar sesión</span>
                </a>
            </li>
        </ul>
    </nav>
    --}}
</div>
