<?php

namespace App\Transformers;

use Gate;
use League\Fractal;

class Transformer extends Fractal\TransformerAbstract
{
    protected function isApi()
    {
        return (request() instanceof \Dingo\Api\Http\Request);
    }
    
    protected function getId($model)
    {
        return (int)$model->id;
        // TODO: not used so far
        //return $this->isApi()
        //    ? $model->id()
        //    : (int)$model->id;
    }
    
    protected function includePolicies($model)
    {
        $policies = get_class_methods(policy($model));
        
        $data = [
            'allowed' => [],
            'denied' => []
        ];
        
        foreach ($policies as $policy) {
            $data['allowed'][$policy] = Gate::allows($policy, $model);
        }
        
        foreach ($policies as $policy) {
            $data['denied'][$policy] = Gate::denies($policy, $model);
        }
        
        return $data;
    }
}
