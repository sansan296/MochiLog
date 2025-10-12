<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 賞味期限付きで商品を登録できる()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/items', [
            'item' => 'ヨーグルト',
            'expiration_year' => 2025,
            'expiration_month' => 10,
            'expiration_day' => 31,
        ]);

        $response->assertRedirect('/items');
        $this->assertDatabaseHas('items', [
            'item' => 'ヨーグルト',
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function 賞味期限が近い順に並ぶ()
    {
        $user = User::factory()->create();

        $item1 = Item::factory()->create([
            'item' => '牛乳',
            'expiration_date' => Carbon::today()->addDays(2),
            'user_id' => $user->id,
        ]);
        $item2 = Item::factory()->create([
            'item' => 'パン',
            'expiration_date' => Carbon::today()->addDays(1),
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get('/items');

        $response->assertSeeInOrder([
            'パン', // 期限が近い
            '牛乳',
        ]);
    }
}
