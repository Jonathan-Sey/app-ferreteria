<?php

namespace App\Policies;

use App\Models\Marca;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MarcasPolicy
{
    use HandlesAuthorization;


    protected function check(User $user, string $permission): bool
    {
        // Si el usuario no tiene roles, denegar acceso
        if (!$user->hasAnyRole()) {
            return false;
        }
        
        return $user->can($permission);
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->check($user, 'view_any_marcas');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Marca $marcas): bool
    {
        return $user->check('view_marcas');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->check('create_marcas');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Marca $marcas): bool
    {
        return $user->check('update_marcas');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Marca $marcas): bool
    {
        return $user->check('delete_marcas');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->check('delete_any_marcas');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Marca $marcas): bool
    {
        return $user->check('force_delete_marcas');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->check('force_delete_any_marcas');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Marca $marcas): bool
    {
        return $user->check('restore_marcas');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->check('restore_any_marcas');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Marca $marcas): bool
    {
        return $user->check('replicate_marcas');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->check('reorder_marcas');
    }
}
