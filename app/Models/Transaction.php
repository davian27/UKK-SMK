<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    public function users(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items(){
        return $this->belongsToMany(Item::class, 'transaction_items', 'transaction_id', 'item_id')->withPivot('quantity');
    }

}
