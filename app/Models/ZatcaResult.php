<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZatcaResult extends ParentModel
{
   
protected $guarded = array('id');
    public function safeTransaction()
    {
        return $this->hasMany(SafesTransactions::class , 'safe_transaction_id' , 'id');

}


}
