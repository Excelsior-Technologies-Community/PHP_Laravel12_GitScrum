<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScrumIssue extends Model
{
    use HasFactory;

    protected $fillable = [
        'sprint_id',
        'assigned_to',
        'title',
        'description',
        'status',
        'priority',
        'story_points',
        'estimated_hours',
        'spent_hours',
        'due_date',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'story_points' => 'integer',
            'estimated_hours' => 'decimal:2',
            'spent_hours' => 'decimal:2',
        ];
    }

    public function sprint()
    {
        return $this->belongsTo(Sprint::class);
    }

    public function assignee()
    {
        return $this->belongsTo(
            User::class,
            'assigned_to'
        );
    }

    public function activities()
    {
        return $this->hasMany(
            IssueActivity::class
        )->latest();
    }

    public function workLogs()
    {
        return $this->hasMany(
            IssueWorkLog::class
        )->latest('logged_date');
    }

    public function comments()
    {
        return $this->hasMany(
            IssueComment::class
        )->oldest();
    }

    public function attachments()
    {
        return $this->hasMany(
            IssueAttachment::class
        )->latest();
    }

    public function recalculateSpentHours(): void
    {
        $this->spent_hours = $this->workLogs()->sum('hours_spent');
        $this->saveQuietly();
    }

    public function getTimeProgressPercentageAttribute(): int
    {
        if (!$this->estimated_hours || $this->estimated_hours <= 0) {
            return $this->spent_hours > 0 ? 100 : 0;
        }

        return min(100, (int) round(($this->spent_hours / $this->estimated_hours) * 100));
    }

    public function isOverdue(): bool
    {
        return $this->due_date
            && $this->due_date->isPast()
            && $this->status !== 'done';
    }
}
