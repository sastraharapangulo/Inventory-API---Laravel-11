<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutgoingTransaction extends Model
{
    protected $fillable = ['product_id', 'outgoing_discount', 'quantity'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
