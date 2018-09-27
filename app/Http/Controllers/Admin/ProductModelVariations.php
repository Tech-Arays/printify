<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductModelVariations extends Controller
{
    //use \App\Http\Controllers\Admin\Traits\RapydControllerTrait;

    public function all(Request $request, $id){
        return view('Admin.');
    }

    protected function add()
    {
        return 'hello';
    }

    protected function getModelForEdit()
    {
        return 'hello';
    }
    
    protected function getModelForDelete()
    {
        return 'hello';
    }

}
