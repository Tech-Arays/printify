<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use FractalManager;
use App\Transformers\CatalogCategory\GarmentGroupIncludedTransformer;

class GarmentGroup extends Model
{
    // id
    // name
    // slug
    // preview_file_id
    
    const SLUG_UNISEX_MEN = 'unisex_men';
    const SLUG_WOMEN = 'women';
    const SLUG_INFANT = 'infant';
    const SLUG_KIDS = 'kids';
    
    protected $table = 'garment_groups';
    public $timestamps = false;
    
    public static function getTableName()
    {
        return with(new static)->getTable();
    }
    
    /************
     * Accessors
     */

    /************
     * Mutators
     */
        
    /*********
     * Scopes
     */

    
    /***********
     * Relations
     */
    
        public function garments()
        {
            return $this->hasMany(Garment::class, 'garment_group_id');
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
        
        public static function defaultName($key)
        {
            $groups = static::listDefaultNames();
            return (isset($groups[$key]) ? $groups[$key] : null);
        }
        
        public static function listDefaultNames()
        {
            return collect([
                static::SLUG_UNISEX_MEN => trans('labels.unisex_men'),
                static::SLUG_WOMEN      => trans('labels.women'),
                static::SLUG_KIDS       => trans('labels.kids'),
                static::SLUG_INFANT     => trans('labels.infant')
            ]);
        }
        
        public static function defaultPosition($key)
        {
            $groups = static::listDefaultPositions();
            return (isset($groups[$key]) ? $groups[$key] : null);
        }
        
        public static function listDefaultPositions()
        {
            return collect([
                static::SLUG_UNISEX_MEN => 1,
                static::SLUG_WOMEN      => 2,
                static::SLUG_KIDS       => 3,
                static::SLUG_INFANT     => 4
            ]);
        }
    
    /**************
     * Transformers
     */
        
        public function transformIncluded()
        {
            return FractalManager::serializeItem($this, new GarmentGroupIncludedTransformer);
        }
        
    /***********
     * Functions
     */
    
        public static function guessByTemplateName($str)
        {
            if (stristr($str, 'guy')) {
                return static::SLUG_UNISEX_MEN;
            }
            else if (stristr($str, 'girl')) {
                return static::SLUG_WOMEN;
            }
            else if (stristr($str, 'infant')) {
                return static::SLUG_INFANT;
            }
            else if (
                stristr($str, 'kids')
                || stristr($str, 'youth')
            ) {
                return static::SLUG_KIDS;
            }
            else {
                return static::SLUG_UNISEX_MEN;
            }
        }
        
        public static function getGuessedByTemplate(ProductModelTemplate $template)
        {
            $slug = static::guessByTemplateName($template->name);
            $group = static::where('slug', $slug)
                ->first();
                
            if (!$group) {
                $group = new static();
                $group->slug = $slug;
                $group->name = static::defaultName($slug);
                $group->position = static::defaultPosition($slug);
                $group->save();
            }
            
            return $group;
        }
        
    /*************
     * Collections
     */
        
        
}
