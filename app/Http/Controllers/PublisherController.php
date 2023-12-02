<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublisherController extends Controller
{
    //
    public function index()
    {
        try {
            $publishers = DB::table('publishers')->paginate(10);
            return response()->json($publishers);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $publisher = DB::table('publishers')->where('id', $id)->first();
            if (!$publisher) {
                return response()->json(['error' => 'Publisher not found'], 404);
            }
            return response()->json($publisher);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Validation logic here

            $name = $request->input('name');

            $publisherId = DB::table('publishers')->insertGetId([
                'name' => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $publisher = DB::table('publishers')->find($publisherId);

            return response()->json($publisher, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Validation logic here

            $name = $request->input('name');

            $updated = DB::table('publishers')
                ->where('id', $id)
                ->update([
                    'name' => $name,
                    'updated_at' => now(),
                ]);

            if ($updated === 0) {
                return response()->json(['error' => 'Publisher not found'], 404);
            }

            $publisher = DB::table('publishers')->find($id);

            return response()->json($publisher, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function destroy($id)
    {
        try {
            $deleted = DB::table('publishers')->where('id', $id)->delete();

            if ($deleted === 0) {
                return response()->json(['error' => 'Publisher not found'], 404);
            }

            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
}
