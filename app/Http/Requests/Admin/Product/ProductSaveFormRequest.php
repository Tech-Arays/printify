<?php

namespace App\Http\Requests\Admin\Product;

use App\Http\Requests\Request;

class ProductSaveFormRequest extends Request
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
            'designer-attachment' => 'file|max:4096|mimetypes:application/zip'
        ];
    }
}
