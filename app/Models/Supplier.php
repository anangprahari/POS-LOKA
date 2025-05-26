<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'avatar'
    ];

    /**
     * Get the full avatar URL
     * 
     * @return string
     */
    public function getAvatarUrl()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }

        return asset('img/default-supplier.png');
    }

    // Define relationships here (e.g., with the Product model)
    /**
     * Get the supplier's full name.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
    
}
