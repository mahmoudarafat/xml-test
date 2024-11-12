<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait Uuid
{
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            try {
                $model->keyType = 'string';
                $model->incrementing = false;

                $model->{$model->getKeyName()} =
                    $model->{$model->getKeyName()} ?: (string) Str::orderedUuid();

            } catch (\Exception $e) {
                abort(500, $e->getMessage());
            }
        });
    }

    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }

}

?>
