<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AuthorController extends Controller
{
    //
    public function index()
    {
        try {
            $perPage = 10;
            $currentPage = request()->get('page', 1);
            $authors = DB::table('authors')->paginate($perPage);
            return response()->json($authors, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $author = DB::table('authors')->where('id', $id)->first();
            if (!$author) {
                return response()->json(['error' => 'Author not found'], 404);
            }
            return response()->json($author);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Validation logic
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
            ]);
            $name = $request->input('name');

            $authorId = DB::table('authors')->insertGetId([
                'name' => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $author = DB::table('authors')->find($authorId);

            return response()->json($author, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Validation logic
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
            ]);
            $name = $request->input('name');

            $updated = DB::table('authors')
                ->where('id', $id)
                ->update([
                    'name' => $name,
                    'updated_at' => now(),
                ]);

            if ($updated === 0) {
                return response()->json(['error' => 'Author not found'], 404);
            }

            $author = DB::table('authors')->find($id);

            return response()->json($author, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function destroy($id)
    {
        try {
            $deleted = DB::table('authors')->where('id', $id)->delete();

            if ($deleted === 0) {
                return response()->json(['error' => 'Author not found'], 404);
            }

            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
}
