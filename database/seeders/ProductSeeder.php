<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Casio F-91W Plateado',
                'description' => 'Clásico, resistente y con un toque de sofisticación. Frente metalizado y malla de goma negra.',
                'price' => 55000.00,
                'stock' => 7,
                'brand' => 'Casio',
                'image_url' => 'https://images.unsplash.com/photo-1584680221528-768565dafc22?q=80&w=600',
            ],
            [
                'name' => 'Seiko 5 Sports',
                'description' => 'Reloj automático con movimiento japonés, caja de acero inoxidable y estilo deportivo.',
                'price' => 245000.00,
                'stock' => 3,
                'brand' => 'Seiko',
                'image_url' => 'https://images.unsplash.com/photo-1612817159949-195b6eb9e31a?q=80&w=600',
            ],
            [
                'name' => 'Orient Bambino Classic',
                'description' => 'Elegancia pura con cristal mineral abovedado, correa de cuero y movimiento automático.',
                'price' => 215000.00,
                'stock' => 5,
                'brand' => 'Orient',
                'image_url' => 'https://images.unsplash.com/photo-1523170335258-f5ed11844a49?q=80&w=600',
            ],
            [
                'name' => 'Timex Weekender',
                'description' => 'Diseño minimalista, luz Indiglo para la oscuridad y correa de nylon intercambiable.',
                'price' => 48000.00,
                'stock' => 9,
                'brand' => 'Timex',
                'image_url' => 'https://images.unsplash.com/photo-1509048191080-d2984bad6ae5?q=80&w=600',
            ],
            [
                'name' => 'Citizen Eco-Drive',
                'description' => 'Tecnología de carga solar, nunca necesita cambio de batería. Elegante y de alta precisión.',
                'price' => 195000.00,
                'stock' => 2,
                'brand' => 'Citizen',
                'image_url' => 'https://images.unsplash.com/photo-1548171915-e7af556f8902?q=80&w=600',
            ],
            [
                'name' => 'Casio G-Shock DW5600',
                'description' => 'Resistencia extrema a los impactos, diseño icónico cuadrado y sumergible hasta 200m.',
                'price' => 125000.00,
                'stock' => 6,
                'brand' => 'Casio',
                'image_url' => 'https://images.unsplash.com/photo-1616450379965-03714c622558?q=80&w=600',
            ],
            [
                'name' => 'Swatch Sistem51',
                'description' => 'Revolucionario movimiento mecánico suizo de solo 51 piezas. Diseño urbano y moderno.',
                'price' => 160000.00,
                'stock' => 8,
                'brand' => 'Swatch',
                'image_url' => 'https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?q=80&w=600',
            ],
            [
                'name' => 'Fossil Grant Chronograph',
                'description' => 'Inspiración vintage con números romanos, función de cronógrafo y malla de cuero marrón.',
                'price' => 115000.00,
                'stock' => 4,
                'brand' => 'Fossil',
                'image_url' => 'https://images.unsplash.com/photo-1524805444758-089113d48a6d?q=80&w=600',
            ],
            [
                'name' => 'Tissot Classic Dream',
                'description' => 'Minimalismo suizo, cristal de zafiro irrayable y movimiento de cuarzo duradero.',
                'price' => 249000.00,
                'stock' => 1,
                'brand' => 'Tissot',
                'image_url' => 'https://images.unsplash.com/photo-1524592094714-0f0654e20314?q=80&w=600',
            ],
            [
                'name' => 'Invicta Pro Diver',
                'description' => 'Estilo clásico de buceo, bisel giratorio unidireccional y agujas luminiscentes.',
                'price' => 89000.00,
                'stock' => 10,
                'brand' => 'Invicta',
                'image_url' => 'https://images.unsplash.com/photo-1533139502658-0198f920d8e8?q=80&w=600',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
