<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Espetos',       'sort_order' => 1],
            ['name' => 'Carnes',        'sort_order' => 2],
            ['name' => 'Self-Service',  'sort_order' => 3],
            ['name' => 'Acompanhamentos','sort_order' => 4],
            ['name' => 'Bebidas',       'sort_order' => 5],
            ['name' => 'Sobremesas',    'sort_order' => 6],
            ['name' => 'Promoções',     'sort_order' => 7],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['slug' => Str::slug($cat['name'])],
                array_merge($cat, ['slug' => Str::slug($cat['name']), 'active' => true])
            );
        }
    }
}
