<?php

namespace App\Models\Traits;

use DateTime;
use DateTimeZone;

trait DatetimeTrait {

    /**
     * @Override
     */
    protected function updateTimestamps()
    {
        $time = (new \DateTime('now', static::getDateTimeZone()))->format('Y-m-d H:i:s');

        if (! $this->isDirty(static::UPDATED_AT)) {
            $this->setUpdatedAt($time);
        }

        if (! $this->exists && ! $this->isDirty(static::CREATED_AT)) {
            $this->setCreatedAt($time);
        }
    }



    /**
     * Get database timezone
     */
    public static $useTimestamps = true;
    public static $timezone = null;
    final public static function getTimezone()
    {
        if (static::$useTimestamps) {
            return 'UTC';
        }
        else {
            if (!static::$timezone) {
                $rows = \DB::select("
                    SELECT IF(@@session.time_zone = 'SYSTEM', @@system_time_zone, @@session.time_zone) as timezone
                ");
                static::$timezone = $rows[0]->timezone;
            }
        }

        return static::$timezone;
    }

    final public static function getDateTimeZone()
    {
        return new DateTimeZone(static::getTimezone());
    }

    protected function at($format = DateTime::W3C, $field)
    {
        if (
            isset($field)
            && $field
            && isset($this->{$field})
            && $this->{$field} != '0000-00-00 00:00:00'
        ) {
            $at = $this->{$field};
        }
        else {
            return null;
        }

        $datetime = new DateTime($at, static::getDateTimeZone());

        if ($format) {
            return $datetime->format($format);
        }

        return $datetime;
    }

    public function createdAt($format = 'U')
    {
        if (isset($this->createdAtField) && $this->createdAtField && isset($this->{$this->createdAtField})) {
            $field = $this->createdAtField;
        }
        else {
            $field = 'created_at';
        }

        $datetime = $this->at(false, $field);
        if ($format) {
            $datetime->setTimezone(
                new DateTimeZone(config('app.timezone'))
            );
            return $datetime->format($format);
        }
        else {
            return $datetime;
        }
    }

    public function updatedAt($format = 'U')
    {

        if (isset($this->updatedAtField) && $this->updatedAtField && isset($this->{$this->updatedAtField})) {
            $field = $this->updatedAtField;
        }
        else {
            $field = 'updated_at';
        }

        $datetime = $this->at(false, $field);

        if ($format) {
            $datetime->setTimezone(
                new DateTimeZone(config('app.timezone'))
            );
            return $datetime->format($format);
        }
        else {
            return $datetime;
        }
    }

    public function createdAtTZ($format = DateTime::W3C)
    {
        if (isset($this->createdAtField) && $this->createdAtField && isset($this->{$this->createdAtField})) {
            $field = $this->createdAtField;
        }
        else {
            $field = 'created_at';
        }

        return $this->at($format, $field);
    }

    public function updatedAtTZ($format = DateTime::W3C)
    {

        if (isset($this->updatedAtField) && $this->updatedAtField && isset($this->{$this->updatedAtField})) {
            $field = $this->updatedAtField;
        }
        else {
            $field = 'updated_at';
        }

        return $this->at($format, $field);
    }

}
