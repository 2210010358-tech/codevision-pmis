<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'client_id',
        'start_date',
        'deadline',
        'description',
        'status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'deadline' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function milestones()
    {
        return $this->hasMany(Milestone::class);
    }

    public function bugs()
    {
        return $this->hasMany(Bug::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function tasks()
    {
        return $this->hasManyThrough(Task::class, Milestone::class);
    }
}
