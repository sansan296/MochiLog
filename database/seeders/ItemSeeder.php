<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Item;
use Carbon\Carbon;

class ItemSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $now = Carbon::now();
    $keyword = 'りんご';

    for ($i = 0; $i < 20; $i++) {
      Item::factory()->create([
        'item' => "{$keyword}",
        'created_at' => $now->copy()->subSeconds(100 - $i),
        'updated_at' => $now->copy()->subSeconds(100 - $i),
      ]);
    }

    for ($i = 20; $i < 100; $i++) {
      Item::factory()->create([
        'created_at' => $now->copy()->subSeconds(100 - $i),
        'updated_at' => $now->copy()->subSeconds(100 - $i),
      ]);
    }
  }
}