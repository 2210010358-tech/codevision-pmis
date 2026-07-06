<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Project;

class ProjectNotification extends Notification
{
    use Queueable;

    protected $project;
    protected $type; // 'assigned', 'status_updated'

    public function __construct(Project $project, string $type)
    {
        $this->project = $project;
        $this->type = $type;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $messages = [
            'assigned' => "You have been assigned to a new project: '{$this->project->name}'.",
            'status_updated' => "The status of project '{$this->project->name}' has been updated to '{$this->project->status}'.",
        ];

        return [
            'project_id' => $this->project->id,
            'project_name' => $this->project->name,
            'type' => $this->type,
            'message' => $messages[$this->type] ?? "Project update: '{$this->project->name}'",
            'action_url' => route('projects.show', $this->project->id),
        ];
    }
}
