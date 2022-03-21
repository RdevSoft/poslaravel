<?php

namespace Database\Seeders;
use App\Models\Product;

use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::create([
                    'name' => 'LARAVEL Y LIVEWIRE',
                    'cost' => 1000,
                    'price' => 5000,
                    'barcode' => '54181545848',
                    'stock' => 1000,
                    'alerts' => 10,
                    'category_id' => 1,
                    'image' => 'curso.png',
                ]);

          Product::create([
                            'name' => 'RUNNING NIKE',
                            'cost' => 10000,
                            'price' => 50000,
                            'barcode' => '37837837837',
                            'stock' => 1000,
                            'alerts' => 10,
                            'category_id' => 2,
                            'image' => 'tenis.png',
                        ]);

         Product::create([
                             'name' => 'IPHONE 11',
                             'cost' => 1000,
                             'price' => 5000,
                             'barcode' => '45353432545',
                             'stock' => 1000,
                             'alerts' => 10,
                             'category_id' => 3,
                             'image' => 'iphone11.png',
                         ]);

                   Product::create([
                                     'name' => 'PC GAMER',
                                     'cost' => 1000,
                                     'price' => 5000,
                                     'barcode' => '56428793124',
                                     'stock' => 1000,
                                     'alerts' => 10,
                                     'category_id' => 1,
                                     'image' => 'pc.png',
                                 ]);
    }
}
