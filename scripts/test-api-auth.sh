#!/usr/bin/env bash
# Prueba funcional manual del login API (Sanctum) contra un entorno Sail levantado.
# Requiere: vendor/bin/sail up -d, DB seedeada (php artisan db:seed), curl, jq.
#
# Uso:
#   ./scripts/test-api-auth.sh                # usa admin.localhost + credenciales del seed
#   BASE_URL=http://otro.localhost EMAIL=... PASSWORD=... ./scripts/test-api-auth.sh

set -euo pipefail

BASE_URL="${BASE_URL:-http://admin.localhost}"
PREFIX="${PREFIX:-landlord}" # "landlord" en central, "activities-board" en un tenant
EMAIL="${EMAIL:-test@example.com}"
PASSWORD="${PASSWORD:-password}"

AUTH_URL="${BASE_URL}/${PREFIX}/api/auth"

echo "== Login (${AUTH_URL}/login) =="
LOGIN_RESPONSE=$(curl -s -X POST "${AUTH_URL}/login" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d "{\"email\":\"${EMAIL}\",\"password\":\"${PASSWORD}\"}")
echo "$LOGIN_RESPONSE" | jq .

TOKEN=$(echo "$LOGIN_RESPONSE" | jq -r '.token')
if [ "$TOKEN" = "null" ] || [ -z "$TOKEN" ]; then
  echo "Login falló, no se obtuvo token." >&2
  exit 1
fi

echo
echo "== Me (con token) =="
curl -s "${AUTH_URL}/me" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer ${TOKEN}" | jq .

echo
echo "== Logout =="
curl -s -i -X POST "${AUTH_URL}/logout" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer ${TOKEN}" | head -1

echo
echo "== Me después de logout (esperado 401) =="
curl -s -o /dev/null -w "HTTP %{http_code}\n" "${AUTH_URL}/me" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer ${TOKEN}"

echo
echo "== Login con password incorrecta (esperado 401) =="
curl -s -o /dev/null -w "HTTP %{http_code}\n" -X POST "${AUTH_URL}/login" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d "{\"email\":\"${EMAIL}\",\"password\":\"wrong-password\"}"

# Nota ActivitiesBoard (tenant): no hay tenant seedeado por defecto en este repo
# (TenantsSeeder está deshabilitado, "los tenants se crean desde el admin").
# Para probar ese guard, creá un tenant desde /landlord/admin/tenants y corré:
#   BASE_URL=http://<dominio-del-tenant> PREFIX=activities-board \
#     EMAIL=test@example.com PASSWORD=password ./scripts/test-api-auth.sh
