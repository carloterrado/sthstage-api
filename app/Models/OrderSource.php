<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderSource extends Model
{
    protected $table = 'orderSource';
    protected $guarded = [];
    
    
    public function orderList()
    {
        return $this->belongsTo(OrderList::class);
    }
    
    public function orderReturnSourceDetails()
    {
        return $this->hasOne(OrderReturnSourceDetails::class);
    }
    
    public function orderTracking()
    {
        return $this->hasOne(OrderTracking::class);
    }
    
    public function orderShipping()
    {
        return $this->hasOne(OrderShipping::class);
    }
    
    public function storeLocation()
    {
        return $this->belongsTo(StoreLocation::class);
    }
    
    public function vendorMain()
    {
        return $this->belongsTo(VendorMain::class);
    }
}