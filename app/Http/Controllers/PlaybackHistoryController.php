<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\PlaybackHistory;


class PlaybackHistoryController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //Validar los datos
        $validator = Validator::make($request->all(), [
            'song_id' => 'required|exists:songs,id',
            'play_date' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        //Guardar cancion en la tabla de historial de reproducciones
        $playbackHistory = new PlaybackHistory();
        $playbackHistory->user_id = $request->user()->id;
        $playbackHistory->song_id = $request->song_id;
        $playbackHistory->play_date = $request->play_date;
        $playbackHistory->save();

        return response()->json($playbackHistory, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        //Mostrar historial de reproducciones de la mas reciente a la mas antigua
        $playbackHistory = PlaybackHistory::where('user_id', $request->user()->id)
            ->orderBy('play_date', 'desc')
            ->get();

        return response()->json($playbackHistory, 200);
    }
}
