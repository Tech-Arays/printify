<?php

namespace App\Models;

use DB;
use FractalManager;
use Illuminate\Database\Eloquent\Model;

use App\Transformers\PriceModifier\PriceModifierBriefTransformer;

class PriceModifier extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use \Venturecraft\Revisionable\RevisionableTrait;

    // id
    // modifier
    // user_id
    // template_id

    public $timestamps = false;
    protected $table = 'price_modifiers';

    // revisions
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $keepRevisionOf = [
        'modifier'
    ];

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function($model) {
            \Event::fire(
                new \App\Events\PriceModifier\PriceModifierCreatedEvent($model)
            );
        });

        static::saved(function($model) {
            \Event::fire(
                new \App\Events\PriceModifier\PriceModifierSavedEvent($model)
            );
        });

        static::deleted(function($model) {
            \Event::fire(
                new \App\Events\PriceModifier\PriceModifierDeletedEvent($model)
            );
        });
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

        public function template()
        {
            return $this->hasOne(ProductModelTemplate::class, 'id', 'template_id');
        }

        public function user()
        {
            return $this->belongsTo(User::class, 'user_id');
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


    /***************
     * Transformers
     */

        public function transformBrief()
        {
            return FractalManager::serializeItem($this, new PriceModifierBriefTransformer);
        }



    /***********
     * Functions
     */

        public function modifyPrice($price)
        {
            $percent = $this->modifier / 100;
            return $price + ($price * $percent);
        }

    /*************
     * Collections
     */


}
