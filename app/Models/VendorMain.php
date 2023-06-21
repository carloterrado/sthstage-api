<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorMain extends Model
{
    protected $table = 'vendor_main';
    public $timestamps = false;
    
    public function orderSource()
    {
        return $this->hasMany(OrderSource::class);
    }
}
