# TicketsValdi

Proyecto modular de **venta y validación de entradas para eventos** sobre la plataforma
multi-tenant. Cada tenant con `current_project = "tickets-valdi"` corre esta aplicación en
su propio schema de base de datos.

Una institución publica eventos; cada evento ofrece tipos de entrada con precio y capacidad;
un cliente arma una compra, paga por una pasarela externa y recibe tickets con QR; en la
puerta, un validador escanea el QR y registra el check-in.

## Table of contents

| Documento | Responde |
|---|---|
| **README.md** (este archivo) | ¿Qué es, cómo lo levanto, en qué estado está? |
| [ARCHITECTURE.md](./ARCHITECTURE.md) | ¿Cómo está construido y dónde se engancha con la plataforma? |
| [DOMAIN.md](./DOMAIN.md) | ¿Cuáles son las reglas de negocio y el modelo de datos? |
| [decisions/](./decisions/) | ¿Por qué se decidió así? (ADRs) |
| [`../CLAUDE.md`](../CLAUDE.md) | Instrucciones para agentes que editan este proyecto |

## Overview

| | |
|---|---|
| Prefijo del proyecto | `tickets-valdi` |
| Título visible | `Tickets Valdi` |
| Clase principal | `App\Projects\TicketsValdi\TicketsValdiProject` |
| Guard de autenticación | `web` (usuarios dentro del schema del tenant) |
| Migraciones propias | `database/migrations/projects/TicketsValdi/` (vacía hoy) |
| Traducciones | `lang/projects/tickets-valdi/{es,en,pt}/messages.php` |
| Vistas | `resources/views/tickets-valdi/` |

## Quick start

```bash
# Levantar el entorno completo (server + queue + logs + vite)
composer dev

# Crear un tenant de este proyecto: desde el admin del Landlord,
# el selector "Proyecto" ofrece "Tickets Valdi".
# El alta dispara MigrateProjectDatabase, que corre:
#   1. database/migrations/projects/Common/
#   2. database/migrations/projects/TicketsValdi/   (hoy vacía)

# Acceder: http://{subdominio}.localhost/tickets-valdi/auth/login

# Tests del proyecto
php artisan test tests/Feature/TicketsValdiProjectTest.php

# Ver las rutas registradas
php artisan route:list | grep tickets-valdi
```

## Current status

**Estado general: esquema de datos completo, lógica de negocio sin implementar.**

El proyecto se selecciona, se instancia, autentica usuarios y expone un ABM de usuarios.
El dominio de ticketing está modelado en [DOMAIN.md](./DOMAIN.md) y **sus 11 migraciones ya
existen**. Faltan los modelos, servicios y pantallas: hoy las tablas se crean vacías y
ningún código las usa.

### Implemented

| Capacidad | Estado | Dónde |
|---|---|---|
| Selección del proyecto al crear/editar tenant | ✅ | `ProjectManager`, `TenantFormRequest` |
| Registro de rutas (central y tenant) | ✅ | `routes/web.php`, `routes/tenant.php` |
| Login / logout | ✅ | `Http/Controller/Auth/AuthController` |
| ABM de usuarios (list, create, edit, delete) | ✅ | `Adapters/Admin/UserAdmin` |
| Menú lateral | ✅ | `Services/MenuBuilderService` (solo item *Usuarios*) |
| Traducciones es / en / pt | ✅ | `lang/projects/tickets-valdi/` |
| Migraciones del dominio (11 tablas) | ✅ | `database/migrations/projects/TicketsValdi/` |
| Alcance por institución en `users` | ✅ columna creada | [ADR 0005](./decisions/0005-institution-scope-on-platform-users.md) |

### Not implemented

| Faltante | Bloqueante para | Notas |
|---|---|---|
| Modelos Eloquent de las 11 tablas | Todo | Las tablas existen, ningún modelo las mapea |
| Repositorios, servicios y adapters de admin | ABM del dominio | Seguir el patrón de `UserAdmin` |
| Liberación de órdenes vencidas | Aforo real | Deuda técnica consciente: BR-12b. El cupo se reserva al crear la orden y hoy no se libera nunca |
| Backed enums (`EventStatus`, `OrderStatus`, `PaymentStatus`, `TicketStatus`, `SeatingType`) | Modelos | Definidos en [DOMAIN.md](./DOMAIN.md#enumerations) |
| Roles de spatie (`super_admin`, `institution_admin`, `validator`) + seeder | Permisos | [ADR-0004](./decisions/0004-reuse-platform-users-for-admins.md) |
| Scoping por institución | Aislamiento entre instituciones de un tenant | BR-2; depende de la pregunta 1 |
| Integración con la pasarela de pago | Cobro | Proveedor sin confirmar (pregunta 4) |
| Generación y validación de QR | Check-in | `qr_code` + `qr_secret`; ver BR-14 |
| Asignación de roles en el alta de usuarios | Permisos por rol | `UserFormRequest` no expone el campo `role` |
| Suplantación de usuarios | Soporte / debugging | Seguir el patrón de ActivitiesBoard (`web` + `auth.tenant`) |
| API REST | Front público / app de validación | Requiere sumar el proyecto a `routes/api-auth.php` |

### Known issues

- `TenantFilterTest` falla al correr la suite completa y pasa en aislamiento
  (contaminación de estado estático entre tests). **Es preexistente**, no lo introdujo
  este proyecto — verificado contra el árbol sin estos cambios.
- El archivo `testing` (base SQLite versionada en git) se modifica al correr los tests.
  Restaurar con `git checkout -- testing` antes de commitear.

## Tests

`tests/Feature/TicketsValdiProjectTest.php` cubre el **contrato de plataforma** del
proyecto, no su lógica de negocio (que todavía no existe):

- registro en `ProjectManager` y resolución por prefijo
- presencia en `config/projects.php`
- existencia de las carpetas de migraciones, docs y traducciones
- generación de endpoints desde los atributos
- que cada endpoint generado esté efectivamente registrado en el router

Ese último test es el que atrapa el error más común al sumar controllers: olvidar
`routes/tenant.php`. Si agregás un controller, el test lo cubre solo — no hace falta
tocarlo.
