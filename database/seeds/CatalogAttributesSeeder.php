<?php

use Illuminate\Database\Seeder;

use App\Models\CatalogCategory;
use App\Models\CatalogAttribute;

class CatalogAttributesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $attributes = [
            'size' => 'Size',
            'color' => 'Color',
            'garment' => 'Garment',
            'gender' => 'Gender',
        ];
        foreach ($attributes as $value => $name) {
            $attr = new CatalogAttribute();
            $attr->name = $name;
            $attr->value = $value;
            $attr->save();
        }
        
        $attrs = CatalogAttribute::get()->lists('id')->toArray();
        $categories = CatalogCategory::get();
        foreach ($categories as $category) {
            $category->catalogAttributes()->sync($attrs);
        }
    }
}
