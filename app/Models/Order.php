<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'customer_id',
        'user_id',
        'payment_method',
        'note',
        'discount',
        'discount_type'
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function getCustomerName()
    {
        if ($this->customer) {
            return $this->customer->first_name . ' ' . $this->customer->last_name;
        }
        return __('customer.working');
    }

    public function subtotal()
    {
        return $this->items->map(function ($i) {
            return $i->price;
        })->sum();
    }

    public function discountAmount()
    {
        $subtotal = $this->subtotal();

        if ($this->discount_type === 'percentage') {
            return ($subtotal * $this->discount) / 100;
        }

        return $this->discount ?? 0;
    }

    public function total()
    {
        return $this->subtotal() - $this->discountAmount();
    }

    public function formattedTotal()
    {
        return number_format($this->total(), 2);
    }

    public function receivedAmount()
    {
        return $this->payments->map(function ($i) {
            return $i->amount;
        })->sum();
    }

    public function formattedReceivedAmount()
    {
        return number_format($this->receivedAmount(), 2);
    }

    public function change()
    {
        return max(0, $this->receivedAmount() - $this->total());
    }
}
