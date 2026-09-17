<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueWorkLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'scrum_issue_id',
        'user_id',
        'hours_spent',
        'logged_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'hours_spent' => 'decimal:2',
            'logged_date' => 'date',
        ];
    }

    public function issue()
    {
        return $this->belongsTo(ScrumIssue::class, 'scrum_issue_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
