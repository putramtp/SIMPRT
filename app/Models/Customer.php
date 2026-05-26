<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CustomerUser;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'phone', 'email', 'address'];

    public function portalUser()
    {
        return $this->hasOne(CustomerUser::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
