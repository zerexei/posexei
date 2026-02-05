<?php

namespace App\Observers;

use App\Models\Organization;
use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        if (! $user->organization_id) {
            $organization = Organization::create([
                'owner_id' => $user->id,
                'name' => $user->name."'s Organization",
            ]);

            $user->forceFill(['organization_id' => $organization->id])->saveQuietly();
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
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
