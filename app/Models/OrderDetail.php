<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $table = 'orderDetails';

    protected $guarded = [];
    
    
    public function orderList()
    {
        return $this->belongsTo(OrderList::class);
    }
    
    public function orderSource()
    {
        return $this->belongsTo(OrderSource::class);
    }
}
