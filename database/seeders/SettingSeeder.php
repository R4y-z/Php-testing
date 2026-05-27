<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Geral
            ['key' => 'restaurant_name',    'value' => 'Churrascaria Nordestina', 'group' => 'general', 'type' => 'string',  'label' => 'Nome do Restaurante'],
            ['key' => 'restaurant_city',    'value' => 'Canindé de São Francisco', 'group' => 'general', 'type' => 'string', 'label' => 'Cidade'],
            ['key' => 'restaurant_state',   'value' => 'SE',                      'group' => 'general', 'type' => 'string',  'label' => 'Estado'],
            ['key' => 'restaurant_address', 'value' => 'Rua Principal, 123',      'group' => 'general', 'type' => 'string',  'label' => 'Endereço'],
            ['key' => 'restaurant_phone',   'value' => '(79) 99999-9999',         'group' => 'general', 'type' => 'string',  'label' => 'Telefone'],
            ['key' => 'restaurant_whatsapp','value' => '5579999999999',           'group' => 'general', 'type' => 'string',  'label' => 'WhatsApp'],
            ['key' => 'restaurant_cnpj',    'value' => '',                        'group' => 'general', 'type' => 'string',  'label' => 'CNPJ'],
            // Horário
            ['key' => 'open_days',          'value' => 'Segunda a Domingo',       'group' => 'hours',   'type' => 'string',  'label' => 'Dias de Funcionamento'],
            ['key' => 'open_time',          'value' => '11:00',                   'group' => 'hours',   'type' => 'string',  'label' => 'Abertura'],
            ['key' => 'close_time',         'value' => '23:00',                   'group' => 'hours',   'type' => 'string',  'label' => 'Fechamento'],
            // Delivery
            ['key' => 'delivery_enabled',   'value' => '1',                       'group' => 'delivery','type' => 'boolean', 'label' => 'Delivery Ativo'],
            ['key' => 'delivery_fee',        'value' => '5.00',                   'group' => 'delivery','type' => 'decimal',  'label' => 'Taxa de Entrega'],
            ['key' => 'min_order',           'value' => '20.00',                  'group' => 'delivery','type' => 'decimal',  'label' => 'Pedido Mínimo'],
            ['key' => 'delivery_time',       'value' => '40-60 minutos',          'group' => 'delivery','type' => 'string',   'label' => 'Tempo de Entrega'],
            // Pagamento
            ['key' => 'payment_dinheiro',   'value' => '1',                       'group' => 'payment', 'type' => 'boolean', 'label' => 'Aceita Dinheiro'],
            ['key' => 'payment_pix',        'value' => '1',                       'group' => 'payment', 'type' => 'boolean', 'label' => 'Aceita PIX'],
            ['key' => 'payment_credito',    'value' => '1',                       'group' => 'payment', 'type' => 'boolean', 'label' => 'Aceita Crédito'],
            ['key' => 'payment_debito',     'value' => '1',                       'group' => 'payment', 'type' => 'boolean', 'label' => 'Aceita Débito'],
            ['key' => 'pix_key',            'value' => '',                        'group' => 'payment', 'type' => 'string',  'label' => 'Chave PIX'],
            // KG
            ['key' => 'kg_price',           'value' => '79.90',                   'group' => 'kg',      'type' => 'decimal',  'label' => 'Preço por KG'],
            ['key' => 'kg_enabled',         'value' => '1',                       'group' => 'kg',      'type' => 'boolean',  'label' => 'Self-Service por KG'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
