<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BookTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_only_sees_own_books(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $ownBook = Book::factory()->for($owner)->create();
        Book::factory()->for($otherUser)->create();

        Sanctum::actingAs($owner);

        $this->getJson('/api/books')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $ownBook->id);
    }

    public function test_user_can_create_search_update_and_delete_a_book(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $created = $this->postJson('/api/books', [
            'name' => 'Clean Code',
            'isbn' => '9780132350884',
            'value' => '39.90',
        ])->assertCreated();

        $bookId = $created->json('data.id');

        $this->getJson('/api/books?search=clean')
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Clean Code');

        $this->patchJson("/api/books/{$bookId}", [
            'value' => '42.00',
        ])->assertOk()->assertJsonPath('data.value', '42.00');

        $this->deleteJson("/api/books/{$bookId}")
            ->assertOk()
            ->assertJson(['message' => 'Book deleted successfully.']);
    }

    public function test_user_cannot_access_another_users_book(): void
    {
        $owner = User::factory()->create();
        $book = Book::factory()->for($owner)->create();
        $otherUser = User::factory()->create();

        Sanctum::actingAs($otherUser);

        $this->getJson("/api/books/{$book->id}")->assertNotFound();
    }
}
