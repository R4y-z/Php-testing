<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'  => 'Administrador',
                'email' => 'admin@restaurante.com',
                'role'  => 'admin',
                'pass'  => 'admin123',
                'phone' => '(79) 99999-0001',
            ],
            [
                'name'  => 'Garçom João',
                'email' => 'garcom@restaurante.com',
                'role'  => 'garcom',
                'pass'  => 'garcom123',
                'phone' => '(79) 99999-0002',
            ],
            [
                'name'  => 'Caixa Maria',
                'email' => 'caixa@restaurante.com',
                'role'  => 'caixa',
                'pass'  => 'caixa123',
                'phone' => '(79) 99999-0003',
            ],
            [
                'name'  => 'Cozinha Pedro',
                'email' => 'cozinha@restaurante.com',
                'role'  => 'cozinha',
                'pass'  => 'cozinha123',
                'phone' => '(79) 99999-0004',
            ],
            [
                'name'  => 'Entregador Carlos',
                'email' => 'delivery@restaurante.com',
                'role'  => 'delivery',
                'pass'  => 'delivery123',
                'phone' => '(79) 99999-0005',
            ],
        ];

        foreach ($users as $data) {
            $role = Role::where('slug', $data['role'])->first();
            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name'     => $data['name'],
                    'password' => Hash::make($data['pass']),
                    'role_id'  => $role?->id,
                    'phone'    => $data['phone'],
                    'active'   => true,
                ]
            );
        }
    }
}
