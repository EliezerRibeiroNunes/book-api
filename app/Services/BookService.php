<?php

namespace App\Services;

use App\Models\Book;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BookService
{
    public function listForUser(User $user, ?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        return $user->books()
            ->when($search, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('isbn', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function createForUser(User $user, array $data): Book
    {
        return $user->books()->create($data);
    }

    public function findForUser(User $user, Book $book): Book
    {
        return $user->books()->whereKey($book->getKey())->firstOrFail();
    }

    public function updateForUser(User $user, Book $book, array $data): Book
    {
        $ownedBook = $this->findForUser($user, $book);
        $ownedBook->update($data);

        return $ownedBook->refresh();
    }

    public function deleteForUser(User $user, Book $book): void
    {
        $this->findForUser($user, $book)->delete();
    }
}
