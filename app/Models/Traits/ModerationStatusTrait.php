<?php

namespace App\Models\Traits;

trait ModerationStatusTrait {
    
    /***********
     * Relations
     */
    
        public function moderationStatusRevisionHistory()
        {
            return $this->morphMany('\Venturecraft\Revisionable\Revision', 'revisionable')
                ->where('key', 'moderation_status')
                ->orderBy('id', SORT_DESC);
        }
        
        public function moderationCommentRevisionHistory()
        {
            return $this->morphMany('\Venturecraft\Revisionable\Revision', 'revisionable')
                ->where('key', 'moderation_status_comment')
                ->orderBy('id', SORT_DESC);
        }
        
    /***********
     * Checks
     */
        
        public function isModerationStatus($status)
        {
            return $this->moderation_status == $status;
        }
        
        public function isApproved()
        {
            return $this->isModerationStatus(static::MODERATION_STATUS_APPROVED);
        }
        
        public function isAutoApproved()
        {
            return $this->isModerationStatus(static::MODERATION_STATUS_AUTO_APPROVED);
        }
        
        public function isDeclined()
        {
            return $this->isModerationStatus(static::MODERATION_STATUS_DECLINED);
        }
        
        public function isOnModeration()
        {
            return $this->isModerationStatus(static::MODERATION_STATUS_ON_MODERATION);
        }
        
        public function isNotApproved()
        {
            return $this->isModerationStatus(static::MODERATION_STATUS_NOT_APPROVED);
        }
        
        public function wasDeclinedAtLeastOnce()
        {
            return (bool)$this->was_declined;
        }
        
    /**********
     * Counters
     */
    
        public static function countOnModeration()
        {
            return static::where('moderation_status', static::MODERATION_STATUS_ON_MODERATION)
                ->count();
        }
        
        public static function countAutoApproved()
        {
            return static::where('moderation_status', static::MODERATION_STATUS_AUTO_APPROVED)
                ->count();
        }
        
     /*************
     * Decorators
     */
        
        public function getModerationStatusName()
        {
            return static::moderationStatusName($this->moderation_status);
        }
    
    /*********
     * Helpers
     */
        
        public static function moderationStatusName($status)
        {
            $statuses = static::listModerationStatuses();
            return isset($statuses[$status]) ? $statuses[$status] : null;
        }
        
        public static function listModerationStatuses()
        {
            return [
                static::MODERATION_STATUS_NOT_APPROVED => trans('labels.not_approved'),
                static::MODERATION_STATUS_ON_MODERATION => trans('labels.on_moderation'),
                static::MODERATION_STATUS_APPROVED => trans('labels.approved'),
                static::MODERATION_STATUS_AUTO_APPROVED => trans('labels.auto_approved'),
                static::MODERATION_STATUS_DECLINED => trans('labels.declined')
            ];
        }
        
    /***********
     * Functions
     */
        
        public function changeModerationStatusTo($status, $comment = null)
        {
            $this->moderation_status = $status;
            
            if ($comment !== null) {
                $this->moderation_status_comment = $comment;
            }
            
            // $result check, because we need to trigger only if really changed
            $result = $this->save();
            
            if ($result) {
                if ($this instanceof \App\Models\ProductVariant) {
                    \Event::fire(new \App\Events\ProductVariant\ProductVariantModerationStatusChangedEvent($this));
                }
                else if ($this instanceof \App\Models\Product) {
                    \Event::fire(new \App\Events\Product\ProductModerationStatusChangedEvent($this));
                }
            }
            
            return $result;
        }
        
        public function approve($comment = '')
        {
            $this->changeModerationStatusTo(
                static::MODERATION_STATUS_APPROVED,
                $comment
            );
        }
        
        public function autoApprove($comment = '')
        {
            $this->changeModerationStatusTo(
                static::MODERATION_STATUS_AUTO_APPROVED,
                $comment
            );
        }
        
        public function decline($comment = '')
        {
            $this->was_declined = true;
            $this->changeModerationStatusTo(
                static::MODERATION_STATUS_DECLINED,
                $comment
            );
        }
}
