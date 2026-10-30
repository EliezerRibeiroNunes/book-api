<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookIndexRequest;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Services\BookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function __construct(private readonly BookService $bookService) {}

    public function index(BookIndexRequest $request)
    {
        return BookResource::collection(
            $this->bookService->listForUser(
                $request->user(),
                $request->validated('search'),
                $request->integer('per_page', 15),
            ),
        );
    }

    public function store(StoreBookRequest $request)
    {
        $book = $this->bookService->createForUser($request->user(), $request->validated());

        return (new BookResource($book))->response()->setStatusCode(201);
    }

    public function show(Request $request, Book $book): BookResource
    {
        return new BookResource(
            $this->bookService->findForUser($request->user(), $book),
        );
    }

    public function update(UpdateBookRequest $request, Book $book): BookResource
    {
        return new BookResource(
            $this->bookService->updateForUser(
                $request->user(),
                $book,
                $request->validated(),
            ),
        );
    }

    public function destroy(Request $request, Book $book): JsonResponse
    {
        $this->bookService->deleteForUser($request->user(), $book);

        return response()->json([
            'message' => 'Book deleted successfully.',
        ]);
    }
}
