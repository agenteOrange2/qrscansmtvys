<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);

        $admin = User::firstOrCreate(
            ['email' => 'frontend@kuiraweb.com'],
            [
                'name' => 'FrontEnd',
                'password' => 'Password',
                'email_verified_at' => now(),
            ]
        );
        $admin->syncRoles(['Administrador']);

        $test = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => 'Admin2025+?',
                'email_verified_at' => now(),
            ]
        );
        $test->syncRoles(['Exhibidor']);
    }
}
