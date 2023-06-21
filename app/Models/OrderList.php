<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderList extends Model
{
    protected $table = 'orderList';
    protected $guarded = [];
    
    
    public function orderDetail()
    {
        return $this->hasOne(OrderDetail::class);
    }
    
    public function orderSource()
    {
        return $this->hasMany(OrderSource::class);
    }
    
    public function orderReturnDetails()
    {
        return $this->hasOne(OrderReturnDetails::class);
    }
    
    public function orderNotes()
    {
        return $this->hasMany(OrderNotes::class);
    }
    
    public function refunds()
    {
        return $this->hasMany(Refunds::class);
    }
    
    public function orderCancel()
    {
        return $this->hasOne(OrderCancel::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function orderStatus()
    {
        return $this->belongsTo(OrderStatus::class);
    }
}
