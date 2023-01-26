<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $adminRole = Role::create(['guard_name' => 'web', 'name' => 'admin']);
        $shareholderRole = Role::create(['guard_name' => 'web', 'name' => 'shareholder']);

        $admin = User::create([
            'name' => 'Admin Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
        ]);

        $admin->assignRole($adminRole);

        Company::create([
            'name' => 'Dangote Cement',
            'user_id' => $admin->id,
            'acronym' => 'DANGCEM'
        ]);
    }
}
