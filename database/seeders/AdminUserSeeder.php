<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $user = User::factory()->create(
            [
            'name' => 'Hashim',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin'),
            ]
        );

        $roles = Role::where('name', 'admin')
            ->whereIn(
                'guard_name', [
                'api',
                'jwt-api'
                ]
            )->get();
        foreach ($roles as $role) {
            $user->assignRole($role);
        }
    }
}
