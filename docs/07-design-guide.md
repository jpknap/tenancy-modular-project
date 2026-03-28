# 🎨 Guía de Diseño - Bootstrap 5.3

## 📋 Características Implementadas

### ✅ Layout Profesional
- **Sidebar** oscuro con navegación moderna
- **Header/Topbar** con menú de usuario y notificaciones
- **Responsive** completamente funcional (mobile-first)
- **Toggle sidebar** para móviles con overlay
- **Footer** profesional

### 🎨 Paleta de Colores
```css
--sidebar-bg: #1e293b      /* Gris oscuro del sidebar */
--sidebar-hover: #334155   /* Hover en menú */
--sidebar-active: #3b82f6  /* Item activo (azul) */
--header-height: 70px      /* Altura del header */
```

### 🧩 Componentes Personalizados

#### 1. Content Card
Tarjeta de contenido con diseño profesional:
```html
<div class="content-card">
    <!-- Tu contenido aquí -->
</div>
```

#### 2. Stats Cards (Métricas)
```html
<div class="content-card">
    <div class="d-flex align-items-center">
        <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
            <i class="bi bi-building text-primary fs-4"></i>
        </div>
        <div>
            <h6 class="text-muted mb-1 small">Título</h6>
            <h3 class="mb-0 fw-bold">123</h3>
        </div>
    </div>
</div>
```

#### 3. Sidebar Navigation
```html
<nav class="sidebar-nav">
    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="#" class="nav-link active">
                <i class="bi bi-house-door-fill"></i>
                <span>Inicio</span>
            </a>
        </li>
    </ul>
</nav>
```

## 🚀 Clases de Bootstrap 5.3 Más Usadas

### Layout & Grid
- `container` / `container-fluid` - Contenedores responsivos
- `row` - Fila del grid
- `col-{breakpoint}-{size}` - Columnas (ej: `col-md-6`)
- `g-3` - Gap/espaciado en grids

### Flexbox Utilities
- `d-flex` - Display flex
- `justify-content-{start|center|end|between}` - Alineación horizontal
- `align-items-{start|center|end}` - Alineación vertical
- `gap-{1-5}` - Espaciado entre elementos flex

### Spacing (Margin & Padding)
- `m-{0-5}` / `mt-{0-5}` / `mb-{0-5}` - Margin top/bottom
- `p-{0-5}` / `px-{0-5}` / `py-{0-5}` - Padding x/y
- `ms-auto` / `me-auto` - Margin start/end auto

### Typography
- `h1` - `h6` - Headings
- `fw-{light|normal|medium|semibold|bold}` - Font weight
- `text-{muted|primary|danger|success}` - Colores de texto
- `fs-{1-6}` - Font size

### Buttons
- `btn btn-primary` - Botón primario
- `btn btn-outline-secondary` - Botón outline
- `btn-sm` / `btn-lg` - Tamaños
- `btn-group` - Grupo de botones

### Cards & Components
- `card` - Tarjeta
- `badge bg-primary` - Badge/etiqueta
- `dropdown` - Menú desplegable
- `table table-hover` - Tabla con hover
- `pagination` - Paginación

### Colors
- `bg-{primary|secondary|success|danger|warning|info|light|dark}`
- `text-{primary|secondary|success|danger|warning|info|light|dark}`
- `bg-opacity-{10|25|50|75}` - Opacidad del background

### Display & Visibility
- `d-{none|block|flex|inline}` - Display
- `d-{breakpoint}-{value}` - Responsive (ej: `d-md-none`)
- `visually-hidden` - Ocultar visualmente (accesibilidad)

## 🎯 Iconos de Bootstrap Icons

Ya incluidos en el layout. Uso:
```html
<i class="bi bi-house-door-fill"></i>
<i class="bi bi-gear"></i>
<i class="bi bi-person-circle"></i>
```

**Iconos comunes:**
- `bi-house-door-fill` - Home
- `bi-gear-fill` - Configuración
- `bi-bell` - Notificaciones
- `bi-person` - Usuario
- `bi-box-arrow-right` - Logout
- `bi-search` - Búsqueda
- `bi-plus-circle` - Agregar
- `bi-trash` - Eliminar
- `bi-pencil` - Editar
- `bi-eye` - Ver

[🔗 Ver todos los iconos](https://icons.getbootstrap.com/)

## 📱 Responsive Breakpoints

```scss
// Extra small (xs) - < 576px (default)
// Small (sm) - ≥ 576px
// Medium (md) - ≥ 768px
// Large (lg) - ≥ 992px
// Extra large (xl) - ≥ 1200px
// Extra extra large (xxl) - ≥ 1400px
```

Ejemplo de uso:
```html
<div class="col-12 col-md-6 col-lg-4">
    <!-- 12 columnas en mobile, 6 en tablet, 4 en desktop -->
</div>
```

## 🛠️ Desarrollo

### Compilar assets en desarrollo (con hot reload):
```bash
./vendor/bin/sail npm run dev
```

### Compilar assets para producción:
```bash
./vendor/bin/sail npm run build
```

## 📚 Ejemplos de Uso

### Página típica
```blade
@extends('layouts.layout_menu_sidebar')

@section('title', 'Mi Página')

@section('content')
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-1 fw-bold">Título</h2>
            <p class="text-muted mb-0">Descripción</p>
        </div>
        <button class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Nuevo
        </button>
    </div>

    {{-- Contenido --}}
    <div class="content-card">
        <!-- Tu contenido -->
    </div>
@endsection
```

### Formulario
```html
<div class="content-card">
    <form>
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre">
        </div>
        
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email">
        </div>
        
        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
</div>
```

## 📖 Documentación Oficial

- [Bootstrap 5.3 Docs](https://getbootstrap.com/docs/5.3/)
- [Bootstrap Icons](https://icons.getbootstrap.com/)
- [Bootstrap Examples](https://getbootstrap.com/docs/5.3/examples/)

## 🎨 Personalización

Los colores y estilos personalizados están en:
- `resources/css/app.scss` - Estilos globales y variables

Para cambiar colores del sidebar:
```scss
:root {
    --sidebar-bg: #1e293b;      // Color de fondo
    --sidebar-hover: #334155;   // Color hover
    --sidebar-active: #3b82f6;  // Color activo
}
```
