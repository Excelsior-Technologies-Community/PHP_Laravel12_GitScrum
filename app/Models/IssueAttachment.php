<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class IssueAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'scrum_issue_id',
        'user_id',
        'filename',
        'original_name',
        'file_path',
        'file_size',
        'mime_type',
    ];

    public function issue()
    {
        return $this->belongsTo(ScrumIssue::class, 'scrum_issue_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type ?? '', 'image/');
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }
}
