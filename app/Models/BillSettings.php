<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillSettings extends Model
{
    protected $guarded = ['shop_id'];
    protected $table = 'bill_setting';
    protected $primaryKey = "shop_id";
    public $timestamps = FALSE;
}
