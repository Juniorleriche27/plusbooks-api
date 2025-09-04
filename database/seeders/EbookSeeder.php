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
            'description' => 'Demo',
            'price' => 0,
        ]);
    }
}
