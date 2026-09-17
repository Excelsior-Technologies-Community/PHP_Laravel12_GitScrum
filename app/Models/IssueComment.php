<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'scrum_issue_id',
        'user_id',
        'comment',
    ];

    public function issue()
    {
        return $this->belongsTo(ScrumIssue::class, 'scrum_issue_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Formats mentions like @Username into highlighted HTML spans.
     */
    public function getFormattedCommentAttribute(): string
    {
        $comment = e($this->comment);
        return preg_replace('/@([a-zA-Z0-9_]+)/', '<span class="badge bg-primary-subtle text-primary fw-bold">@$1</span>', $comment);
    }
}
