<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'scrum_issue_id',
        'user_id',
        'action',
        'description',
    ];

    public function issue()
    {
        return $this->belongsTo(
            ScrumIssue::class,
            'scrum_issue_id'
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}