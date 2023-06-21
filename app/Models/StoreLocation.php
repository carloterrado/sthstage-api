<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreLocation extends Model
{
    protected $table = 'store_location';
    public $timestamps = false;
    
    public function orderSource()
    {
        return $this->hasMany(OrderSource::class);
    }
}
