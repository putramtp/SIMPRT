<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = ['task_id', 'user_id', 'description', 'status', 'photo', 'signature_tech', 'signature_cust'];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function teknisi()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
