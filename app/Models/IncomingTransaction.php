<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomingTransaction extends Model
{
    protected $fillable = ['product_id', 'incoming_discount', 'quantity'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
