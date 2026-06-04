<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Task;

class TaskNotification extends Notification
{
    use Queueable;

    protected $task;
    protected $type; // 'assigned', 'deadline_approaching', 'completed'

    public function __construct(Task $task, string $type)
    {
        $this->task = $task;
        $this->type = $type;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $project = $this->task->milestone->project;
        $developerName = $this->task->developer->name ?? 'N/A';
        $deadlineStr = $this->task->deadline ? $this->task->deadline->format('Y-m-d') : 'N/A';
        
        $messages = [
            'assigned' => "You have been assigned a new task: '{$this->task->name}' in project '{$project->name}'.",
            'deadline_approaching' => "Task '{$this->task->name}' is approaching its deadline ({$deadlineStr}).",
            'completed' => "Task '{$this->task->name}' has been completed by {$developerName}.",
        ];

        return [
            'task_id' => $this->task->id,
            'project_id' => $project->id,
            'task_name' => $this->task->name,
            'project_name' => $project->name,
            'type' => $this->type,
            'message' => $messages[$this->type] ?? "Task update: '{$this->task->name}'",
            'action_url' => route('tasks.show', $this->task->id),
        ];
    }
}
