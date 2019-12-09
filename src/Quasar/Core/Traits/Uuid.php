<?php namespace Quasar\Core\Traits;

use Illuminate\Support\Str;

/**
 * Taken from: https://github.com/kirkbushell/eloquence/blob/master/src/Behaviours/Uuid.php
 */
trait Uuid
{
    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function bootUuid()
    {
        /**
         * Attach to the 'creating' Model Event to provide a UUID
         * for the `id` field (provided by $model->getKeyName())
         */
        static::creating(function ($model) 
        {
            if (empty($model->uuid)) 
            {
                $model->uuid = (string) Str::uuid();
            }
        });
    }
}