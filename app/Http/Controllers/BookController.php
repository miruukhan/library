<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    //
    public function index()
    {
        try {
            $books = DB::table('books')->paginate(10);
            return response()->json($books);
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
            $publisher_id = $request->input('publisher_id');

            $bookId = DB::table('books')->insertGetId([
                'title' => $title,
                'publisher_id' => $publisher_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

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
            $publisher_id = $request->input('publisher_id');

            $updated = DB::table('books')
                ->where('id', $id)
                ->update([
                    'title' => $title,
                    'publisher_id' => $publisher_id,
                    'updated_at' => now(),
                ]);

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
