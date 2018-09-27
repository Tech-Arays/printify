<?php

namespace App\Http\Requests\Admin\ProductModel;

use App\Http\Requests\Request;

class ProductModelSaveFormRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'preview' => 'image|max:4096'
        ];
    }
}
