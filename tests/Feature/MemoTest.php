<?php

use App\Models\User;
use App\Models\Item;
use App\Models\Memo;

it('displays the memo creation form', function () {
  $user = User::factory()->create();
  $this->actingAs($user);

  $item = $user->items()->create(Item::factory()->raw());

  $response = $this->get(route('items.memos.create', $item));
  $response->assertStatus(200);
  $response->assertViewIs('items.memos.create');
  $response->assertViewHas('item', $item);
});

it('allows authenticated users to create a memo', function () {
  $user = User::factory()->create();
  $this->actingAs($user);

  $item = $user->items()->create(Item::factory()->raw());
  $memoData = ['memo' => 'store test memo'];

  $response = $this->post(route('items.memos.store', $item), $memoData);
  $response->assertRedirect(route('items.show', $item));
  $this->assertDatabaseHas('memos', [
    'memo' => $memoData['memo'],
    'item_id' => $item->id,
    'user_id' => $user->id,
  ]);
});

it('displays a memo', function () {
  $user = User::factory()->create();
  $this->actingAs($user);

  $item = $user->items()->create(Item::factory()->raw());
  $memo = $item->memos()->create(Memo::factory()->raw(['user_id' => $user->id]));

  $response = $this->get(route('items.memos.show', [$item, $memo]));
  $response->assertStatus(200);
  $response->assertViewIs('items.memos.show');
  $response->assertViewHas('item', $item);
  $response->assertViewHas('memo', $memo);
});

it('displays the edit memo page', function () {
  $user = User::factory()->create();
  $this->actingAs($user);

  $item = $user->items()->create(Item::factory()->raw());
  $memo = $item->memos()->create(Memo::factory()->raw(['user_id' => $user->id]));

  $response = $this->get(route('items.memos.edit', [$item, $memo]));
  $response->assertStatus(200);
  $response->assertViewIs('items.memos.edit');
  $response->assertViewHas('item', $item);
  $response->assertViewHas('memo', $memo);
});

it('allows a user to update their memo', function () {
  $user = User::factory()->create();
  $this->actingAs($user);

  $item = $user->items()->create(Item::factory()->raw());
  $memo = $item->memos()->create(Memo::factory()->raw(['user_id' => $user->id]));
  $updatedData = ['memo' => 'update test memo'];

  $response = $this->put(route('items.memos.update', [$item, $memo]), $updatedData);
  $response->assertRedirect(route('items.memos.show', [$item, $memo]));
  $this->assertDatabaseHas('memos', [
    'id' => $memo->id,
    'memo' => $updatedData['memo'],
  ]);
});

it('allows a user to delete their memo', function () {
  $user = User::factory()->create();
  $this->actingAs($user);

  $item = $user->items()->create(Item::factory()->raw());
  $memo = $item->memos()->create(Memo::factory()->raw(['user_id' => $user->id]));

  $response = $this->delete(route('items.memos.destroy', [$item, $memo]));
  $response->assertRedirect(route('items.show', $item));
  $this->assertDatabaseMissing('memos', [
    'id' => $memo->id,
  ]);
});