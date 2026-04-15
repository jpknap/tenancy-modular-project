## backlog
- [ ] #023 | Log de auditoría de suplantaciones | landlord | low | user-impersonation | Tabla central impersonation_logs, vista en landlord admin
- [ ] #029 | UI permisos extra al rol admin (fase 2) | core | low | permission-system | Vista /admin/settings/permissions, syncPermissions por rol
- [ ] #010 | Helper TimezoneDisplay service | core | medium | timezone-config | display(Carbon $date, string $format): string con timezone del usuario activo
- [ ] #011 | Blade directive @displayDate | core | medium | timezone-config | Registrar en AppServiceProvider, uso en vistas
- [ ] #012 | Campo timezone en formularios de usuario | core | medium | timezone-config | Select con timezone_identifiers_list() en UserFormRequest
- [ ] #013 | Sección Configuración General admin (timezone) | core | medium | timezone-config | Compartida con language-config, campo timezone de tenant
- [ ] #014 | Input UTC en entidades con fechas | activities-board | medium | timezone-config | Conversión UTC en ActivityFormRequest y similares

## todo

## in-progress
- [ ] #024 | Instalar spatie/laravel-permission + migraciones tenant | core | high | permission-system | Tablas roles/permissions en schema de cada tenant
- [ ] #025 | Seeders roles y permisos base por tenant | core | high | permission-system | Roles superadmin/admin/user + permisos users:* y settings:*
- [ ] #026 | HasRoles en User models + Gate::before superadmin | core | high | permission-system | Trait HasRoles, canImpersonate(), canBeImpersonated(), Gate::before
- [ ] #027 | Middleware CheckPermission en rutas admin | core | medium | permission-system | Aliases permission/role en bootstrap/app.php
- [ ] #028 | UI asignación de roles a usuarios | core | medium | permission-system | Campo role en formulario usuario, solo visible para superadmin

## done
- [x] #001 | Auth Landlord base compatible multi-proyecto | landlord | high | core-auth | BaseAuthController, guard landlord, EnsureAuthenticated
- [x] #002 | Logout base | core | high | core-auth | Logout via BaseAuthController, destruye sesión y redirige
- [x] #003 | Form edición perfil usuario | core | high | core-auth | ProfileFormRequest, iteración de guards, edición de nombre/email/password
- [x] #004 | Migración campo locale en users y tenants | core | high | language-config | locale VARCHAR(10) en users central, tenant y tabla tenants
- [x] #005 | Middleware SetLocale | core | high | language-config | usuario→tenant→config fallback, registrado después de InitializeTenancyByDomain
- [x] #006 | Sección Configuración General admin (locale) | landlord | medium | language-config | SettingsController, GeneralSettingsFormRequest, locale de tenant
- [x] #007 | Campo locale en formulario de usuario | core | medium | language-config | Select es/en en UserFormRequest de cada proyecto
- [x] #008 | Archivos de traducción es/en | core | high | language-config | lang/es/ y lang/en/ para validation, auth, pagination y app.php
- [x] #009 | Migración campo timezone en users y tenants | core | high | timezone-config | timezone VARCHAR(50) en migraciones existentes centrales y tenant
- [x] #015 | Campo is_system_user en create_users_table | core | high | user-impersonation | Agregado directamente en la migración de creación + fillable en User models
- [x] #016 | Token HMAC para acceso cross-domain al tenant | landlord | high | user-impersonation | HMAC-SHA256 firmado con app.key, TTL 2min, one-time use via Cache
- [x] #017 | Crear system_user automático al crear tenant | landlord | high | user-impersonation | TenantSystemUserSeeder en setupDefaultSettings() post RolesAndPermissionsSeeder
- [x] #018 | Acción "Acceder al Tenant" en lista de tenants | landlord | high | user-impersonation | TenantAccessController + ListAction target=_blank + verificación superadmin
- [x] #019 | Endpoint /system-login en tenant | core | high | user-impersonation | SystemLoginController, valida HMAC+exp+one-time-use+is_system_user, Auth::loginUsingId
- [x] #020 | Vista selección de usuario para suplantar | core | high | user-impersonation | Acción Suplantar en UserAdmin con condition callable, solo visible para system_user
- [x] #021 | Impersonation start/stop en tenant | core | high | user-impersonation | ImpersonationController + StopImpersonationController, session system_impersonator_id
- [x] #022 | Banner visual de suplantación activa | core | medium | user-impersonation | Banner rojo en layout con route dinámico via ProjectManager
