<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sprint extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'goal',
        'start_date',
        'end_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function issues()
    {
        return $this->hasMany(ScrumIssue::class);
    }

    public function getTotalIssuesAttribute(): int
    {
        return $this->issues()->count();
    }

    public function getCompletedIssuesAttribute(): int
    {
        return $this->issues()
            ->where('status', 'done')
            ->count();
    }

    public function getPendingIssuesAttribute(): int
    {
        return $this->issues()
            ->where('status', '!=', 'done')
            ->count();
    }

    public function getCompletionPercentageAttribute(): int
    {
        $total = $this->total_issues;

        if ($total === 0) {
            return 0;
        }

        return (int) round(
            ($this->completed_issues / $total) * 100
        );
    }

    public function getOverdueIssuesAttribute(): int
    {
        return $this->issues()
            ->whereDate('due_date', '<', now())
            ->where('status', '!=', 'done')
            ->count();
    }
}