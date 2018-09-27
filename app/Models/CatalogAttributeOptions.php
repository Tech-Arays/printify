<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Transformers\CatalogAttribute\CatalogAttributeOptionFullTransformer;
use App\Transformers\Serializers\SimpleArraySerializer;

class CatalogAttributeOption extends \Baum\Node
{

    protected $table = 'catalog_attribute_options';

	// baum
        // 'parent_id' column name
        protected $parentColumn = 'parent_id';

        // 'lft' column name
        protected $leftColumn = 'lft';

        // 'rgt' column name
        protected $rightColumn = 'rgt';

        // 'depth' column name
        protected $depthColumn = 'depth';

    // guard attributes from mass-assignment
    protected $guarded = ['id', 'parent_id', 'lidx', 'ridx', 'nesting'];

    protected $fillable = ['name','value'];
    public $timestamps = false;

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    
    /***********
     * Relations
     */

        public function catalogAttribute()
        {
            return $this->belongsTo(\App\Models\CatalogAttribute::class, 'attribute_id');
        }

    

    /**************
     * Transformers
     */

        public function transformFull()
        {
            $resource = \FractalManager::item($this, new CatalogAttributeOptionFullTransformer);
            return \FractalManager::i(new SimpleArraySerializer())->createData($resource)->toArray();
        }

    /***********
     * Functions
     */

        protected static function addIfNotExist($attributeOption, $attributeId)
        {
            $option = static::where('name', $attributeOption['name'])
                ->where('value', $attributeOption['value'])
                ->where('attribute_id', $attributeId)
                ->first();

            if (!$option) {
                $option = new static();
                $option->name  = $attributeOption['name'];
                $option->value = $attributeOption['value'];
                $option->attribute_id = $attributeId;
                if (!empty($attributeOption['kz_option_id'])) {
                    $option->kz_option_id = $attributeOption['kz_option_id'];
                }
                $option->save();
            }

            return $option->id;
        }

        protected static function addOrUpdate($attributeOption, $attributeId)
        {
            $option = static::where('name', $attributeOption['name'])
                ->where('value', $attributeOption['value'])
                ->where('attribute_id', $attributeId)
                ->first();

            if (!$option) {
                $option = new static();
                $option->attribute_id = $attributeId;
                $option->name  = $attributeOption['name'];
                $option->value = $attributeOption['value'];
            }

            if (!empty($attributeOption['kz_option_id'])) {
                $option->kz_option_id = $attributeOption['kz_option_id'];
            }
            $option->save();

            return $option->id;
        }

}
