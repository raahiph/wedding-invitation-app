<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('wedding.categories', []) as $cat) {
            Category::firstOrCreate(['name' => $cat['name']]);
        }
    }
}
