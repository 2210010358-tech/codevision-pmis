<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bug extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'priority',
        'attachment',
        'assigned_to',
        'reported_by',
        'status',
        'actual_hours',
        'notes'
    ];

    protected $casts = [
        'actual_hours' => 'decimal:2',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function developer()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
}
