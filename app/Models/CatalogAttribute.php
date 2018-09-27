<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

//use App\Transformers\Serializers\SimpleArraySerializer;
//use App\Transformers\CatalogAttribute\CatalogAttributeTransformer;

class CatalogAttribute extends Model
{
    const ATTRIBUTE_COLOR = 'color';
    const ATTRIBUTE_SIZE = 'size';
    const ATTRIBUTE_VARIANT = 'variant';

    // id
    // name
    // value

    protected $table = 'catalog_attributes';

    public $timestamps = false;

    // runtime fillable var
    public $selectedOptions = [];

    /************
     * Mutators
     */


    /*********
     * Scopes
     */



    /***********
     * Relations
     */

        public function catalogOptions()
        {
            return $this->hasMany(\App\Models\CatalogAttributeOption::class, 'attribute_id');
        }

    /***********
     * Checks
     */



    /**********
     * Counters
     */



    /*************
     * Decorators
     */



    /*********
     * Helpers
     */

    /**************
     * Transformers
     */

        public static function transformAll()
        {
            $attrs = static::with('catalogOptions')
                ->get();

            $resource = \FractalManager::collection($attrs, new CatalogAttributeTransformer);
            return \FractalManager::i(new SimpleArraySerializer())->createData($resource)->toArray();
        }

        public function transformFull()
        {
            $resource = \FractalManager::item($this, new CatalogAttributeTransformer);
            return \FractalManager::i(new SimpleArraySerializer())->createData($resource)->toArray();
        }

    /***********
     * Functions
     */

        public static function addIfNotExist($name)
        {
            $attribute = static::where('name', $name)
                ->first();

            if (!$attribute) {
                $attribute = new static();
                $attribute->name  = $name;
                $attribute->value = str_slug($name);
                $attribute->save();
            }

            return $attribute;
        }

    /*************
     * Collections
     */


}
