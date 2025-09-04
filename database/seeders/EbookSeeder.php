<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ebook;

class EbookSeeder extends Seeder
{
    public function run(): void
    {
        Ebook::create([
            'title' => 'Premier test',
            'description' => 'E-book de démonstration',
            'price' => 0,
            'file_path' => null,
        ]);

        Ebook::create([
            'title' => 'Deuxième test',
            'description' => 'Encore un e-book',
            'price' => 10,
            'file_path' => null,
        ]);
    }
}
