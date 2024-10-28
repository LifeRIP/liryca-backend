<?php

use Illuminate\Support\Facades\DB;

/*
// Un helper para obtener el usuario autenticado por el token
if (!function_exists('getAuthenticatedUser')) {
    function getAuthenticatedUser() {
        return auth()->user();
    }
}
*/

// Función para ejecutar una transacción
function transactional($callback)
{
    DB::beginTransaction();
    try {
        $result = $callback();
        DB::commit();
        return $result;
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'message' => 'An error occurred'
        ], 500);
    }
}
