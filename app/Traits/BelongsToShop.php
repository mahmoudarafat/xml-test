<?php

namespace App\Traits;

use App\Models\Scopes\ShopScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BelongsToShop
{
     static function bootBelongsToShop(): void
    {
        static::addGlobalScope('shop',new ShopScope());
        static::creating(function ($model) {
            $shop_id = request('shop_id', auth('web')->user()->shop_id);
            $model->shop_id = $shop_id;
            $model->created_by = auth('web')->id() ?? '0';
        });
    }
}
