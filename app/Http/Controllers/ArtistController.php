<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Album;
use Illuminate\Http\Request;

class ArtistController extends Controller
{
    public function getAlbumsByArtist($artistId)
    {
        $albums = Album::where('artist_id', $artistId)->paginate(5);

        if ($albums->isEmpty()) {
            return response()->json(['error' => 'No albums found for this artist'], 404);
        }

        return response()->json([
            'artist_id' => $artistId,
            'albums' => $albums->items(),  // Retorna solo los álbumes de la página actual
            'pagination' => [
                'current_page' => $albums->currentPage(),
                'per_page' => $albums->perPage(),
                'total' => $albums->total(),
                'last_page' => $albums->lastPage(),
                'next_page_url' => $albums->nextPageUrl(),
                'prev_page_url' => $albums->previousPageUrl(),
            ],
        ]);
    }
}
