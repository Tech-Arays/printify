<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use FractalManager;
use App\Transformers\CatalogCategory\GarmentIncludedTransformer;

class Garment extends Model
{
    // id
    // name
    // slug
    // preview_file_id
    // garment_group_id

    const SLUG_T_SHIRT = 't_shirt';
    const SLUG_TANK_TOP = 'tank_top';
    const SLUG_OTHER = 'other';
    const SLUG_ALL_OVER_PRINT = 'all_over_print';
    const SLUG_PRINT_IO = 'printio';
    const SLUG_HEADWEAR = 'headwear';
    const SLUG_SOCKS = 'socks';
    const SLUG_GALLOREE = 'galloree';

    protected $table = 'garments';
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

        public function garmentGroup()
        {
            return $this->belongsTo(GarmentGroup::class, 'garment_group_id');
        }

        public function getFullGarmentAttribute()
        {
            return $this->name . " (" . ucfirst($this->garmentGroup->name).")";
        }

        public function preview()
        {
            return $this->hasOne(File::class, 'id', 'preview_file_id');
        }

        public function templates()
        {
            return $this->hasMany(ProductModelTemplate::class, 'garment_id');
        }

    /***********
     * Checks
     */

        public function isAllOverPrint()
        {
            return $this->slug == static::SLUG_ALL_OVER_PRINT;
        }

        public function isAllOverPrintOrSimilar()
        {
            return
                $this->slug == static::SLUG_ALL_OVER_PRINT
                || $this->slug == static::SLUG_PRINT_IO
                || $this->slug == static::SLUG_HEADWEAR
                || $this->slug == static::SLUG_SOCKS
                || $this->slug == static::SLUG_GALLOREE;
        }

        public function isPrintIO()
        {
            return $this->slug == static::SLUG_PRINT_IO;
        }

        public function isGalloree()
        {
            return $this->slug == static::SLUG_GALLOREE;
        }

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
            $garments = static::listDefaultNames();
            return (isset($garments[$key]) ? $garments[$key] : null);
        }

        public static function listDefaultNames()
        {
            return collect([
                static::SLUG_T_SHIRT        => trans('labels.t_shirt'),
                static::SLUG_TANK_TOP       => trans('labels.tank_top'),
                static::SLUG_ALL_OVER_PRINT => trans('labels.all_over_print'),
                static::SLUG_PRINT_IO       => trans('labels.print_io'),
                static::SLUG_HEADWEAR       => trans('labels.headwear'),
                static::SLUG_GALLOREE       => trans('labels.galloree'),
                static::SLUG_SOCKS          => trans('labels.socks'),
                static::SLUG_OTHER          => trans('labels.other')
            ]);
        }

        public static function defaultPosition($key)
        {
            $garments = static::listDefaultGarmentPositions();
            return (isset($garments[$key]) ? $garments[$key] : null);
        }

        public static function listDefaultGarmentPositions()
        {
            return collect([
                static::SLUG_T_SHIRT        => 1,
                static::SLUG_TANK_TOP       => 2,
                static::SLUG_ALL_OVER_PRINT => 3,
                static::SLUG_PRINT_IO       => 4,
                static::SLUG_HEADWEAR       => 5,
                static::SLUG_GALLOREE       => 6,
                static::SLUG_SOCKS          => 7,
                static::SLUG_OTHER          => 8
            ]);
        }

    /**************
     * Transformers
     */

        public function transformIncluded()
        {
            return FractalManager::serializeItem($this, new GarmentIncludedTransformer);
        }

    /***********
     * Functions
     */

        public static function guessByTemplate(ProductModelTemplate $template)
        {
            if (stristr($template->category->name, 'wild tee')) {
                return static::SLUG_ALL_OVER_PRINT;
            }
            else if (
                stristr($template->category->name, 'Throw Pillow Zipper')
                || stristr($template->category->name, 'Shower Curtains')
                || stristr($template->category->name, 'ToteBag')
                || stristr($template->category->name, 'Ottoman')
                || stristr($template->category->name, 'Mug')
                || stristr($template->category->name, 'HandTowel')
                || stristr($template->category->name, 'DuvetCover')
                || stristr($template->category->name, 'DogBed')
                || stristr($template->category->name, 'BathTowel')
                || stristr($template->category->name, 'EverythingBag')
                || stristr($template->category->name, 'WovenBlanket')
                || stristr($template->category->name, 'FleeceBlanket')
            ) {
                return static::SLUG_PRINT_IO;
            }
            else if (stristr($template->category->name, 'headwear')) {
                return static::SLUG_HEADWEAR;
            }
            else if (
                stristr($template->category->name, 'Doggie Tank')
                || stristr($template->category->name, 'Skate Deck')
                || stristr($template->category->name, 'Beach Towl')
                || stristr($template->category->name, 'Throw Pillows')
                || stristr($template->category->name, 'Apron')
                || stristr($template->category->name, 'Leggings')
            ) {
                return static::SLUG_GALLOREE;
            }
            else if (stristr($template->category->name, 'socks')) {
                return static::SLUG_SOCKS;
            }
            else if (stristr($template->name, 't-shirt')) {
                return static::SLUG_T_SHIRT;
            }
            else if (
                stristr($template->name, 'tank-top')
                || stristr($template->name, 'tank top')
            ) {
                return static::SLUG_TANK_TOP;
            }
            else {
                return static::SLUG_OTHER;
            }
        }

        public static function getGuessedByTemplate(ProductModelTemplate $template)
        {
            $slug = static::guessByTemplate($template);
            $group = GarmentGroup::getGuessedByTemplate(
                $template
            );
            $garment = static::where('slug', $slug)
                ->where('garment_group_id', $group->id)
                ->first();

            if (!$garment) {
                $garment = new static();
                $garment->slug = $slug;
                $garment->name = static::defaultName($slug);
                $garment->position = static::defaultPosition($slug);

                $garment->garment_group_id = $group->id;

                $garment->save();
            }

            return $garment;
        }

    /*************
     * Collections
     */


}
