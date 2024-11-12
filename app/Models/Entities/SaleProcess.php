<?php

namespace App\Models\Entities;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Modules\BackInvioce\Entities\SalePoint;
use Modules\BillSettings\Entities\Salesman;

class SaleProcess extends Model
{
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($saleProcess) {
            // Generate a random access token
            $saleProcess->access_token =Str::random(30);
        });
    }
    public $timestamps =false;
    protected $table = 'sale_process';
    protected $fillable = [];
    protected $appends = ['platform'];
   
    public function salesDetail()
    {
        return $this->hasMany(SaleDetails::class, 'sale_id','id');
    }
   

    public function getSaleDateAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i:s ');
    }

    public function getPlatformAttribute($value)
    {
        return $this->unique_row || $this->local_bill_no ? "<b class='text-danger'>الفاتورة تمت من التطبيق</b>" : '';
    }

    public function checkBackBill()
    {
        $id =$this->id;
        $check = \DB::table('sale_back_invoice')
            ->where('shop_id',session('shop_id'))
            ->where('sale_id', $id)
            ->first();

        if($check){
            return false;
        }else{
            return true;
        }
    }
    public function checkinstallments($bill_id)
    {

        $installments=\DB::table('installments_list')
        ->where('shop_id',session('shop_id'))
        ->where('bill_id', $bill_id)
        ->where('installment_pay',0)
        ->get();

        if(count($installments) > 0 )
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function checkinstallmentsPrint($bill_id)
    {
        $installments=\DB::table('installments_list')
        ->where('shop_id',session('shop_id'))
        ->where('bill_id', $bill_id)
        ->get();
        if(count($installments) > 0 )
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    public function getClient()
    {
        $client = Client::where('shop_id', session('shop_id'))->where('id', $this->client_id)->first();
        if($client){
            return $client->client_name;
        }else{
            return '---';
        }
    }



    public function getItemsQty()
    {
        $qty = SaleDetails::where('shop_id', session('shop_id'))
            ->where('sale_id', $this->id)
            ->sum('quantity');
        return $qty;
    }

    public function getBackItemsQty()
    {
        $back = SaleBackInvoice::where('shop_id', session('shop_id'))
            ->where('sale_id', $this->id)
            ->pluck('id');
        $back_ids = json_decode(json_encode($back));

        $back_qty = SaleBack::where('shop_id', session('shop_id'))
            ->whereIn('back_id', $back_ids)
            ->sum('quantity');
        return $back_qty;
    }

    public function storeName()
    {
        return $this->belongsTo(Store::class, 'store');
    }

    public function itemTransaction()
    {
        return $this->hasMany(ItemTransaction::class, 'sale_id')->whereType(1);
    }


    public function salesMan()
    {
        return $this->belongsTo(Salesman::class, 'sales_man');
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'add_user');
    }


    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    static function salePoint($id)
    {
        return SalePoint::where('id',$id)->first()->point_name;
    }


    public function charge_company()
    {
        return $this->belongsTo(ChargeCompany::class, 'charge_company_id')->withDefault([
            'name' => 'غير محدد'
        ]);
    }


}
