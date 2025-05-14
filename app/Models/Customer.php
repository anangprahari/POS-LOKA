<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Customer extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'avatar',
        'user_id',
    ];

    public function getAvatarUrl()
    {
        return Storage::url($this->avatar);
    }

    /**
     * Get the orders for the customer.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Check if customer is a returning customer (more than one order)
     * 
     * @return bool
     */
    public function isReturningCustomer()
    {
        return $this->orders()->count() > 1;
    }

    /**
     * Check if customer is a VIP customer (total purchases exceeding $100,000 or more than 5 orders)
     * 
     * @return bool
     */
    public function isVipCustomer()
    {
        $orderCount = $this->orders()->count();
        $totalSpent = $this->orders()->sum('total_amount');

        return $totalSpent > 100000 || $orderCount > 5;
    }

    /**
     * Get date of last order
     * 
     * @return string|null
     */
    public function getLastOrderDate()
    {
        $lastOrder = $this->orders()->latest()->first();
        return $lastOrder ? $lastOrder->created_at->format('M d, Y') : null;
    }
}
