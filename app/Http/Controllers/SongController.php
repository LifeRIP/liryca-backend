<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Album;
use App\Models\Artist;
use App\Models\PlaybackHistory;
use App\Models\Playlist;
use App\Models\PlaylistSong;
use App\Models\Song;
use App\Models\SongCollaborator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SongController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            // Obtener todas las canciones con su informacion y el user_id y nombre de los colaboradores
            $songs = Song::all();
            foreach ($songs as $song) {
                $collaborators = SongCollaborator::where('song_id', $song->id)
                    ->join('artists', 'song_collaborators.artist_id', '=', 'artists.id')
                    ->join('users', 'artists.user_id', '=', 'users.id')
                    ->select('users.id', 'users.username')
                    ->get();
                $song->collaborators = $collaborators;
            }

            return response()->json([
                'success' => true,
                'data' => $songs
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validar los campos requeridos
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'album_id' => 'nullable|exists:albums,id',
                'time' => 'required|date_format:H:i:s', // Formato HH:MM:SS
                'genre' => 'required|string|max:100',
                'url_song' => 'required|url',
                'collaborators' => 'array', // Colaboradores
            ]);

            // Comprobar si la validación falla
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 400);
            }

            $artist = Artist::where('user_id', $request->user()->id)->first();

            // Validar si la canción con el mismo título y artista ya existe
            $existingSong = Song::where('title', $request->title)
                ->where('artist_id', $artist->id)
                ->first();
            if ($existingSong) {
                return response()->json([
                    'success' => false,
                    'message' => 'Song title already exists'
                ], 400);
            }

            // Crear una nueva canción
            $song = Song::create([
                'title' => $request->title,
                'artist_id' => $artist->id,
                'album_id' => $request->album_id,
                'time' => $request->time,
                'genre' => $request->genre,
                'url_song' => $request->url_song,
            ]);

            // Crear los colaboradores de la canción
            if ($request->collaborators) {
                foreach ($request->collaborators as $collaborator) {
                    SongCollaborator::create([
                        'song_id' => $song->id,
                        'artist_id' => $collaborator
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'data' => $song
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            // Verificar si la canción existe
            $song = Song::find($id);
            if (!$song) {
                return response()->json([
                    'success' => false,
                    'message' => 'Song not found'
                ], 404);
            }

            // Obtener los colaboradores de la canción
            $collaborators = SongCollaborator::where('song_id', $song->id)
                ->join('artists', 'song_collaborators.artist_id', '=', 'artists.id')
                ->join('users', 'artists.user_id', '=', 'users.id')
                ->select('users.id', 'users.name')
                ->get();
            $song->collaborators = $collaborators;

            // Mostrar la canción
            return response()->json([
                'success' => true,
                'data' => $song
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            // Validar los campos requeridos
            $validator = Validator::make($request->all(), [
                'title' => 'string|max:255',
                'artist_id' => 'exists:artists,id',
                'album_id' => 'nullable|exists:albums,id',
                'time' => 'date_format:H:i:s', // Formato HH:MM:SS
                'genre' => 'string|max:100',
                'url_song' => 'url',
                'is_active' => 'boolean',
                'collaborators' => 'array', // Colaboradores
            ]);

            // Verificar si la canción existe
            $song = Song::find($id);
            if (!$song) {
                return response()->json([
                    'success' => false,
                    'message' => 'Song not found'
                ], 404);
            }

            // Comprobar si la validación falla
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 400);
            }

            // Actualizar la canción
            $song->update($request->all());

            // Actualizar los colaboradores de la canción

            // Eliminar los colaboradores actuales
            SongCollaborator::where('song_id', $song->id)->delete();

            // Crear los nuevos colaboradores

            if ($request->collaborators) {
                foreach ($request->collaborators as $collaborator) {
                    SongCollaborator::create([
                        'song_id' => $song->id,
                        'artist_id' => $collaborator
                    ]);
                }
            }

            $song->collaborators = SongCollaborator::where('song_id', $song->id)
                ->join('artists', 'song_collaborators.artist_id', '=', 'artists.id')
                ->join('users', 'artists.user_id', '=', 'users.id')
                ->select('users.id', 'users.name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $song
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            // Verificar si la canción existe
            $song = Song::find($id);
            if (!$song) {
                return response()->json([
                    'success' => false,
                    'message' => 'Song not found'
                ], 404);
            }

            // Eliminar la canción
            $song->delete();

            // Eliminar los colaboradores de la canción
            SongCollaborator::where('song_id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Song deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/song/top-by-user/{userId}",
     *     summary="Get top songs by user",
     *     description="Returns the top 10 songs by a user based on playback count",
     *     operationId="top-by-user",
     *     tags={"song"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="ID of the user",
     *         required=true,
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="song_id", type="integer", example=1),
     *                     @OA\Property(property="album_id", type="integer", example=1),
     *                     @OA\Property(property="time", type="string", example="00:01:38"),
     *                     @OA\Property(property="genre", type="string", example="Reggaeton"),
     *                     @OA\Property(property="song_url", type="string", example="https://i.scdn.co/image/ab67616d0000b273b62a2ec2d61d48f34a368144"),
     *                     @OA\Property(property="is_active", type="boolean", example=true),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2024-11-18T00:20:29.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-11-18T00:20:29.000000Z"),
     *                     @OA\Property(property="song_name", type="string", example="Mírame"),
     *                     @OA\Property(property="artist_id", type="integer", example="264e1f6c-52ec-48ea-bfb1-13100f8b5cf3"),
     *                     @OA\Property(property="album_image", type="string", example="https://i.scdn.co/image/ab67616d0000b273b62a2ec2d61d48f34a368144"),
     *                     @OA\Property(property="artist_name", type="string", example="Blessd"),
     *                     @OA\Property(property="is_liked", type="boolean", example=true)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Error: Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="No songs found for this user"
     *             )
     *         )
     *     )
     * )
     */
    public function getTopSongsByUser(Request $request, $userId)
    {
        $topSongs = Song::whereHas('artist', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->where('is_active', true)
            ->with(['album:id,icon', 'artist.user:id,username']) // Include album icon and user username
            ->withCount('playbackHistories as play_count')
            ->orderByDesc('play_count')
            ->take(10)
            ->get();

        if ($topSongs->isEmpty()) {
            return response()->json(['error' => 'No songs found for this artist'], 404);
        }

        // Comprobar que la playlist likedSongs exista
        if (Playlist::where('user_id', $request->user()->id)
            ->where('name', 'LikedSongs')
            ->exists()

        ) {
            $LikedSongs = Playlist::where('user_id', $request->user()->id)
                ->where('name', 'LikedSongs')
                ->first();

            //Comprobar si la canción tiene like
            $topSongs = $topSongs->map(function ($song) use ($LikedSongs) {
                $song->is_liked = PlaylistSong::where('playlist_id', $LikedSongs->id)
                    ->where('song_id', $song->id)
                    ->exists();
                return $song;
            });
        } else {
            $topSongs = $topSongs->map(function ($song) {
                $song->is_liked = false;
                return $song;
            });
        }

        return response()->json([
            'data' => $topSongs->map(function ($song) {
                return [
                    'song_id' => $song->id,
                    'album_id' => $song->album_id,
                    'time' => $song->time,
                    'genre' => $song->genre,
                    'song_url' => $song->url_song,
                    'is_active' => $song->is_active,
                    'created_at' => $song->created_at,
                    'updated_at' => $song->updated_at,
                    'song_name' => $song->title,
                    'artist_id' => $song->artist->user->id,
                    'album_image' => $song->album->icon,
                    'artist_name' => $song->artist->user->username,
                    'is_liked' => $song->is_liked
                ];
            }),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/song/by-album-id/{albumId}",
     *     summary="Get songs by album",
     *     description="Returns all songs from a specific album",
     *     operationId="by-album-id",
     *     tags={"song"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="albumId",
     *         in="path",
     *         description="ID of the album",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="song_id", type="integer", example=1),
     *                     @OA\Property(property="album_id", type="integer", example=1),
     *                     @OA\Property(property="time", type="string", example="00:01:38"),
     *                     @OA\Property(property="genre", type="string", example="Reggaeton"),
     *                     @OA\Property(property="song_url", type="string", example="https://i.scdn.co/image/ab67616d0000b273b62a2ec2d61d48f34a368144"),
     *                     @OA\Property(property="is_active", type="boolean", example=true),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2024-11-18T00:20:29.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-11-18T00:20:29.000000Z"),
     *                     @OA\Property(property="song_name", type="string", example="Mírame"),
     *                     @OA\Property(property="artist_id", type="integer", example="264e1f6c-52ec-48ea-bfb1-13100f8b5cf3"),
     *                     @OA\Property(property="album_image", type="string", example="https://i.scdn.co/image/ab67616d0000b273b62a2ec2d61d48f34a368144"),
     *                     @OA\Property(property="artist_name", type="string", example="Blessd"),
     *                     @OA\Property(property="is_liked", type="boolean", example=true)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Error: Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="No songs found for this album"
     *             )
     *         )
     *     )
     * )
     */
    public function getSongsByAlbumId(Request $request, $albumId)
    {
        $songs = Song::where('album_id', $albumId)
            ->with(['album', 'artist'])
            ->get();

        if ($songs->isEmpty()) {
            return response()->json(['error' => 'No songs found for this album'], 404);
        }

        // Comprobar que la playlist likedSongs exista
        if (Playlist::where('user_id', $request->user()->id)
            ->where('name', 'LikedSongs')
            ->exists()

        ) {
            $LikedSongs = Playlist::where('user_id', $request->user()->id)
                ->where('name', 'LikedSongs')
                ->first();

            //Comprobar si la canción tiene like
            $songs = $songs->map(function ($song) use ($LikedSongs) {
                $song->is_liked = PlaylistSong::where('playlist_id', $LikedSongs->id)
                    ->where('song_id', $song->id)
                    ->exists();
                return $song;
            });
        } else {
            $songs = $songs->map(function ($song) {
                $song->is_liked = false;
                return $song;
            });
        }

        $songs = $songs->map(function ($song) {
            return [
                'song_id' => $song->id,
                'album_id' => $song->album_id,
                'time' => $song->time,
                'genre' => $song->genre,
                'song_url' => $song->url_song,
                'is_active' => $song->is_active,
                'created_at' => $song->created_at,
                'updated_at' => $song->updated_at,
                'song_name' => $song->title,
                'artist_id' => $song->artist->user->id,
                'album_image' => $song->album->icon,
                'artist_name' => $song->artist->user->username,
                'is_liked' => $song->is_liked
            ];
        });

        return response()->json([
            'data' => $songs,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/song/songs/top-today",
     *     summary="Get top songs today",
     *     description="Returns the top 10 songs played today",
     *     operationId="top-songs-today",
     *     tags={"song"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="song_id", type="integer", example=1),
     *                     @OA\Property(property="album_id", type="integer", example=1),
     *                     @OA\Property(property="song_url", type="string", example="https://i.scdn.co/image/ab67616d0000b273b62a2ec2d61d48f34a368144"),
     *                     @OA\Property(property="song_name", type="string", example="Mírame"),
     *                     @OA\Property(property="artist_id", type="integer", example="264e1f6c-52ec-48ea-bfb1-13100f8b5cf3"),
     *                     @OA\Property(property="artist_name", type="string", example="Blessd"),
     *                     @OA\Property(property="is_liked", type="boolean", example=true)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Error: Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="No songs found for today"
     *             )
     *         )
     *     )
     * )
     */
    public function getTopSongsToday(Request $request)
    {
        $topSongs = Song::where('songs.is_active', true)
            ->join('artists', 'songs.artist_id', '=', 'artists.id')
            ->join('users', 'artists.user_id', '=', 'users.id')
            ->select(
                'songs.*',
                'users.id as user_id',
                'users.username'
            )
            ->whereHas('playbackHistories', function ($query) {
                $query->whereDate('created_at', today());
            })
            ->withCount(['playbackHistories as play_count' => function ($query) {
                $query->whereDate('created_at', today());
            }])
            ->orderByDesc('play_count')
            ->take(10)
            ->get();

        if ($topSongs->isEmpty()) {
            return response()->json(['error' => 'No songs found for today'], 404);
        }

        // Comprobar que la playlist likedSongs exista
        if (Playlist::where('user_id', $request->user()->id)
            ->where('name', 'LikedSongs')
            ->exists()

        ) {
            $LikedSongs = Playlist::where('user_id', $request->user()->id)
                ->where('name', 'LikedSongs')
                ->first();

            //Comprobar si la canción tiene like
            $topSongs = $topSongs->map(function ($song) use ($LikedSongs) {
                $song->is_liked = PlaylistSong::where('playlist_id', $LikedSongs->id)
                    ->where('song_id', $song->id)
                    ->exists();
                return $song;
            });
        } else {
            $topSongs = $topSongs->map(function ($song) {
                $song->is_liked = false;
                return $song;
            });
        }

        return response()->json([
            'success' => true,
            'data' => $topSongs->map(function ($song) {
                return [
                    'song_id' => $song->id,
                    'album_id' => $song->album_id,
                    'song_url' => $song->url_song,
                    'song_name' => $song->title,
                    'artist_id' => $song->artist->user->id,
                    'artist_name' => $song->artist->user->username,
                    'is_liked' => $song->is_liked
                ];
            }),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/song/songs/top-today-by-my-country",
     *     summary="Get top songs today by my country",
     *     description="Returns the top 10 songs played today by my country",
     *     operationId="top-songs-today-by-my-country",
     *     tags={"song"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="song_id", type="integer", example=1),
     *                     @OA\Property(property="album_id", type="integer", example=1),
     *                     @OA\Property(property="song_url", type="string", example="https://i.scdn.co/image/ab67616d0000b273b62a2ec2d61d48f34a368144"),
     *                     @OA\Property(property="song_name", type="string", example="Mírame"),
     *                     @OA\Property(property="artist_id", type="integer", example="264e1f6c-52ec-48ea-bfb1-13100f8b5cf3"),
     *                     @OA\Property(property="artist_name", type="string", example="Blessd"),
     *                     @OA\Property(property="is_liked", type="boolean", example=true)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Error: Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="No songs found for today in my country"
     *             )
     *         )
     *     )
     * )
     */
    public function getTopSongsTodayByMyCountry(Request $request)
    {
        // Obtener el país del usuario autenticado desde el token
        $country = $request->user()->country;
        // Inicio del día actual
        $today = now()->startOfDay();

        // Consulta para obtener las canciones más escuchadas hoy en el país del usuario
        $topSongs = PlaybackHistory::query()
            ->select('songs.id', 'songs.title', 'songs.artist_id', 'songs.album_id', 'songs.url_song', 'artists.user_id as artist_user_id', 'artist_users.username', DB::raw('COUNT(playback_histories.id) as play_count'))
            ->join('songs', 'playback_histories.song_id', '=', 'songs.id')
            ->join('artists', 'songs.artist_id', '=', 'artists.id')
            ->join('users as artist_users', 'artists.user_id', '=', 'artist_users.id') // Unir con la tabla users a través de artists
            ->join('users as playback_users', 'playback_histories.user_id', '=', 'playback_users.id') // Unir con la tabla users para filtrar por país
            ->where('playback_users.country', '=', $country)
            ->where('playback_histories.created_at', '>=', $today)
            ->groupBy('songs.id', 'songs.title', 'songs.artist_id', 'songs.album_id', 'songs.url_song', 'artists.user_id', 'artist_users.username')
            ->orderByDesc('play_count')
            ->take(10) // Limita a las 10 canciones más escuchadas
            ->get();

        //return $topSongs;
        if ($topSongs->isEmpty()) {
            return response()->json(['error' => 'No songs found for today in my country'], 404);
        }

        // Comprobar que la playlist likedSongs exista
        if (Playlist::where('user_id', $request->user()->id)
            ->where('name', 'LikedSongs')
            ->exists()
        ) {
            $LikedSongs = Playlist::where('user_id', $request->user()->id)
                ->where('name', 'LikedSongs')
                ->first();

            // Comprobar si la canción tiene like
            $topSongs = $topSongs->map(function ($song) use ($LikedSongs) {
                $song->is_liked = PlaylistSong::where('playlist_id', $LikedSongs->id)
                    ->where('song_id', $song->id)
                    ->exists();
                return $song;
            });
        } else {
            $topSongs = $topSongs->map(function ($song) {
                $song->is_liked = false;
                return $song;
            });
        }

        return response()->json([
            'success' => true,
            'data' => $topSongs->map(function ($song) {
                return [
                    'song_id' => $song->id,
                    'album_id' => $song->album_id,
                    'song_url' => $song->url_song,
                    'song_name' => $song->title,
                    'artist_id' => $song->artist_user_id,
                    'artist_name' => $song->username,
                    'is_liked' => $song->is_liked
                ];
            }),
        ]);
    }
}
