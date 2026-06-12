<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'milestone_id',
        'name',
        'description',
        'assigned_to',
        'priority',
        'deadline',
        'estimated_hours',
        'actual_hours',
        'progress_percentage',
        'status',
        'notes',
        'branch_name',
        'commit_hash',
        'commit_url'
    ];

    protected $casts = [
        'deadline' => 'date',
        'estimated_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
        'progress_percentage' => 'integer',
    ];

    public function milestone()
    {
        return $this->belongsTo(Milestone::class);
    }

    public function developer()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function checklists()
    {
        return $this->hasMany(TaskChecklist::class);
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function bugs()
    {
        return $this->hasMany(Bug::class);
    }

    // Accessors for metrics
    public function getTimeVarianceAttribute()
    {
        return $this->actual_hours - $this->estimated_hours;
    }

    public function getProductivityAttribute()
    {
        if ($this->actual_hours <= 0) {
            return 0;
        }
        return ($this->estimated_hours / $this->actual_hours) * 100;
    }
}
