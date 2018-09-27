<?php

namespace App\Models\Traits;

trait VisibilityTrait
{
    /*********
     * Scopes
     */
    
        public function scopeVisible($query)
        {
            return $query
                ->where('visibility', static::VISIBILITY_VISIBLE);
        }
        
    /*************
     * Checks
     */
    
        public function isVisible()
        {
            return $this->visibility == static::VISIBILITY_VISIBLE;
        }
    
    /*************
     * Decorators
     */
        
        public function getVisibilityName()
        {
            return static::visibilityName($this->visibility);
        }
        
    /*********
     * Helpers
     */
    
        public static function visibilityName($visibility)
        {
            $visibilities = static::listVisibilities();
            return $visibilities[$visibility];
        }
        
        public static function listVisibilities()
        {
            return [
                static::VISIBILITY_VISIBLE => trans('labels.visible'),
                static::VISIBILITY_HIDDEN => trans('labels.hidden')
            ];
        }
        
}
