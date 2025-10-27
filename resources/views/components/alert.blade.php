@php
    $alerts = [
        'success' => [
            'icon' => 'bi-check-circle-fill',
            'color' => 'success',
            'title' => '¡Éxito!'
        ],
        'error' => [
            'icon' => 'bi-x-circle-fill',
            'color' => 'danger',
            'title' => 'Error'
        ],
        'warning' => [
            'icon' => 'bi-exclamation-triangle-fill',
            'color' => 'warning',
            'title' => 'Advertencia'
        ],
        'info' => [
            'icon' => 'bi-info-circle-fill',
            'color' => 'info',
            'title' => 'Información'
        ],
    ];
@endphp

<div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
    @foreach($alerts as $type => $config)
        @if(session()->has("alert_{$type}"))
            @php
                $alert = session("alert_{$type}");
                $message = is_array($alert) ? $alert['message'] : $alert;
                $title = is_array($alert) ? ($alert['title'] ?? $config['title']) : $config['title'];
            @endphp

            <div class="toast align-items-center border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-start p-3 bg-white">
                        <div class="d-flex align-items-center justify-content-center bg-{{ $config['color'] }} bg-opacity-10 rounded-circle me-3"
                             style="width: 40px; height: 40px; flex-shrink: 0;">
                            <i class="{{ $config['icon'] }} text-{{ $config['color'] }} fs-5"></i>
                        </div>
                        <div class="flex-grow-1">
                            <strong class="d-block mb-1 text-dark">{{ $title }}</strong>
                            <div class="text-muted small">{{ $message }}</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        @endif
    @endforeach
</div>

<style>
    .toast {
        min-width: 350px;
        max-width: 450px;
        background: white;
        backdrop-filter: blur(10px);
        animation: slideInRight 0.3s ease-out;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .toast-body {
        background: white;
        border-radius: 8px;
    }

    .btn-close-white {
        filter: none;
        opacity: 0.5;
    }

    .btn-close-white:hover {
        opacity: 1;
    }
</style>

@push('scripts')
<script>
    // Inicializar toasts inmediatamente después de que el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initToasts);
    } else {
        initToasts();
    }

    function initToasts() {
        setTimeout(() => {
            const toastElements = document.querySelectorAll('#toast-container .toast');

            if (toastElements.length > 0) {
                console.log('Inicializando', toastElements.length, 'toast(s)');

                toastElements.forEach((toastEl, index) => {
                    try {
                        if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
                            const toast = new bootstrap.Toast(toastEl, {
                                autohide: true,
                                delay: 5000
                            });

                            // Mostrar con un pequeño delay entre cada uno
                            setTimeout(() => {
                                toast.show();
                                console.log('Toast', index + 1, 'mostrado');
                            }, index * 100);
                        } else {
                            console.error('Bootstrap Toast no disponible');
                        }
                    } catch (error) {
                        console.error('Error al inicializar toast:', error);
                    }
                });
            }
        }, 100); // Pequeño delay para asegurar que Bootstrap esté cargado
    }
</script>
@endpush
