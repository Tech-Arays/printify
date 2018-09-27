<?php

namespace App\Models\Traits;

use Hashids;

trait HashidTrait
{
    public function id()
    {
        if ($this->id) {
            return Hashids::encode($this->id);
        }
        else {
            return null;
        }
    }

    public static function findByHashId($id)
    {
        $decoded = Hashids::decode($id);
        $id = null;

        if (!empty($decoded)) {
            $id = (int)$decoded[0];
        }

        return parent::find($id);
    }
}
