#!/bin/bash

# Script para filtrar y analizar logs de System Auth

if [ ! -f "storage/logs/laravel.log" ]; then
    echo "Error: storage/logs/laravel.log no encontrado"
    exit 1
fi

echo "=== LOGS DE SYSTEM AUTH (Últimos 100) ==="
grep '\[Log-System-Auth\]' storage/logs/laravel.log | tail -100

echo ""
echo "=== CONTEOS POR TIPO ==="
echo "SystemLoginController: $(grep -c 'SystemLoginController::login()' storage/logs/laravel.log)"
echo "EnsureAuthenticated: $(grep -c 'EnsureAuthenticated::handle()' storage/logs/laravel.log)"
echo "ProjectManager: $(grep -c 'ProjectManager::' storage/logs/laravel.log)"
echo "ProjectInitService: $(grep -c 'ProjectInitService::init()' storage/logs/laravel.log)"
echo "LogTenancyState: $(grep -c 'LogTenancyState::handle()' storage/logs/laravel.log)"

echo ""
echo "=== CICLO DETECTADO ==="
echo "Llamadas a /sport-competition/auth/login: $(grep '/sport-competition/auth/login' storage/logs/laravel.log | wc -l)"
echo "Llamadas a /sport-competition/admin/users/list: $(grep '/sport-competition/admin/users/list' storage/logs/laravel.log | wc -l)"

echo ""
echo "Tip: Para seguir en tiempo real:"
echo "  tail -f storage/logs/laravel.log | grep '\[Log-System-Auth\]'"
