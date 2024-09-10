<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Song;
use Illuminate\Http\JsonResponse;

class SongController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $songs = Song::all();
        return response()->json($songs);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $song = Song::create($request->all());
        return response()->json([
            'success' => true,
            'data' => $song
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $song = Song::find($id);
        return response()->json($song);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $song = Song::find($id);
        $song->update($request->all());
        return response()->json([
            'success' => true,
            'data' => $song
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $song = Song::find($id);
        $song->delete();
        return response()->json([
            'success' => true,
            'data' => $song
        ]);
    }
}
