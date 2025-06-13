<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['code', 'name', 'price', 'stock'];

    public function incomingTransactions()
    {
        return $this->hasMany(IncomingTransaction::class);
    }

    public function outgoingTransactions()
    {
        return $this->hasMany(OutgoingTransaction::class);
    }
}
