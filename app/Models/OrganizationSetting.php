<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class OrganizationSetting extends ParentModel
{
    // protected $table = 'incoming_bill_return';
    protected $table = "badr_shop";
//    protected $appends = ['present_name'];
    protected $guarded = array('id');


//    public function getPreserntNameAttribute()
//    {
//        if (config('app.locale') == 'ar') {
//            return $this->name;
//        } else {
//            return $this->name_en;
//        }
//        return $this->name_ar;
//    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($address) {
            $address->updateAddress();
        });
    }

    private function updateAddress()
    {
        $governorate=$this->attributes['governorate'] ?? '';
        $city = $this->attributes['city'] ?? '' ;
        $street_name = $this->attributes['street_name'] ?? '';
        $plot_identification = $this->attributes['plot_identification'] ??'';
        $building_number = $this->attributes['building_number'] ?? '';
        $this->attributes['address'] =
            $governorate .
            ($city ? '/' . $city : '') .
            ($street_name ? '/' . $street_name : '') .
            ($plot_identification ? '/' . $plot_identification : '') .
            ($building_number ? '/' . $building_number : '');
    }

}
