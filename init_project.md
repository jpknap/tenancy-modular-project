# Inicio rápido

## Levantar el proyecto

```bash
# 1. Copiar env (ya tiene la config de Sail/PostgreSQL)
cp .env.local .env

# 2. Instalar dependencias (una sola vez)
composer install
npm install

# 3. Levantar contenedores
./vendor/bin/sail up -d

# 4. Migraciones
./vendor/bin/sail artisan migrate

# 5. Assets
./vendor/bin/sail npm run dev
```

El proyecto queda disponible en `http://localhost`.

---

## Crear un tenant de prueba

```bash
./vendor/bin/sail artisan tinker
```

```php
$tenant = App\Models\Tenant::create([
    'name'            => 'Demo',
    'identifier'      => 'demo',
    'current_project' => 'activities-board',
    'data'            => [],
]);

$tenant->domains()->create(['domain' => 'demo.localhost']);
```

Agregar el dominio en `/etc/hosts`:

```
127.0.0.1   demo.localhost
```

---

## Crear usuarios

### Usuario Landlord (administrador central)

```bash
./vendor/bin/sail artisan tinker
```

```php
App\Projects\Landlord\Models\User::create([
    'name'     => 'Admin',
    'email'    => 'admin@landlord.com',
    'password' => bcrypt('password'),
]);
```

### Usuario del tenant (ActivitiesBoard)

```php
// Primero inicializar el contexto del tenant
$tenant = App\Models\Tenant::first();
tenancy()->initialize($tenant);

App\Projects\ActivitiesBoard\Models\User::create([
    'name'     => 'Usuario Demo',
    'email'    => 'user@demo.com',
    'password' => bcrypt('password'),
]);

tenancy()->end();
```

---

## Navegación sugerida

### Landlord — `http://localhost`

| URL | Descripción |
|-----|-------------|
| `/landlord/auth/login` | Login central |
| `/landlord/admin/tenants/list` | Ver y gestionar tenants |
| `/landlord/admin/tenants/create` | Crear nuevo tenant (genera schema PostgreSQL) |
| `/landlord/admin/users/list` | Usuarios administradores |

### ActivitiesBoard — `http://demo.localhost`

| URL | Descripción |
|-----|-------------|
| `/activities-board/auth/login` | Login del tenant |
| `/activities-board/admin/activities/list` | Listado de actividades |
| `/activities-board/admin/activities/create` | Crear actividad |
| `/activities-board/admin/users/list` | Usuarios del tenant |
