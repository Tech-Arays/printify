<?php

namespace App\Models;

use FractalManager;

use App\Components\Money;
use App\Transformers\CatalogCategory\CatalogCategoryTransformer;
use App\Transformers\CatalogCategory\CatalogCategoryIncludedTransformer;

class CatalogCategory extends \Baum\Node
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    // id
    // user_id
    // name
    // status
    // type
    // category
    // preview_file_id
    // created_at
    // updated_at


    protected $table = 'catalog_categories';

    // baum
        // 'parent_id' column name
        protected $parentColumn = 'parent_id';

        // 'lft' column name
        protected $leftColumn = 'lft';

        // 'rgt' column name
        protected $rightColumn = 'rgt';

        // 'depth' column name
        protected $depthColumn = 'depth';

        protected $orderColumn = 'id';

    // guard attributes from mass-assignment
    //protected $guarded = ['id', 'parent_id', 'lidx', 'ridx', 'nesting'];

   /// protected $fillable = [];

    protected $casts = [];

    // revisions
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $keepRevisionOf = [
        'status'
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
                new \App\Events\CatalogCategory\CatalogCategoryCreatedEvent($model)
            );
        });

        static::saved(function($model) {
            \Event::fire(
                new \App\Events\CatalogCategory\CatalogCategorySavedEvent($model)
            );
        });

        static::deleted(function($model) {
            \Event::fire(
                new \App\Events\CatalogCategory\CatalogCategoryDeletedEvent($model)
            );
        });
    }

    
    /***********
     * Relations
     */

        public function catalogAttributes()
        {
            return $this->belongsToMany(\App\Models\CatalogAttribute::class, 'catalog_category_attribute_relations', 'category_id', 'attribute_id')
                ->orderBy('id');
        }

        public function templates()
        {
            return $this->hasMany(\App\Models\ProductModelTemplate::class, 'category_id');
        }

        public function preview()
        {
            return $this->hasOne(\App\Models\File::class, 'id', 'preview_file_id');
        }

    /***********
     * Checks
     */

        public function isPrepaid()
        {
            return (bool)$this->prepaid_amount;
        }

        public function isHeadwear()
        {
            return stristr($this->slug, 'headwear');
        }

        public function isPrintIO()
        {
            return stristr($this->name, 'Throw Pillow Zipper')
                || stristr($this->name, 'Shower Curtains')
                || stristr($this->name, 'ToteBag')
                || stristr($this->name, 'Ottoman')
                || stristr($this->name, 'Mug')
                || stristr($this->name, 'HandTowel')
                || stristr($this->name, 'DuvetCover')
                || stristr($this->name, 'DogBed')
                || stristr($this->name, 'BathTowel')
                || stristr($this->name, 'EverythingBag')
                || stristr($this->name, 'WovenBlanket')
                || stristr($this->name, 'FleeceBlanket');
        }

        public function isSocks()
        {
            return stristr($this->name, 'socks');
        }

        public function isGalloree()
        {
            return stristr($this->name, 'Doggie Tank')
                || stristr($this->name, 'Skate Deck')
                || stristr($this->name, 'Beach Towl')
                || stristr($this->name, 'Throw Pillows')
                || stristr($this->name, 'Apron')
                || stristr($this->name, 'Leggings');
        }

    /**********
     * Counters
     */



    /*************
     * Decorators
     */

        public function nameis()
        {
            switch($this->name) {
                case 'Reg Tees':
                    $name = 'Reg Tees';
                    break;

                case 'Wild Tees':
                    $name = 'Wild Tees';
                    break;

                case 'Phone Cases':
                    $name = 'Phone Cases';
                    break;

                case 'Art Prints':
                    $name = 'Art Prints';
                    break;

                case 'Stickers':
                    $name = 'Stickers';
                    break;

                case 'SOCKS':
                    $name = 'Socks';
                    break;

                default:
                    $name = $this->name;
            }

            return $this->name;
        }

        public function prepaidAmountMoney()
        {
            return Money::i()->parse($this->prepaid_amount);
        }

    /*********
     * Helpers
     */

    /**************
     * Transformers
     */

        /*public function transformDefaultRootCategoriesTree()
        {
            return FractalManager::serializeItem($this, new CatalogCategoryTransformer);
        }

        public function transformIncluded()
        {
            return FractalManager::serializeItem($this, new CatalogCategoryIncludedTransformer);
        }*/

    /***********
     * Functions
     */

        public static function getDefaultRoot() {
            return static::where(['slug' => 'root'])
                ->first();
        }

        public static function getOrCreateDefaultRoot() {
            $root = static::getDefaultRoot();

            if (!$root) {
                $root = new CatalogCategory();
                $root->name = 'Root';
                $root->slug = 'root';
                $root->save();
            }

            return $root;
        }

        public static function getDefaultRootTopCategories()
        {
            $root = static::getDefaultRoot();
            return $root->immediateDescendants()->get();
        }

        public static function getDefaultRootCategoriesTree()
        {
            $root = static::getDefaultRoot();
            return $root
                ->immediateDescendants()
                ->with('preview')
                ->with('templates')
                ->with('templates.image')
                ->with('templates.preview')
                ->with('templates.models')
                ->with('catalogAttributes')
                ->orderBy('id')
                ->get();
        }

        public static function getOrCreateFirstLevelCategoryByName($name)
        {
            $category = static::where('name', $name)
                ->where('slug', str_slug($name))
                ->first();

            if (!$category) {
                $category = new static();
                $category->slug = str_slug($name);
                $category->name = $name;
                $category->saveOrFail();
                $category->makeChildOf(
                    static::getOrCreateDefaultRoot()
                );
            }

            return $category;
        }

    /*************
     * Collections
     */


}
