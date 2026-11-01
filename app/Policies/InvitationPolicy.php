<?php

namespace App\Policies;

use App\Models\Invitation;
use App\Models\User;

class InvitationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Invitation $invitation): bool
    {
        return $user->isAdmin() || $user->id === $invitation->user_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Invitation $invitation): bool
    {
        return $user->isAdmin() || $user->id === $invitation->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Invitation $invitation): bool
    {
        return $user->isAdmin() || $user->id === $invitation->user_id;
    }

    /**
     * Determine whether the user can publish the model.
     */
    public function publish(User $user, Invitation $invitation): bool
    {
        return ($user->isAdmin() || $user->id === $invitation->user_id) && ! $invitation->isExpired();
    }
}
