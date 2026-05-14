<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'fields', 'created_by'];
    protected $casts    = ['fields' => 'array'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
