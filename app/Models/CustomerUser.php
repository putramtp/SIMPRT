<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class CustomerUser extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'customer_users';

    protected $guard = 'customer';

    protected $fillable = [
        'customer_id',
        'name',
        'email',
        'password',
        'signature',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
