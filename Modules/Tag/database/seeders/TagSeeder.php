<?php

namespace Modules\Tag\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Tag\Models\Tag;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tag::factory()->count(5)->create();
    }
}
