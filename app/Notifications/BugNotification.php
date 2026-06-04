<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Bug;

class BugNotification extends Notification
{
    use Queueable;

    protected $bug;
    protected $type; // 'assigned', 'deadline_approaching', 'resolved'

    public function __construct(Bug $bug, string $type)
    {
        $this->bug = $bug;
        $this->type = $type;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $project = $this->bug->project;
        $developerName = $this->bug->developer->name ?? 'N/A';

        $messages = [
            'assigned' => "You have been assigned a new bug: '{$this->bug->title}' in project '{$project->name}'.",
            'deadline_approaching' => "Bug '{$this->bug->title}' is approaching its target fix date.",
            'resolved' => "Bug '{$this->bug->title}' has been resolved by {$developerName}.",
        ];

        return [
            'bug_id' => $this->bug->id,
            'project_id' => $project->id,
            'bug_title' => $this->bug->title,
            'project_name' => $project->name,
            'type' => $this->type,
            'message' => $messages[$this->type] ?? "Bug update: '{$this->bug->title}'",
            'action_url' => route('projects.show', [$project->id, 'tab' => 'bugs']),
        ];
    }
}
