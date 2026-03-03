<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /** @var \App\Models\Role $admin */
        $admin = Role::firstOrCreate(['name' => 'admin']);

        $permission = Permission::firstOrCreate(['name' => 'settings.view']);
        $admin->permissions()->sync([$permission->id], detaching: false);

        if (User::count()) {
            return;
        }

        User::factory()->create([
            'email' => 'test@example.com',
            'name' => 'Test User',
            'role_id' => $admin->id,
        ]);

        // User::factory(10)->create();
    }
}
