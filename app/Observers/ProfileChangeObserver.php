<?php

namespace App\Observers;

use App\Models\User;
use App\Models\ProfileChange;

class ProfileChangeObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        //
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // Obtener los datos antiguos y nuevos del perfil
        $oldData = $user->getOriginal();  // Obtiene los datos antes de la actualización
        $newData = $user->getAttributes();  // Obtiene los datos actuales después de la actualización

        // Registrar los cambios en la tabla 'profile_changes'
        ProfileChange::create([
            'user_id' => $user->id,
            'old_data' => json_encode($oldData),  // Convierte los datos antiguos a formato JSON
            'new_data' => json_encode($newData),  // Convierte los datos nuevos a formato JSON
        ]);
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
