<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\User;
use App\Models\File;

class FilePolicy
{
    use HandlesAuthorization;

    public function show(User $user, $file)
    {
        return $user->isOwnerOf($file);
    }

    public function delete(User $user, $file)
    {
        return (
            $user->isOwnerOf($file)
            && !$file->isInUse()
        );
    }
}
