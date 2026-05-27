<?php

namespace Database\Seeders;

use App\Models\RestaurantTable;
use Illuminate\Database\Seeder;

class TableSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 15; $i++) {
            RestaurantTable::updateOrCreate(
                ['number' => str_pad($i, 2, '0', STR_PAD_LEFT)],
                [
                    'number'   => str_pad($i, 2, '0', STR_PAD_LEFT),
                    'name'     => 'Mesa ' . str_pad($i, 2, '0', STR_PAD_LEFT),
                    'capacity' => $i <= 10 ? 4 : 6,
                    'status'   => 'disponivel',
                    'location' => $i <= 5 ? 'Salão Principal' : ($i <= 10 ? 'Área Externa' : 'Área VIP'),
                    'active'   => true,
                ]
            );
        }
    }
}
