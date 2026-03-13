<?php

namespace App\Models;

class ProNotificationLog extends BaseModel
{
    protected $table = 'pro_notification_logs';

    protected $fillable = [
        'company_id',
        'task_id',
        'project_id',
        'user_id',
        'type',
        'channel',
        'to',
        'status',
        'content_summary',
        'error_details',
        'sent_at'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
