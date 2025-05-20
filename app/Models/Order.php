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
        'discount_type',
        'total_amount'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'total_amount' => 'float',
        'discount' => 'float',
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

    /**
     * Calculate the subtotal of the order (sum of all items)
     *
     * @return float
     */
    public function subtotal()
    {
        // Check if items are already loaded to avoid N+1 query issue
        if ($this->relationLoaded('items')) {
            return $this->items->sum('price');
        }

        return $this->items()->sum('price');
    }

    /**
     * Calculate the discount amount based on the discount type
     *
     * @return float
     */
    public function discountAmount()
    {
        $subtotal = $this->subtotal();

        if ($this->discount_type === 'percentage') {
            return ($subtotal * $this->discount) / 100;
        }

        return $this->discount ?? 0;
    }

    /**
     * Calculate the total amount (subtotal - discount)
     *
     * @return float
     */
    public function total()
    {
        return $this->subtotal() - $this->discountAmount();
    }

    /**
     * Get the formatted total amount
     *
     * @return string
     */
    public function formattedTotal()
    {
        return number_format($this->total(), 2);
    }

    /**
     * Calculate the total amount received from payments
     *
     * @return float
     */
    public function receivedAmount()
    {
        // Check if payments are already loaded to avoid N+1 query issue
        if ($this->relationLoaded('payments')) {
            return $this->payments->sum('amount');
        }

        return $this->payments()->sum('amount');
    }

    /**
     * Get the formatted received amount
     *
     * @return string
     */
    public function formattedReceivedAmount()
    {
        return number_format($this->receivedAmount(), 2);
    }

    /**
     * Calculate the change amount (received - total)
     *
     * @return float
     */
    public function change()
    {
        return max(0, $this->receivedAmount() - $this->total());
    }

    /**
     * Check if this order has any items
     *
     * @return bool
     */
    public function hasItems()
    {
        return $this->items()->count() > 0;
    }

    /**
     * Check if the order is fully paid
     *
     * @return bool
     */
    public function isPaid()
    {
        return $this->total() <= $this->receivedAmount();
    }

    /**
     * Get remaining amount to be paid
     * 
     * @return float
     */
    public function remainingAmount()
    {
        $total = $this->total();
        $paid = $this->receivedAmount();
        return max(0, $total - $paid);
    }

    /**
     * Update total_amount before saving the order
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            // Only calculate if total_amount is not already set
            if (empty($order->total_amount)) {
                $order->total_amount = $order->total();
            }
        });

        static::updating(function ($order) {
            // Re-calculate total_amount when order items or discount changes
            if ($order->isDirty(['discount', 'discount_type']) || $order->items()->count() > 0) {
                $order->total_amount = $order->total();
            }
        });
    }
}
