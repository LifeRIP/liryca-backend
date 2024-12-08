<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\User;
use App\Models\Artist;
use App\Models\Album;
use App\Models\Song;
use App\Models\Playlist;

class SearchController extends Controller
{
    public function search_bar(Request $request, string $search)
    {
        try {

            //reemplazar los & por espacios para la busqueda 
            $search = str_replace('&', ' ', $search);

            $Songs = $this->Songs($search);

            $Albums = $this->Albums($search);

            $Playlists = $this->Playlists($search);

            $Artists = $this->Artists($request, $search);


            $Users = $this->Users($request, $search);

            $response = [
                'songs' => $Songs,
                'albums' => $Albums,
                'playlists' => $Playlists,
                'artists' => $Artists,
                'users' => $Users
            ];

            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function Songs(string $search)
    {
        try {
            // Buscar canciones que contengan la palabra en el nombre o en el genero
            $Songs = Song::where('title', 'like', '%' . $search . '%')
                ->orWhere('genre', 'like', '%' . $search . '%')
                ->get();

            // Buscar canciones que contengan la palabra en el nombre del Usuario y rol sea artista
            $UserArtists = User::where('username', 'like', '%' . $search . '%')
                ->where('role', 'artist')
                ->get();

            foreach ($UserArtists as $UserArtist) {
                $Artist = Artist::where('user_id', $UserArtist->id)->first();
                $Songs = $Songs->merge($Artist->songs);
            }

            // Buscar canciones que contengan la palabra en el nombre del album

            $Albums = Album::where('title', 'like', '%' . $search . '%')->get();
            foreach ($Albums as $Album) {
                $Songs = $Songs->merge($Album->songs);
            }

            $Songs = $Songs->map(function ($song) {
                return [
                    'id' => $song->id,
                    'title' => $song->title,
                    'genre' => $song->genre,
                    'url' => $song->url_song,
                    'duration' => $song->duration,
                    'album_id' => $song->album->id,
                    'album_image' => $song->album->icon,
                    'album_title' => $song->album->title,
                    'artist_name' => $song->artist->user->username,
                    'artist_id' => $song->artist->user->id
                ];
            });

            return $Songs;
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function Albums(string $search)
    {
        try {
            // Buscar albumes que contengan la palabra en el nombre
            $Albums = Album::where('title', 'like', '%' . $search . '%')->get();

            // Albumes de artistas que contengan la palabra en el nombre del artista
            $UserArtists = User::where('username', 'like', '%' . $search . '%')
                ->where('role', 'artist')
                ->get();

            foreach ($UserArtists as $UserArtist) {
                $Artist = Artist::where('user_id', $UserArtist->id)->first();
                $Albums = $Albums->merge($Artist->albums);
            }

            $Albums = $Albums->map(function ($album) {
                return [
                    'id' => $album->id,
                    'title' => $album->title,
                    'icon' => $album->icon,
                    'release_date' => $album->release_date,
                    'artist_name' => $album->artist->user->username,
                    'artist_id' => $album->artist->user->id
                ];
            });

            return $Albums;
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function Playlists(string $search)
    {
        try {
            // Buscar playlist que contengan la palabra en el nombre
            $Playlists = Playlist::where('name', 'like', '%' . $search . '%')
                ->where('privacy', 'public')
                ->orWhere('description', 'like', '%' . $search . '%')
                ->where('privacy', 'public')
                ->get();

            // playlist de usuarios que contengan la palabra en el nombre del usuario
            $Users = User::where('username', 'like', '%' . $search . '%')->get();

            foreach ($Users as $User) {
                $Playlists = $Playlists->merge($User->playlists);
            }

            $Playlists = $Playlists->map(function ($playlist) {
                return [
                    'id' => $playlist->id,
                    'name' => $playlist->name,
                    'image' => $playlist->image,
                    'user_name' => $playlist->user->username,
                    'user_id' => $playlist->user->id
                ];
            });

            return $Playlists;
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function Artists(Request $request, string $search)
    {
        try {
            // Buscar usuarios contengan la palabra en el nombre o en la descripcion y rol sea artist
            $UserArtists = User::where('role', 'artist')
                ->where('username', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%')
                ->where('role', 'artist')
                ->get();


            // Buscar artistas que contengan la palabra en el about del artista
            $Artists = Artist::where('about', 'like', '%' . $search . '%')->get();

            foreach ($Artists as $Artist) {
                $UserArtists = $UserArtists->merge(User::where('id', $Artist->user_id)->get());
            }

            //Comprobar si el usuario sigue a los artistas
            $UserArtists = $UserArtists->map(function ($user) use ($request) {
                $user->is_followed = $user->followers->contains($request->user()->id);
                return $user;
            });

            $UserArtists = $UserArtists->map(function ($user) {
                return [
                    'id' => $user->id,
                    'username' => $user->username,
                    'profile_picture' => $user->profile_picture,
                    'profile_banner' => $user->profile_banner,
                    'follow' => $user->is_followed

                ];
            });

            return $UserArtists;
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function Users(Request $request, string $search)
    {
        try {
            // Buscar usuarios contengan la palabra en el nombre o en la descripcion y rol sea user
            $Users = User::where('role', 'user')
                ->where('username', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%')
                ->where('role', 'user')
                ->get();

            //Comprobar si el usuario sigue a los usuarios 
            $Users = $Users->map(function ($user) use ($request) {
                $user->is_followed = $user->followers->contains($request->user()->id);
                return $user;
            });

            $Users = $Users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'username' => $user->username,
                    'profile_picture' => $user->profile_picture,
                    'profile_banner' => $user->profile_banner,
                    'follow' => $user->is_followed
                ];
            });

            return $Users;
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
