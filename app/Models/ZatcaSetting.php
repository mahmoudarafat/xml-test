<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class ZatcaSetting extends ParentModel
{


    use  Uuid;

    public $incrementing = false;

    protected $keyType = 'uuid';

    protected $guarded = ['id'];
    protected $primaryKey = 'id';
}
