<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use App\Http\Services\Historyable;
use Spatie\Activitylog\LogOptions;


class ParentModel extends Model
{

    // public function newQuery()
    // {

    //     $table = $this->getTable();

    //     $tableCShop = $table .  '.shop_id';

    //     $route = url('/') . '/' . request()->segment(1);


    //     if (! in_array($route, [route('install'), route('post-install'), url('show-my-contract-print'), url('show-my-rent-bill'), url('show-my-payment-bill'), url('show-my-receipt-print')])) {
    //         if (request()->filled('shop_id')) {
    //             return parent::newQuery()->where($tableCShop, request()->shop_id);
    //         } else {
    //             return parent::newQuery()->where($tableCShop, auth()->user()->shop_id ?? '');
    //         }
    //     } else {
    //         return parent::newQuery();
    //     }
    // }

    protected static function boot()
    {
        parent::boot();
        // Observer
        static::creating(function ($model) {
            if (request()->filled('shop_id')) {
                $model->shop_id = request()->shop_id;
            } else {
                $model->shop_id = auth()->user()->shop_id;
            }
            $model->created_by = auth()->id() ?? '0';
        });
    }
}
