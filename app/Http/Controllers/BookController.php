<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{

    public function index()
    {

        try {
            $books = DB::table('books')
                ->leftJoin('author_book', 'books.id', '=', 'author_book.book_id')
                ->leftJoin('authors', 'author_book.author_id', '=', 'authors.id')
                ->leftJoin('publishers', 'author_book.publisher_id', '=', 'publishers.id')
                ->select(
                    'books.id as book_id',
                    'books.title as book_title',
                    'authors.name as author_name',
                    'publishers.name as publisher_name',

                )
                ->get();

            return response()->json($books, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $book = DB::table('books')->where('id', $id)->first();
            if (!$book) {
                return response()->json(['error' => 'Book not found'], 404);
            }
            return response()->json($book);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Validation logic here

            $title = $request->input('title');
            $publisher_ids = $request->input('publisher_ids');
            $author_ids = $request->input('author_ids');

            $bookId = DB::table('books')->insertGetId([
                'title' => $title,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Save the relationship in the pivot table
            foreach ($publisher_ids as $publisher_id) {

                foreach ($author_ids as $author_id) {


                    DB::table('author_book')->insert([
                        'author_id' => $author_id,
                        'publisher_id' => $publisher_id,
                        'book_id' => $bookId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            $book = DB::table('books')->find($bookId);

            return response()->json($book, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Validation logic here

            $title = $request->input('title');
            $publisher_ids = $request->input('publisher_ids');
            $author_ids = $request->input('author_ids');
            $update_authors = $request->input('update_author');
            $updated = DB::table('books')
                ->where('id', $id)
                ->update([
                    'title' => $title,
                    'updated_at' => now(),
                ]);

            // Update the relationship in the pivot table
            DB::table('author_book')->where('book_id', $id)->delete(); // Remove existing relationships

            foreach ($publisher_ids as $publisher_id) {

                foreach ($author_ids as $author_id) {

                DB::table('author_book')->insert([
                    'author_id' => $author_id,
                    'publisher_id' => $publisher_id,
                    'book_id' => $id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            }

            if ($updated === 0) {
                return response()->json(['error' => 'Book not found'], 404);
            }

            $book = DB::table('books')->find($id);

            return response()->json($book, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function destroy($id)
    {
        try {
            $deleted = DB::table('books')->where('id', $id)->delete();

            if ($deleted === 0) {
                return response()->json(['error' => 'Book not found'], 404);
            }

            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function homepage()
    {
        try {
            $books = DB::table('books')->paginate(10);
            return view('homepage', ['books' => $books]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
