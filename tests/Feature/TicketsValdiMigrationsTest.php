<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Verifica el esquema que un tenant de TicketsValdi obtiene al crearse:
 * primero las migraciones Common de la plataforma y después las del proyecto,
 * en el mismo orden que ejecuta MigrateProjectDatabase.
 */
class TicketsValdiMigrationsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh', [
            '--path' => ['database/migrations/projects/Common', 'database/migrations/projects/TicketsValdi'],
        ]);
    }

    #[Test]
    public function itCreatesEveryDomainTable(): void
    {
        $expected = [
            'institutions',
            'customers',
            'events',
            'ticket_types',
            'coupons',
            'orders',
            'order_items',
            'payments',
            'tickets',
            'ticket_checkins',
        ];

        foreach ($expected as $table) {
            $this->assertTrue(Schema::hasTable($table), "Falta la tabla '{$table}'.");
        }
    }

    #[Test]
    public function everyDomainTableHasTimestamps(): void
    {
        $tables = [
            'institutions',
            'customers',
            'events',
            'ticket_types',
            'coupons',
            'orders',
            'order_items',
            'payments',
            'tickets',
            'ticket_checkins',
        ];

        foreach ($tables as $table) {
            $this->assertTrue(
                Schema::hasColumns($table, ['created_at', 'updated_at']),
                "La tabla '{$table}' no tiene created_at/updated_at."
            );
        }
    }

    #[Test]
    public function itDoesNotCreateItsOwnUsersOrRolesTables(): void
    {
        $this->assertFalse(Schema::hasTable('admin_users'));
        $this->assertFalse(Schema::hasTable('institution_user'));
    }

    #[Test]
    public function itAddsNullableInstitutionScopeToPlatformUsers(): void
    {
        $this->assertTrue(Schema::hasColumn('users', 'institution_id'));
    }

    #[Test]
    public function customersAllowGuestCheckoutWithoutPassword(): void
    {
        $this->assertTrue(Schema::hasColumns('customers', ['email', 'password', 'registered_at']));

        $id = \DB::table('customers')->insertGetId([
            'email' => 'guest@example.test',
            'name' => 'Guest Buyer',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $customer = (array) \DB::table('customers')->find($id);

        $this->assertNull($customer['password'], 'Un comprador invitado no debe requerir contraseña.');
        $this->assertNull($customer['registered_at'], 'Un comprador invitado todavía no completó el registro.');
    }

    #[Test]
    public function customerEmailIsUnique(): void
    {
        $row = [
            'email' => 'dup@example.test',
            'name' => 'First',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        \DB::table('customers')->insert($row);

        $this->expectException(\Illuminate\Database\QueryException::class);

        \DB::table('customers')->insert([
            ...$row,
            'name' => 'Second',
        ]);
    }
}
