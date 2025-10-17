<div id="topbar-content">
    {{-- Contenido del topbar hidratado por View Composer --}}
    <h1>{{ $topbarData['title'] ?? 'Barra superior' }}</h1>
    
    @if(isset($topbarData['user']))
        <div class="user-info">
            <span>{{ $topbarData['user']->name ?? 'Usuario' }}</span>
        </div>
    @endif
    
    @if(isset($topbarData['notifications']) && count($topbarData['notifications']) > 0)
        <div class="notifications">
            <span>{{ count($topbarData['notifications']) }} notificaciones</span>
        </div>
    @endif
</div>
