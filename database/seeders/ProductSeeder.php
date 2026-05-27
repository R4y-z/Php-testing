<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $espetos    = Category::where('slug', 'espetos')->first();
        $carnes     = Category::where('slug', 'carnes')->first();
        $selfService= Category::where('slug', 'self-service')->first();
        $acomps     = Category::where('slug', 'acompanhamentos')->first();
        $bebidas    = Category::where('slug', 'bebidas')->first();
        $sobremesas = Category::where('slug', 'sobremesas')->first();

        $products = [
            // Espetos
            ['category_id' => $espetos?->id,    'name' => 'Espeto de Frango',        'price' => 8.00,  'type' => 'unitario', 'description' => 'Espeto de frango temperado na brasa'],
            ['category_id' => $espetos?->id,    'name' => 'Espeto de Carne',         'price' => 10.00, 'type' => 'unitario', 'description' => 'Espeto de picanha na brasa'],
            ['category_id' => $espetos?->id,    'name' => 'Espeto de Coração',       'price' => 7.00,  'type' => 'unitario', 'description' => 'Espeto de coração de frango'],
            ['category_id' => $espetos?->id,    'name' => 'Espeto Misto',            'price' => 12.00, 'type' => 'unitario', 'description' => 'Espeto com frango e carne'],
            // Carnes
            ['category_id' => $carnes?->id,     'name' => 'Picanha Grelhada',        'price' => 65.00, 'type' => 'unitario', 'description' => 'Picanha grelhada (300g)'],
            ['category_id' => $carnes?->id,     'name' => 'Costela de Boi',          'price' => 55.00, 'type' => 'unitario', 'description' => 'Costela de boi no bafo'],
            ['category_id' => $carnes?->id,     'name' => 'Frango Assado',           'price' => 45.00, 'type' => 'unitario', 'description' => 'Frango inteiro assado na brasa'],
            // Self-Service por KG
            ['category_id' => $selfService?->id,'name' => 'Self-Service por KG',     'price' => 79.90, 'type' => 'kg',       'description' => 'Buffet completo com saladas, pratos quentes e assados'],
            ['category_id' => $selfService?->id,'name' => 'Salada por KG',           'price' => 39.90, 'type' => 'kg',       'description' => 'Saladas variadas no buffet'],
            // Acompanhamentos
            ['category_id' => $acomps?->id,     'name' => 'Arroz e Feijão',          'price' => 8.00,  'type' => 'unitario', 'description' => 'Porção de arroz e feijão'],
            ['category_id' => $acomps?->id,     'name' => 'Farofa Especial',         'price' => 6.00,  'type' => 'unitario', 'description' => 'Farofa temperada da casa'],
            ['category_id' => $acomps?->id,     'name' => 'Vinagrete',               'price' => 5.00,  'type' => 'unitario', 'description' => 'Vinagrete fresco'],
            ['category_id' => $acomps?->id,     'name' => 'Pão de Alho',             'price' => 6.00,  'type' => 'unitario', 'description' => 'Pão de alho na brasa (4 unidades)'],
            // Bebidas
            ['category_id' => $bebidas?->id,    'name' => 'Água Mineral 500ml',      'price' => 3.00,  'type' => 'unitario', 'description' => 'Água mineral sem gás'],
            ['category_id' => $bebidas?->id,    'name' => 'Refrigerante Lata',       'price' => 5.00,  'type' => 'unitario', 'description' => 'Lata 350ml'],
            ['category_id' => $bebidas?->id,    'name' => 'Suco Natural',            'price' => 8.00,  'type' => 'unitario', 'description' => 'Suco de fruta natural 400ml'],
            ['category_id' => $bebidas?->id,    'name' => 'Cerveja Long Neck',       'price' => 9.00,  'type' => 'unitario', 'description' => 'Cerveja 355ml gelada'],
            ['category_id' => $bebidas?->id,    'name' => 'Caipirinha',              'price' => 15.00, 'type' => 'unitario', 'description' => 'Caipirinha de limão'],
            // Sobremesas
            ['category_id' => $sobremesas?->id, 'name' => 'Pudim de Leite',          'price' => 8.00,  'type' => 'unitario', 'description' => 'Pudim caseiro de leite condensado'],
            ['category_id' => $sobremesas?->id, 'name' => 'Mousse de Maracujá',      'price' => 7.00,  'type' => 'unitario', 'description' => 'Mousse cremoso de maracujá'],
        ];

        foreach ($products as $data) {
            if (!$data['category_id']) continue;
            Product::updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                array_merge($data, [
                    'slug'      => Str::slug($data['name']),
                    'active'    => true,
                    'available' => true,
                ])
            );
        }
    }
}
