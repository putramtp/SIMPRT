<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Template;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'customer_id',
        'created_by', 'status', 'due_date', 'priority', 'template_id',
    ];

    protected $casts = ['due_date' => 'date'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignees()
    {
        return $this->belongsToMany(User::class, 'task_user')->withTimestamps();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}
