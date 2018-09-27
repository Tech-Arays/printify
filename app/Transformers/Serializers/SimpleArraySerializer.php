<?php

namespace App\Transformers\Serializers;

use League\Fractal\Serializer\ArraySerializer;

class SimpleArraySerializer extends ArraySerializer
{
    public function collection($resourceKey, array $data)
    {
        return $data;
    }
}
