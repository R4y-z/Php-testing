<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Administrador', 'slug' => 'admin',    'description' => 'Acesso total ao sistema'],
            ['name' => 'Garçom',        'slug' => 'garcom',   'description' => 'Atendimento de mesas e pedidos'],
            ['name' => 'Caixa',         'slug' => 'caixa',    'description' => 'Fechamento de contas e pagamentos'],
            ['name' => 'Cozinha',       'slug' => 'cozinha',  'description' => 'Visualização e atualização de pedidos'],
            ['name' => 'Delivery',      'slug' => 'delivery', 'description' => 'Gestão de entregas'],
            ['name' => 'Cliente',       'slug' => 'cliente',  'description' => 'Acesso à loja online'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['slug' => $role['slug']], $role);
        }
    }
}
