<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Article;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class UserObserver
{
    /**
     * Handle the User "deleting" event.
     */
    public function deleting(User $user): void
    {
        // 1. Protection: SuperAdmin cannot be deleted under any circumstance
        if ($user->hasRole('super_admin') || in_array($user->email, ['sifuncion@gmail.com', 'admin@glodaxia.com', 'luis.figuera@glodaxia.com'])) {
            throw ValidationException::withMessages([
                'user' => 'El Super Administrador principal está blindado y no puede ser eliminado.',
            ]);
        }

        // 2. Find the unique SuperAdmin of the system to inherit orphan articles
        $superAdmin = User::role('super_admin')->first() ?? User::whereIn('email', ['sifuncion@gmail.com', 'admin@glodaxia.com', 'luis.figuera@glodaxia.com'])->first();

        if ($superAdmin && $superAdmin->id !== $user->id) {
            $count = Article::where('user_id', $user->id)->count();
            if ($count > 0) {
                Article::where('user_id', $user->id)->update(['user_id' => $superAdmin->id]);
                Log::warning("⚠️ Usuario redactor eliminado (ID {$user->id} - {$user->name}): Se reasignaron automáticamente {$count} artículos al Super Administrador (ID {$superAdmin->id} - {$superAdmin->name}).");
            }
        }
    }
}