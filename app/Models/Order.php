<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = "orders";
    protected $fillable = ['order_name', 'first_name', 'last_name', 
                            'email', 'quantity', 'coupon',
                            'total_amount', 'status', ];
}
